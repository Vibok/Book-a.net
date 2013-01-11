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

    class Collections {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'bookas':
                    // bookas de la colección
                    if (isset($_SESSION['user']->roles['director']) && $_SESSION['user']->collection != $id) {
                        Advice::Info('No eres director de esta colecci&oacute;n, no podr&aacute;s previsualizar los bookas que no estén publicados');
                    }

                    $list = Model\Booka::getList(array('collection'=>$id));
                    $status = Model\Booka::status();
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'collections',
                            'file' => 'bookas',
                            'status' => $status,
                            'list' => $list
                        )
                    );

                    break;
                case 'add':
                    $order = Model\Collection::next();
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'collections',
                            'file' => 'edit',
                            'action' => 'add',
                            'collection' => (object) array('order'=>$order),
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        // tratamos la imagen
                        if(!empty($_FILES['new-image']['name'])) {
                            $theimage = new Model\Image($_FILES['new-image']);
                            if ($theimage->save()) {
                                $image = $theimage->id;
                            } else {
                                Advice::Error('Ha fallado al grabar la imagen');
                                $image = $_POST['image'];
                            }
                        } elseif (!empty($_POST['image-remove'])) {
                            $theimage = Model\Image::get($_POST['image']);
                            $theimage->remove();
                            $image = '';
                        } else {
                            $image = $_POST['image'];
                        }
                        
                        // instancia
                        $item = new Model\Collection(array(
                            'id' => $_POST['id'],
                            'name_es' => $_POST['name_es'],
                            'name_en' => $_POST['name_en'],
                            'keywords_es' => $_POST['keywords_es'],
                            'keywords_en' => $_POST['keywords_en'],
                            'description_es' => $_POST['description_es'],
                            'description_en' => $_POST['description_en'],
                            'text_es' => $_POST['text_es'],
                            'text_en' => $_POST['text_en'],
                            'director' => $_POST['director'],
                            'image' => $image,
                            'color' => $_POST['color'],
                            'order' => $_POST['order']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection('/admin/collections');
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    } else {
                        $collection = Model\Collection::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'collections',
                            'file' => 'edit',
                            'action' => 'edit',
                            'collection' => $collection,
                        )
                    );

                    break;
                case 'up':
                    Model\Collection::up($id);
                    throw new Redirection('/admin/collections');
                    break;
                case 'down':
                    Model\Collection::down($id);
                    throw new Redirection('/admin/collections');
                    break;
                case 'remove':
                    Model\Collection::delete($id);
                    throw new Redirection('/admin/collections');
                    break;
            }

            $collections = Model\Collection::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'collections',
                    'file' => 'list',
                    'collections' => $collections
                )
            );
            
        }

    }

}
