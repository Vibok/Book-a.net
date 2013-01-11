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
		Base\Library\Advice,
        Base\Library\Template,
        Base\Library\Mail,
        Base\Library\Newsletter,
        Base\Library\Lang,
        Base\Model;

    class Mailing {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            // Valores de filtro
            $langs = Lang::getAll(true);
            $status = Model\Booka::status();
            $methods = Model\Invest::methods();
            $types = array(
                'investor' => 'Cofinanciadores',
                'owner' => 'Autores',
                'user' => 'Usuarios'
            );
            $roles = array(
                'admin' => 'Administrador',
                'checker' => 'Revisor'
            );

            // una variable de sesion para mantener los datos de todo esto
            if (!isset($_SESSION['mailing'])) {
                $_SESSION['mailing'] = array();
            }

            switch ($action) {
                case 'edit':

                    $_SESSION['mailing']['receivers'] = array();

                    $values = array();
                    $sqlFields  = '';
                    $sqlInner  = '';
                    $sqlFilter = '';


                    // cargamos los destiantarios
                    //----------------------------
                    // por tipo de usuario
                    switch ($filters['type']) {
                        case 'investor':
                            $sqlInner .= "INNER JOIN invest
                                    ON invest.user = user.id
                                    AND (invest.status = 0 OR invest.status = 1 OR invest.status = 3 OR invest.status = 4)
                                INNER JOIN booka
                                    ON booka.id = invest.booka
                                    ";
                            $sqlFields .= ", booka.name_es as booka";
                            $sqlFields .= ", booka.id as bookId";
                            break;
                        case 'owner':
                            $sqlInner .= "INNER JOIN booka
                                    ON booka.owner = user.id
                                    ";
                            $sqlFields .= ", booka.name_es as booka";
                            $sqlFields .= ", booka.id as bookId";
                            break;
                        default :
                            break;
                    }
                    $_SESSION['mailing']['filters_txt'] = 'los <strong>' . $types[$filters['type']] . '</strong> ';

                    if (!empty($filters['booka']) && !empty($sqlInner)) {
                        $sqlFilter .= " AND booka.name_es LIKE (:booka) ";
                        $values[':booka'] = '%'.$filters['booka'].'%';
                        $_SESSION['mailing']['filters_txt'] .= 'de Bookas que su nombre contenga <strong>\'' . $filters['booka'] . '\'</strong> ';
                    } elseif (empty($filters['booka']) && !empty($sqlInner)) {
                        $_SESSION['mailing']['filters_txt'] .= 'de cualquier Booka ';
                    }

                    if (isset($filters['status']) && $filters['status'] > -1 && !empty($sqlInner)) {
                        $sqlFilter .= "AND booka.status = :status ";
                        $values[':status'] = $filters['status'];
                        $_SESSION['mailing']['filters_txt'] .= 'en estado <strong>' . $status[$filters['status']] . '</strong> ';
                    } elseif ($filters['status'] < 0 && !empty($sqlInner)) {
                        $_SESSION['mailing']['filters_txt'] .= 'en cualquier estado ';
                    }

                    if ($filters['type'] == 'investor') {
                        if (!empty($filters['method']) && !empty($sqlInner)) {
                            $sqlFilter .= "AND invest.method = :method ";
                            $values[':method'] = $filters['method'];
                            $_SESSION['mailing']['filters_txt'] .= 'mediante <strong>' . $methods[$filters['method']] . '</strong> ';
                        } elseif (empty($filters['method']) && !empty($sqlInner)) {
                            $_SESSION['mailing']['filters_txt'] .= 'mediante cualquier metodo ';
                        }
                    }

                    if (!empty($filters['role'])) {
                        $sqlInner .= "INNER JOIN user_role
                                ON user_role.user_id = user.id
                                AND user_role.role_id = :role
                                ";
                        $values[':role'] = $filters['role'];
                        $_SESSION['mailing']['filters_txt'] .= 'que sean <strong>' . $roles[$filters['role']] . '</strong> ';
                    }

                    if (!empty($filters['name'])) {
                        $sqlFilter .= " AND ( user.name LIKE (:name) OR user.email LIKE (:name) ) ";
                        $values[':name'] = '%'.$filters['name'].'%';
                        $_SESSION['mailing']['filters_txt'] .= 'que su nombre o email contenga <strong>\'' . $filters['name'] . '\'</strong> ';
                    }

                    if (!empty($filters['lang'])) {
                        $sqlFilter .= " AND user.lang = :lang";
                        $values[':lang'] = $filters['lang'];
                        $_SESSION['mailing']['filters_txt'] .= 'que su idioma habitual sea <strong>\'' . strtoupper($filters['lang']) . '\'</strong> ';
                    }

                    $sql = "SELECT
                                user.id as id,
                                user.name as name,
                                user.email as email,
                                user.lang as lang
                                $sqlFields
                            FROM user
                            $sqlInner
                            WHERE user.id != 'root'
                            AND user.active = 1
                            $sqlFilter
                            GROUP BY user.id
                            ORDER BY user.name ASC
                            ";

//                        echo '<pre>'.$sql . '<br />'.print_r($values, 1).'</pre>';

                    if ($query = Model\User::query($sql, $values)) {
                        foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                            $_SESSION['mailing']['receivers'][$receiver->id] = $receiver;
                        }
                    } else {
                        Advice::Error('Fallo el SQL!!!!! <br />' . $sql . '<pre>'.print_r($values, 1).'</pre>');
                    }

                    // si no hay destinatarios, salta a la lista con mensaje de error
                    if (empty($_SESSION['mailing']['receivers'])) {
                        Advice::Error('No se han encontrado destinatarios para ' . $_SESSION['mailing']['filters_txt']);

                        throw new Redirection('/admin/mailing/list');
                    }

                    // si hay, mostramos el formulario de envio
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder'    => 'mailing',
                            'file'      => 'edit',
                            'filters'   => $filters,
                            'status'    => $status,
                            'types'     => $types,
                            'roles'     => $roles
                        )
                    );

                    break;
                case 'send':
                    $tini = \microtime();
                    // Enviando contenido recibido a destinatarios recibidos
                    $users = array();

