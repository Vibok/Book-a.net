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
        Base\Model,
        Base\Library\Advice,
        Base\Core\Redirection,
        Base\Core\Error;

    class Categories {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'add':
                    $order = Model\Category::next();
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'categories',
                            'file' => 'edit',
                            'action' => 'add',
                            'category' => (object) array('order'=>$order),
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        // instancia
                        $item = new Model\Category(array(
                            'id' => $_POST['id'],
                            'name_es' => $_POST['name_es'],
                            'name_en' => $_POST['name_en'],
                            'order' => $_POST['order']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection('/admin/categories');
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    } else {
                        $category = Model\Category::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'categories',
                            'file' => 'edit',
                            'action' => 'edit',
                            'category' => $category,
                        )
                    );

                    break;
                case 'up':
                    Model\Category::up($id);
                    throw new Redirection('/admin/categories');
                    break;
                case 'down':
                    Model\Category::down($id);
                    throw new Redirection('/admin/categories');
                    break;
                case 'remove':
                    Model\Category::delete($id);
                    throw new Redirection('/admin/categories');
                    break;
            }

            $categories = Model\Category::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'categories',
                    'file' => 'list',
                    'categories' => $categories
                )
            );
            
        }

    }

}
