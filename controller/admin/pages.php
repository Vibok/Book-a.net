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
		Base\Library\Page;

    class Pages {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'up':
                    Page::up($id);
                    throw new Redirection("/admin/pages");
                    break;
                case 'down':
                    Page::down($id);
                    throw new Redirection("/admin/pages");
                    break;
                case 'add':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page = new Page();
                        
                        $page->id         = $_POST['id'];
                        $page->name       = $_POST['name'];
                        $page->order      = 0;
                        
                        if ($page->add($errors)) {

//                            Advice::Info('La página <strong>'.$page->name. '</strong> se ha creado correctamente, se puede editar ahora.');

                            throw new Redirection("/admin/pages/edit/{$page->id}");
                        } else {
                            Advice::Error(implode('<br />', $errors));
                            throw new Redirection("/admin/pages/add");
                        }
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'add'
                        )
                     );
                    break;

                case 'edit':
                    // si estamos editando una página
                    $page = Page::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page->text_es    = $_POST['text_es'];
                        $page->text_en    = $_POST['text_en'];
                        $page->content_es = $_POST['content_es'];
                        $page->content_en = $_POST['content_en'];
                        if ($page->save($errors)) {
//                            Advice::Info('La página '.$page->name. ' se ha actualizado correctamente');

                            throw new Redirection("/admin/pages");
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'edit',
                            'page' => $page
                        )
                     );
                    break;

                case 'list':
                    $pages = Page::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'list',
                            'pages' => $pages
                        )
                    );
                    break;
            }

        }

    }

}
