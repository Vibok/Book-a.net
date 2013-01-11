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

    class Footer {

        public static function process ($action = 'list', $id = null, $filters = array()) {
echo trace($filters);
            $filters['nonews'] = true;
            $columns = Model\Footer::getList();
            unset($columns['news']);

            if (!isset($columns[$filters['column']])) {
                unset($filters['column']);
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $footer = new Model\Footer(array(
                    'id' => $_POST['id'],
                    'column' => $_POST['column'],
                    'title_es' => $_POST['title_es'],
                    'title_en' => $_POST['title_en'],
                    'url' => $_POST['url'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
                ));

				if ($footer->save($errors)) {
                    /*
                    switch ($_POST['action']) {
                        case 'add':
                            Advice::Info('Pregunta añadida correctamente');
                            break;
                        case 'edit':
                            Advice::Info('Pregunta editada correctamente');
                            break;
                    }
                     */
				} else {
                    Advice::Error(implode('<br />', $errors));

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'footer' => $footer,
                            'filter' => $filter,
                            'columns' => $columns
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Footer::up($id);
                    throw new Redirection("/admin/footer");
                    break;
                case 'down':
                    Model\Footer::down($id);
                    throw new Redirection("/admin/footer");
                    break;
                case 'add':
                    $next = Model\Footer::next($filters['column']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'edit',
                            'action' => 'add',
                            'footer' => (object) array('column' => $filters['column'], 'order' => $next, 'cuantos' => $next),
                            'columns' => $columns
                        )
                    );
                    break;
                case 'edit':
                    $footer = Model\Footer::get($id);

                    $cuantos = Model\Footer::next($footer->column);
                    $footer->cuantos = ($cuantos -1);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'edit',
                            'action' => 'edit',
                            'footer' => $footer,
                            'columns' => $columns
                        )
                    );
                    break;
                case 'remove':
                    Model\Footer::delete($id);
                    throw new Redirection("/admin/footer");
                    break;
            }

            $footers = Model\Footer::getAll($filters);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'footer',
                    'file' => 'list',
                    'footers' => $footers,
                    'columns' => $columns,
                    'filters' => $filters
                )
            );
            
        }

    }

}
