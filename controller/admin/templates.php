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
        Base\Library\Template;

    class Templates {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una plantilla
                    $template = Template::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $template->title_es = $_POST['title_es'];
                        $template->title_en = $_POST['title_en'];
                        $template->text_es  = $_POST['text_es'];
                        $template->text_en  = $_POST['text_en'];
                        if ($template->save($errors)) {
//                            Advice::Info('La plantilla se ha actualizado correctamente');
                            throw new Redirection("/admin/templates");
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'edit',
                            'template' => $template
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $templates = Template::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'list',
                            'templates' => $templates
                        )
                    );
                    break;
            }

        }

    }

}
