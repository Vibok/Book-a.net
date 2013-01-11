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
	    Base\Library\Advice;

    class Texts {

        public static function process ($action = 'list', $id = null, $filters = array()) {
            

            // gestionar post
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();

                $data = array(
                    'id'      => $_POST['id'],
                    'text_es' => $_POST['text_es'],
                    'text_en' => $_POST['text_en'],
                    'group'   => $_POST['group']
                );

                if (Text::save($data, $errors)) {
//                    Advice::Info('El texto ha sido actualizado');
                    throw new Redirection("/admin/texts");
                } else {
                    Advice::Error(implode('<br />', $errors));
                }
            }

            // valores de filtro
            $groups    = Text::groups();

            switch ($action) {
                case 'list':
                    // metemos el todos
                    \array_unshift($groups, 'Todas las agrupaciones');

                    $list = Text::getAll($filters);
                    foreach ($list as $key=>$item) {
                        $list[$key]->group = $groups[$item->group];
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'list',
                            'groups' => $groups,
                            'list' => $list,
                            'filters' => $filters,
                        )
                    );

                    break;
                case 'edit':
                    $text = Text::getFull($id);


                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'edit',
                            'action' => 'edit',
                            'text' => $text,
                            'groups' => $groups
                        )
                    );

                    break;
                default:
                    throw new Redirection("/admin/texts");
            }
            
        }

    }

}
