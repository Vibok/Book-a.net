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

    use Base\Library\Page,
        Base\Core\Redirection,
        Base\Core\View,
        Base\Model,
        Base\Library\Text,
        Base\Library\Mail,
        Base\Library\Template;

    class About extends \Base\Core\Controller {
        
        public function index ($id = null, $section = null) {

            // si llegan a la de mantenimiento sin estar en mantenimiento
            if ($id == 'maintenance' && CONF_MAINTENANCE !== true) {
                $id = 'credits';

            } elseif (CONF_MAINTENANCE === true) {

                return new View('view/about/maintenance.html.php', array());
                
            }

            // paginas especificas
            if ($id == 'faq' || $id == 'contact') {
                throw new Redirection('/'.$id, Redirection::TEMPORARY);
            }

            // el tipo de contenido de la pagina about es diferente
            if ($id == 'about' || empty($id)) {
                // resto de casos
                $page = Page::get('about');

                return new View(
                    'view/about/index.html.php',
                    array(
                        'text' => $page->text,
                        'content' => $page->content
                    )
                 );

            }

            // resto de casos
            $page = Page::get($id);

            return new View(
                'view/about/sample.html.php',
                array(
                    'text' => $page->text,
                    'content' => $page->content
                )
             );

        }
        
    }
    
}