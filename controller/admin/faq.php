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

    class Faq {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $sections = Model\Faq::sections();

            if (!isset($sections[$filters['section']])) {
                unset($filters['section']);
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $faq = new Model\Faq(array(
                    'id' => $_POST['id'],
                    'section' => $_POST['section'],
                    'title_es' => $_POST['title_es'],
                    'title_en' => $_POST['title_en'],
                    'description_es' => $_POST['description_es'],
                    'description_en' => $_POST['description_en'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
                ));

				if ($faq->save($errors)) {
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
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Faq::up($id);
                    throw new Redirection("/admin/faq");
                    break;
                case 'down':
                    Model\Faq::down($id);
                    throw new Redirection("/admin/faq");
                    break;
                case 'add':
                    $next = Model\Faq::next($filters['section']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'add',
                            'faq' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $faq = Model\Faq::get($id);

                    $cuantos = Model\Faq::next($faq->section);
                    $faq->cuantos = ($cuantos -1);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'edit',
                            'faq' => $faq,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Faq::delete($id);
                    throw new Redirection("/admin/faq");
                    break;
            }

            $faqs = Model\Faq::getAll($filters['section']);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'faq',
                    'file' => 'list',
                    'faqs' => $faqs,
                    'sections' => $sections,
                    'filters' => $filters
                )
            );
            
        }

    }

}
