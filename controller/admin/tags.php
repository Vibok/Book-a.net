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
        Base\Model\Post\Tag,
        Base\Library\Advice,
        Base\Core\Redirection,
        Base\Core\Error;

    class Tags {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'tags',
                            'file' => 'edit',
                            'action' => 'add',
                            'tag' => (object) array(),
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        // instancia
                        $item = new Tag(array(
                            'id' => $_POST['id'],
                            'name_es' => $_POST['name_es'],
                            'name_en' => $_POST['name_en']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection('/admin/tags');
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    } else {
                        $tag = Tag::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'tags',
                            'file' => 'edit',
                            'action' => 'edit',
                            'tag' => $tag,
                        )
                    );

                    break;
                case 'remove':
                    Tag::delete($id);
                    throw new Redirection('/admin/tags');
                    break;
            }

            $tags = Tag::getList();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'tags',
                    'file' => 'list',
                    'tags' => $tags
                )
            );
            
        }

    }

}
