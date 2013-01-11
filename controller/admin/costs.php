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
        Base\Model;

    class Costs {

        public static function process ($action = 'list', $id = null) {

            $model = 'Base\Model\Booka\Cost\Type';
            $url = '/admin/costs';

            $campos = array (
                        'id' => array(
                            'label' => '',
                            'name' => 'id',
                            'type' => 'hidden'

                        ),
                        'name_es' => array(
                            'label' => 'Tipo',
                            'name' => 'name_es',
                            'type' => 'text'
                        ),
                        'name_en' => array(
                            'label' => 'Tipo (inglés)',
                            'name' => 'name_en',
                            'type' => 'text'
                        ),
                        'description_es' => array(
                            'label' => 'Descripción',
                            'name' => 'description_es',
                            'type' => 'text'
                        ),
                        'description_en' => array(
                            'label' => 'Descripción (inglés)',
                            'name' => 'description',
                            'type' => 'text'
                        )
                    );
            
            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Añadir'
                                ),
                                'fields' => $campos
                            )
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        // instancia
                        $item = new $model(array(
                            'name_es' => $_POST['name_es'],
                            'name_en' => $_POST['name_en'],
                            'description_es' => $_POST['description_es'],
                            'description_en' => $_POST['description_en']
                        ));

                        if ($item->save($errors)) {
//                            Advice::Info('El tipo de coste ha sido actualizado');
                            throw new Redirection($url);
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => Text::get('regular-save')
                                ),
                                'fields' => $campos
                            )
                        )
                    );

                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'addbutton' => 'Nuevo',
                    'data' => $model::getList(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Tipo',
                        'used' => 'Registros',
                        'remove' => ''
                    ),
                    'url' => "$url"
                )
            );
            
        }

    }

}
