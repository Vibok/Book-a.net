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
        Base\Core\Error,
        Base\Core\View,
		Base\Model,
        Base\Library\Text,
        Base\Library\Advice;

	class Collection extends \Base\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id = null, $show = 'content') {
            
            if (empty($id)) {
                // lista de cabeceras
                $list = Model\Collection::getAll();
                return new View ('view/collection/list.html.php', array('list' => $list));

            } else {
                
                $item = Model\Collection::get($id);

                if (!$item instanceof Model\Collection) {
                    throw new Redirection("/collection");
                }

                $viewData = array();
                $viewData['id'] = $id;
                $viewData['collection'] = $item;
                $viewData['show'] = $show;

                if ($show == 'bookas') {
                    // bookas de la colección
                    $viewData['bookas'] = Model\Booka::getList(array('collection'=>$id));
                    
                    return new View ('view/collection/bookas.html.php', $viewData);
                    
                } else {
                    
                    $viewData['list'] = Model\Collection::getList(true);
                    $viewData['investors'] = Model\Collection::getInvestors($id, 10);
                    
                    return new View ('view/collection/view.html.php', $viewData);
                }
                
            }
		}

		public function raw ($id) {
            $item = Model\Collection::get($id);
            die(trace($item));
		}
 
    }

}