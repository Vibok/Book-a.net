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

namespace Base\Controller {

    use Base\Core\Redirection,
        Base\Core\View,
        Base\Library\Text;

    class Search extends \Base\Core\Controller {
        
        public function index () {

            // si estamos en paginación de una búsqueda
            
            
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
                $_SESSION['current_search'] = $_POST['query'];
                $string = \strip_tags($_POST['query']); // busqueda de texto
                $results = \Base\Library\Search::text($string);

			} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searcher'])) {

                // vamos montando $params con los 3 parametros y las opciones marcadas en cada uno
                $params = array('scope'=>array(), 'status'=>array());

                foreach ($params as $param => $empty) {
                    foreach ($_POST[$param] as $key => $value) {
                        if ($value == 'all') {
                            $params[$param] = array();
                            break;
                        }
                        $params[$param][] = "'{$value}'";
                    }
                }

                $params['query'] = \strip_tags($_POST['query']);

                // para cada parametro, si no hay ninguno es todos los valores
                $results = \Base\Library\Search::params($params);

            } else {
                throw new Redirection('/');
            }
            
            return new View(
                'view/search.html.php',
                array(
                    'results' => $results
                )
             );

        }
        
        public function category ($category = null) {

            if (empty($category)) {
                throw new Redirection('/search');
            }
            return new View(
                'view/search.html.php',
                array(
                    'show' => 'categories',
                    'current' => $category,
                    'results' => array()
                )
             );

        }
        
    }
    
}