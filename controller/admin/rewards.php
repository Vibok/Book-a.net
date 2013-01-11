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
		Base\Library\Advice,
		Base\Library\Feed,
        Base\Model\Booka\Reward\Type;

    class Rewards {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $type = new Type(array(
                    'id' => $_POST['id'],
                    'name_es' => $_POST['name_es'],
                    'name_en' => $_POST['name_en'],
                    'description_es' => $_POST['description_es'],
                    'description_en' => $_POST['description_en'],
                    'order' => $_POST['order']
                ));

				if ($type->save($errors)) {
                    /*
                    switch ($_POST['action']) {
                        case 'add':
                            Advice::Info('Tipo añadido correctamente');
                            break;
                        case 'edit':
                            Advice::Info('Tipo editado correctamente');
                            break;
                    }
                     */
                    throw new Redirection("/admin/rewards");
				} else {
                    Advice::Error(implode('<br />', $errors));

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'rewards',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'item' => $type
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Type::up($id);
                    throw new Redirection("/admin/rewards");
                    break;
                case 'down':
                    Type::down($id);
                    throw new Redirection("/admin/rewards");
                    break;
                case 'add':
                    $next = Type::next();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'rewards',
                            'file' => 'edit',
                            'action' => 'add',
                            'item' => (object) array('order' => $next)
                        )
                    );
                    break;
                case 'edit':
                    $type = Type::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'rewards',
                            'file' => 'edit',
                            'action' => 'edit',
                            'item' => $type
                        )
                    );
                    break;
                case 'remove':
                    Type::delete($id);
                    throw new Redirection("/admin/rewards");
                    break;
            }

            $types = Type::getList();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'rewards',
                    'file' => 'list',
                    'items' => $types
                )
            );
            
        }

    }

}