//                        $content = nl2br($_POST['content']);
                    $content = $_POST['content'];
                    $subject = $_POST['subject'];
                    $templateId = !empty($_POST['template']) ? $_POST['template'] : 11;


                    // Contenido para newsletter
                    if ($templateId == 33) {
                        $_SESSION['NEWSLETTER_SENDID'] = '';
                        $tmpcontent = \Base\Library\Newsletter::getContent($content);
                    }

                    // ahora, envio, el contenido a cada usuario
                    foreach ($_SESSION['mailing']['receivers'] as $usr=>$userData) {
                        $users[] = $usr;
                        if (!isset($_POST[$usr])) {
                            $campo = 'receiver_'.str_replace('.', '_', $usr);
                            if (!isset($_POST[$campo])) {
                                continue;
                            }
                        }

                        // si es newsletter
                        if ($templateId == 33) {
                            // Mirar que no tenga bloqueadas las preferencias
                            if (Model\User::mailBlock($usr)) {
                                Advice::Error($usr . ' lo tiene bloqueado');
                                continue;
                            }

                            // el sontenido es el mismo para todos, no lleva variables
                        } else {
                            $tmpcontent = \str_replace(
                                array('%USERID%', '%USEREMAIL%', '%USERNAME%', '%SITEURL%', '%PROJECTID%', '%PROJECTNAME%', '%PROJECTURL%'),
                                array(
                                    $usr,
                                    $userData->email,
                                    $userData->name,
                                    SITE_URL,
                                    $userData->bookId,
                                    $userData->booka,
                                    SITE_URL.'/booka/'.$userData->bookId
                                ),
                                $content);
                        }

                        $mailHandler = new Mail();

                        $mailHandler->to = $userData->email;
                        $mailHandler->toName = $userData->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = '<br />'.$tmpcontent.'<br />';
                        $mailHandler->html = true;
                        $mailHandler->template = $templateId;
                        if ($mailHandler->send($errors)) {
                            $_SESSION['mailing']['receivers'][$usr]->ok = true;
                        } else {
                            Advice::Error(implode('<br />', $errors));
                            $_SESSION['mailing']['receivers'][$usr]->ok = false;
                        }

                        unset($mailHandler);
                    }

                    $tend = \microtime();
                    $time = $tend - $tini;

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder'    => 'mailing',
                            'file'      => 'send',
                            'content'   => $content,
//                                'bookas'  => $bookas,
                            'status'    => $status,
                            'methods'   => $methods,
                            'types'     => $types,
                            'roles'     => $roles,
                            'users'     => $users,
                            'time'      => $time
                        )
                    );

                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder'    => 'mailing',
                    'file'      => 'list',
//                    'bookas'  => $bookas,
                    'status'    => $status,
                    'methods'   => $methods,
                    'types'     => $types,
                    'roles'     => $roles,
                    'filters'   => $filters
                )
            );
            
        }

    }

}
