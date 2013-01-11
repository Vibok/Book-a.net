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

    use Base\Core\View,
        Base\Model\Booka,
        Base\Model\Post,
        Base\Library\Page,
        Base\Library\Text;

    class Bookallow extends \Base\Core\Controller {
        
        public function index () {

            // Noticias de bookas en campaña
            $posts   = Post::getAll(array('booka'=>'allow'), true);

            // bookas en campaña
            $bookas  = Booka::getList(array('status'=>'3'));

            // contenido de la presentación
            
            return new View('view/index.html.php',
                array(
                    'posts'  => $posts,
                    'bookas' => $bookas
                )
            );
            
        }
        
    }
    
}