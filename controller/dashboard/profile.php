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

namespace Base\Controller\Dashboard {

    use Base\Core\View,
        Base\Core\Redirection,
        Base\Core\Error,
		Base\Library\Advice,
		Base\Library\Text,
        Base\Model;

    class Profile {

        public static function process ($action = 'list', $subaction = null) {
            
            $user = $_SESSION['user'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			    $errors = array();

                $user->name   = $_POST['name'];
                
                // tratar si quitan la imagen
                if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                    $user->avatar->remove();
                    $user->avatar = '';
                }
                // fin avatar

                // Avatar
                if(!empty($_FILES['avatar_upload']['name'])) {
                    $user->avatar = $_FILES['avatar_upload'];
                }

                // Intereses
                // añadir las que vienen y no tiene
                $tiene = Model\User\Interest::get($user->id);
                if (isset($_POST['interests'])) {
                    $viene = $_POST['interests'];
                    $quita = array_diff($tiene, $viene);
                } else {
                    $quita = $tiene;
                }
                $guarda = array_diff($viene, $tiene);
                foreach ($guarda as $key=>$int) {
                    $interest = new Model\User\Interest(array('id'=>$int,'user'=>$user->id));
                    $interest->save();
                }

                // quitar las que tiene y no vienen
                foreach ($quita as $key=>$int) {
                    $_interest = new Model\User\Interest(array('id'=>$int,'user'=>$user->id));
                    $_interest->remove($errors);
                }
               // fin intereses

                // datos perfil y personales
                $user_data = array (
                    'about' => $_POST['about'],
                    'keywords' => $_POST['keywords'],
                    'web' => $_POST['web'],
                    'facebook' => $_POST['facebook'] != Text::get('regular-facebook-url') ? $_POST['facebook'] : null,
                    'twitter' => $_POST['twitter'] != Text::get('regular-twitter-url') ? $_POST['twitter'] : null,
                    'google' => $_POST['google'] != Text::get('regular-google-url') ? $_POST['google'] : null,
                    'linkedin' => $_POST['linkedin'] != Text::get('regular-linkedin-url') ? $_POST['linkedin'] : null
                );
                
                $user_personal = array(
                    'name'     => $_POST['name'],
                    'nif'      => $_POST['nif'],
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'city'     => $_POST['city'],
                    'country'  => $_POST['country']
                );

                Model\User::setData($user->id, $user_data, $errors);
                Model\User::setPersonal($user->id, $user_personal, true, $errors);
                // fin datos perfil y personales
                
                // actualizamos para el avatar básicamente
                if ($user->save($errors)) {
                    $user = Model\User::flush();
                }
                
                if (empty($errors)) {
//                    Advice::Info(Text::get('user-profile-saved'));
                } else {
                    Advice::Error(implode('<br />', $errors));
                }
                

            }

            $viewData = array(
                'show' => 'profile',
                'personal' => Model\User::getPersonal($user->id),
                'categories' => Model\Booka\Category::getAll()
            );
            
            return new View ( 'view/dashboard/index.html.php', $viewData );
        }

    }

}
