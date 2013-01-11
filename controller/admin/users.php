<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */

namespace Base\Controller\Admin {

    use Base\Core\View,
        Base\Core\Redirection,
        Base\Core\Error,
		Base\Library\Text,
		Base\Library\Template,
        Base\Library\Advice,
        Base\Model;

    class Users {

        public static function process ($action = 'list', $id = null, $filters = array(), $subaction = '') {

            $errors = array();

            switch ($action)  {
                case 'add':

                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        $user = new Model\User();
                        $user->userid = $_POST['userid'];
                        $user->name = $_POST['name'];
                        $user->email = $_POST['email'];
                        $user->password = $_POST['password'];
                        $user->save($errors);

                        if(empty($errors)) {
                          // mensaje de ok y volvemos a la lista de usuarios
//                          Advice::Info(Text::get('user-register-success'));
                          throw new Redirection('/admin/users/manage/'.$user->id);
                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                            Advice::Error(implode('<br />', $errors));
                        }
                    }

                    // vista de crear usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'add',
                            'data'=>$data
                        )
                    );

                    break;
                case 'level':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_numeric($_POST['level'])) {

                        $user = Model\User::get($id);
                        if ($user->setLevel($_POST['level'])) {

                        } else {
                            Advice::Error('No se ha guardado bien, intentarlo de nuevo.');
                        }
                    }
                    throw new Redirection('/admin/users/manage/'.$id);


                    break;
                case 'edit':

                    $user = Model\User::get($id);

                    // si llega post: actualizamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $tocado = array();
                        // para crear se usa el mismo método save del modelo, hay que montar el objeto
                        if (!empty($_POST['email'])) {
                            $user->email = $_POST['email'];
                            $tocado[] = 'el email';
                        }
                        if (!empty($_POST['password'])) {
                            $user->password = $_POST['password'];
                            $tocado[] = 'la contraseña';
                        }

                        if(!empty($tocado) && $user->update($errors)) {

                            // mensaje de ok y volvemos a la lista de usuarios
//                            Advice::Info('Datos actualizados');
                            throw new Redirection('/admin/users');

                        } else {
                            // si hay algun error volvemos a poner los datos en el formulario
                            $data = $_POST;
                            Advice::Error(implode('<br />', $errors));
                        }
                    }

                    // vista de editar usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'edit',
                            'user'=>$user,
                            'data'=>$data
                        )
                    );

                    break;
                case 'manage':

                    // si llega post: ejecutamos + mensaje + seguimos editando
                    $sql = '';
                    if (!empty($subaction)) {
                        switch ($subaction)  {
                            case 'ban':
                                $sql = "UPDATE user SET active = 0 WHERE id = :user";
                                $log_action = 'desactivado';
                                break;
                            case 'unban':
                                $sql = "UPDATE user SET active = 1 WHERE id = :user";
                                $log_action = 'activado';
                                break;
                            case 'show':
                                $sql = "UPDATE user SET hide = 0 WHERE id = :user";
                                $log_action = 'mostrado';
                                break;
                            case 'hide':
                                $sql = "UPDATE user SET hide = 1 WHERE id = :user";
                                $log_action = 'ocultado';
                                break;
                            default:

                                $roles = Model\User::getRolesList();

                                // poner o quitar rol
                                if (substr($subaction, 0, 2) == 'no') {
                                    // quita lo que sea despues de no
                                    $rol = substr($subaction, 2);
                                    if (!isset($roles[$rol])) break;
                                    $sql = "DELETE FROM user_role WHERE role_id = '{$rol}' AND user_id = :user";
                                    $log_action = 'quitado el rol de ' . $roles[$rol];
                                } else {
                                    // pone lo que sea
                                    $rol = $subaction;
                                    if (!isset($roles[$rol])) break;
                                    $sql = "REPLACE INTO user_role (user_id, role_id) VALUES (:user, '{$rol}')";
                                    $log_action = 'dado el rol de ' . $roles[$rol];
                                }
                        }
                    }


                    if (!empty($sql)) {

                        $user = Model\User::getMini($id);

                        if (Model\User::query($sql, array(':user'=>$id))) {

                            // mensaje de ok y volvemos a la gestion del usuario
//                            Advice::Info('Se ha <strong>' . $log_action . '</strong> al usuario <strong>'.$user->name.'</strong> CORRECTAMENTE');
//                            $log_text = 'El admin %s ha %s al usuario %s';
                        } else {

                            // mensaje de error y volvemos a la gestion del usuario
                            Advice::Error('Ha FALLADO cuando se ha <strong>' . $log_action . '</strong> al usuario <strong>'.$id.'</strong>');
//                            $log_text = 'Al admin %s le ha <strong>FALLADO</strong> cuando ha %s al usuario %s';

                        }

                        throw new Redirection('/admin/users/manage/'.$id);
                    }

                    $user = Model\User::get($id);

                    $viewData = array(
                            'folder' => 'users',
                            'file' => 'manage',
                            'user'=>$user
                        );
                    
                    if (isset($user->roles['director'])) {
                        $viewData['collections'] = Model\Collection::getList();
                    }

                    // vista de gestión de usuario
                    return new View('view/admin/index.html.php', $viewData);


                    break;
                case 'impersonate':

                    $user = Model\User::get($id);

                    // vista de acceso a suplantación de usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file'   => 'impersonate',
                            'user'   => $user
                        )
                    );

                    break;

                case 'collection':

                    // si llega post: creamos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['collection'])) {

                        if(empty($_POST['collection'])) {
                            $sql = "DELETE FROM user_collection WHERE user = :user";
                            if (Model\User::query($sql, array(':user'=>$id))) {
//                                Advice::Info('Este director ya no controla ninguna colección');
                            } else {
                                Advice::Error('Ha fallado al quitar la colección');
                            }
                        } else {
                            $sql = "REPLACE INTO user_collection (user, collection) VALUES (:user, :collection)";
                            if (Model\User::query($sql, array(':user'=>$id, ':collection'=>$_POST['collection']))) {
//                                Advice::Info('Se ha cambiado la coleccion');
                            } else {
                                Advice::Error('Ha fallado al cambiar la colección');
                            }
                        }
                        
                        throw new Redirection('/admin/users/manage/'.$id);
                    }

                    // vista de crear usuario
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'add',
                            'data'=>$data
                        )
                    );

                    break;
                    
                    
                case 'list':
                default:
                    if (!empty($filters['filtered'])) {
                        $users = Model\User::getAll($filters);
                    } else {
                        $users = array();
                    }
                    $status = array(
                                'active' => 'Activo',
                                'inactive' => 'Inactivo'
                            );
                    $roles = array(
                        'admin' => 'Admin',
                        'director' => 'Director',
                        'vip-blog' => 'Colabora Blog',
                        'vip-booka' => 'Colabora Booka',
                        'user' => 'Solo usuario'
                    );
                    $types = array(
                        'creators' => 'Impulsores', // que tienen algun proyecto en campaña, financiado, archivado o caso de éxito
                        'investors' => 'Cofinanciadores', // que han aportado a algun proyecto en campaña, financiado, archivado o caso de éxito
                        'supporters' => 'Colaboradores' // que han enviado algun mensaje en respuesta a un mensaje de colaboración
                    );
                    $orders = array(
                        'created' => 'Fecha de alta',
                        'name' => 'Alias',
                        'id' => 'User',
                        'amount' => 'Cantidad',
                        'bookas' => 'Bookas'
                    );
                    // Bookas con aportes válidos
                    $bookas = Model\Invest::bookas(true);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'users',
                            'file' => 'list',
                            'users'=>$users,
                            'filters' => $filters,
                            'name' => $name,
                            'status' => $status,
                            'roles' => $roles,
                            'types' => $types,
                            'bookas' => $bookas,
                            'orders' => $orders
                        )
                    );
                break;
            }
            
        }

    }

}
