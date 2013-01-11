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
        Base\Model;

    class Invests {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // métodos de pago
            $methods = Model\Invest::methods();
            // estados del proyecto
            $status = Model\Booka::status();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de Bookas
            $bookas = Model\Invest::bookas(true);
            // extras
            $types = array(
                'donative' => 'Solo los donativos',
                'anonymous' => 'Solo los anónimos',
                'manual' => 'Solo los manuales'
            );


            // detalles del aporte
            if ($action == 'details') {

                $invest = Model\Invest::get($id);
                $booka = Model\Booka::get($invest->booka);
                $userData = Model\User::get($invest->user);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'details',
                        'invest' => $invest,
                        'booka' => $booka,
                        'user' => $userData,
                        'status' => $status,
                        'investStatus' => $investStatus
                    )
                );
            }
            // edicion
            if ($action == 'edit' && !empty($id)) {

                $invest = Model\Invest::get($id);
                $bookaData = Model\Booka::get($invest->booka);
                $userData = Model\User::getMini($invest->user);
                $status = Model\Booka::status();

                // si tratando post
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                    $errors = array();

                    $invest->amount = $_POST['amount'];
                    
                    // las recompensas
                    $chosen = $_POST['selected_rewards'];
                    if (empty($chosen)) {
                        // renuncia a las recompensas, bien por el/ella!
                        $invest->resign = true;
                        $invest->rewards = array();
                    } else {
                        $invest->resign = false;
                        $invest->rewards = $chosen;
                    }


                    // dirección de envio para la recompensa
                    // y datos fiscales por si fuera donativo
                    $invest->address = (object) array(
                        'name'     => $_POST['name'],
                        'nif'      => $_POST['nif'],
                        'address'  => $_POST['address'],
                        'zipcode'  => $_POST['zipcode'],
                        'location' => $_POST['location'],
                        'city'     => $_POST['city'],
                        'country'  => $_POST['country']
                    );

                    
                    if ($invest->update($errors)) {
//                        Advice::Info('Se han actualizado los datos del aporte: recompensa y dirección');
                        throw new Redirection('/admin/invests');
                    } else {
                        Advice::Error('No se han actualizado correctamente los datos del aporte. ERROR: '.implode(', ', $errors));
                    }

                }

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'edit',
                        'invest'   => $invest,
                        'booka'  => $bookaData,
                        'user'  => $userData,
                        'status'   => $status
                    )
                );

            }
            
            // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
           if ($action == 'add') {

                // listado de proyectos existentes
                $bookas = Model\Invest::bookas(true);
                // usuarios
                $users = Model\User::getAllMini();

                // generar aporte manual
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add']) ) {

                    $userData = Model\User::getMini($_POST['user']);
                    $bookaData = Model\Booka::getMini($_POST['booka']);

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $_POST['amount'],
                            'user'      => $userData->id,
                            'booka'   => $bookaData->id,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign'    => 1,
                            'admin'     => $_SESSION['user']->id
                        )
                    );

                    if ($invest->save($errors)) {
                        Model\Invest::setDetail($invest->id, 'admin-created', 'Este aporte ha sido creado manualmente por el admin ' . $_SESSION['user']->name);
//                        Advice::Info('Aporte manual creado correctamente, seleccionar recompensa y dirección de entrega.');
                        throw new Redirection('/admin/invests/edit/'.$invest->id);
                    } else{
                        $errors[] = 'Ha fallado algo al crear el aporte manual';
                    }

                }

                 $viewData = array(
                        'folder' => 'invests',
                        'file' => 'add',
                        'users' => $users,
                        'bookas' => $bookas
                    );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia

           }


            // listado de aportes
            if ($filters['filtered'] == 'yes') {
                $list = Model\Invest::getList($filters);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'invests',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'bookas'        => $bookas,
                    'methods'       => $methods,
                    'types'         => $types,
                    'investStatus'  => $investStatus
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
