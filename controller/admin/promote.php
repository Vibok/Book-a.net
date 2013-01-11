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

    class Promote {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $promo = new Model\Promote(array(
                    'id' => $id,
                    'booka' => $_POST['booka'],
                    'order' => $_POST['order'],
                    'active' => $_POST['active']
                ));

				if ($promo->save($errors)) {
                    /*
                    switch ($_POST['action']) {
                        case 'add':
                            Advice::Info('Proyecto destacado correctamente');
                            break;
                        case 'edit':
                            Advice::Info('Destacado actualizado correctamente');
                            break;
                    }
                    */
                    throw new Redirection('/admin/promote');
				}
				else {

                    Advice::Error(implode(', ', $errors));

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'promo' => $promo
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Promote::setActive($id, $set);
                    throw new Redirection('/admin/promote');
                    break;
                case 'up':
                    Model\Promote::up($id);
                    throw new Redirection('/admin/promote');
                    break;
                case 'down':
                    Model\Promote::down($id);
                    throw new Redirection('/admin/promote');
                    break;
                case 'remove':
                    if (Model\Promote::delete($id)) {
                        Advice::Info('Destacado quitado correctamente');
                    } else {
                        Advice::Error('No se ha podido quitar el destacado');
                    }
                    throw new Redirection('/admin/promote');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Promote::next();
                    // Bookas disponibles
                    $bookas = Model\Promote::available();
                    $status = Model\Booka::status();

                    if (empty($bookas)) {
                        Advice::Error('No hay Bookas para destacar');
                        throw new Redirection('/admin/promote');
                    }


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'bookas' => $bookas,
                            'status' => $status
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Promote::get($id);
                    // Bookas disponibles
                    // si tenemos ya proyecto seleccionado lo incluimos
                    $bookas = Model\Promote::available($promo->booka);
                    $status = Model\Booka::status();


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo,
                            'bookas' => $bookas,
                            'status' => $status
                        )
                    );
                    break;
            }


            $promoted = Model\Promote::getAll(false);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'promote',
                    'file' => 'list',
                    'promoted' => $promoted
                )
            );
            
        }

    }

}
