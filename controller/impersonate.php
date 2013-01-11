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
        Base\Library\Advice,
		Base\Model\User;

	class Impersonate extends \Base\Core\Controller {

	    /**
	     * Suplantando al usuario
	     * @param string $id   user->id
	     */
		public function index () {

            $admin = $_SESSION['user'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST' 
                && !empty($_POST['id'])
                && !empty($_POST['impersonate'])) {

                // eliminamos todos los datos de session
                session_unset();
                
                // rellenamos de nuevo la sesión
                $_SESSION['user'] = User::get($_POST['id']);
                
                throw new Redirection('/dashboard');
                
            }
            else {
                Advice::Error('Ha ocurrido un error');
                throw new Redirection('/dashboard');
            }
		}

    }

}