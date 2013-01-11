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
		Base\Core\Model,
        Base\Library\Check,
        Base\Library\Text,
        Base\Library\Advice,
        Base\Library\Page,
        Base\Core\View;

	class Newsletter extends \Base\Core\Controller {

	    // última newsletter enviada
		public function index () {
            throw new Redirection('/');

            if ($query = Model::query("SELECT html FROM mail WHERE email = 'any' ORDER BY id DESC")) {
                $content = $query->fetchColumn();
                return new View ('view/email/newsletter.html.php', array('content'=>$content));
            }
		}

        // suscribirse
		public function suscribe () {
            
            $email = $_POST['email'];
            
            if (Check::mail($email)) {
                if (Model::query("REPLACE INTO suscribe VALUES(?)", array($email))) {
                    $page = Page::get('suscribe');

                    return new View(
                        'view/about/sample.html.php',
                        array(
                            'text' => $page->text,
                            'content' => $page->content
                        )
                     );
                } else {
                    Advice::Error(Text::get('error-user-email-invalid'));
                    throw new Redirection('/#footer');
                }
            } else {
                Advice::Error(Text::get('error-user-email-invalid'));
                throw new Redirection('/#footer');
            }
            
            // cualquier otro caso
            throw new Redirection('/#footer');
        }        
        
        // dessuscribirse
		public function unsuscribe () {
            
            // si el token mola, lo doy de baja
            if (!empty($_GET['email'])) {
                $email = $_GET['email'];
                $query = Model::query('SELECT email FROM suscribe WHERE email = ?', array($email));
                if($exist = $query->fetchColumn()) {
                    Model::query("DELETE FROM suscribe WHERE email = ?", array($email));

                    $page = Page::get('unsuscribe');

                    return new View(
                        'view/about/sample.html.php',
                        array(
                            'text' => $page->text,
                            'content' => $page->content
                        )
                     );
                } else {
                    Advice::Error(Text::get('unsuscribe-email-incorrect'));
                    throw new Redirection('/');
                }

            } else {
                if (!empty($_SESSION['user']->id)) {
                    throw new Redirection('/dashboard/preferences/#check');
                } else {
                    throw new Redirection('/');
                }
            }
            
        }        
        
        
        
    }

}