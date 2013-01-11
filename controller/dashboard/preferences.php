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

    class Preferences {

        public static function process ($action = 'list', $subaction = null) {
         
            $user = $_SESSION['user'];

            // para operar en esta sección deben tener el email confirmado
            // si no lo tienen: aviso y que contacten
            //    a ver si podemos recuperar el mail de confirmacion o reenviarlo
            if (!$user->confirmed) {
                Advice::Error(Text::get('user-email-not_confirmed'));
                throw new Redirection('/contact?action=confirm&email='.$user->email);
            }

            $success = array();
            $errors = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['save-email-data'])) {
                    // E-mail
                    if(!empty($_POST['user_nemail']) || !empty($_POST['user_remail'])) {
                        if(empty($_POST['user_nemail'])) {
                            $errors['email'] = Text::get('error-user-email-empty');
                        }
                        elseif(!\Base\Library\Check::mail($_POST['user_nemail'])) {
                            $errors['email'] = Text::get('error-user-email-invalid');
                        }
                        elseif(empty($_POST['user_remail'])) {
                            $errors['email_retry'] = Text::get('error-user-email-empty');
                        }
                        elseif (strcmp($_POST['user_nemail'], $_POST['user_remail']) !== 0) {
                            $errors['email_retry'] = Text::get('error-user-email-confirm');
                        }
                        else {
                            $user->email = $_POST['user_nemail'];
                            unset($_POST['user_nemail']);
                            unset($_POST['user_remail']);
                            $success[] = Text::get('user-email-change-sended');

                        }
                    }
                } 
                
                if (isset($_POST['save-password-data'])) {
                    // Contraseña
                    if(!empty($_POST['user_npassword']) ||!empty($_POST['user_rpassword'])) {
                        if(empty($_POST['user_npassword'])) {
                            $errors['password_new'] = Text::get('error-user-password-empty');
                        }
                        elseif(!\Base\Library\Check::password($_POST['user_npassword'])) {
                            $errors['password_new'] = Text::get('error-user-password-invalid');
                        }
                        elseif(empty($_POST['user_rpassword'])) {
                            $errors['password_retry'] = Text::get('error-user-password-empty');
                        }
                        elseif(strcmp($_POST['user_npassword'], $_POST['user_rpassword']) !== 0) {
                            $errors['password_retry'] = Text::get('error-user-password-confirm');
                        }
                        else {
                            $user->password = $_POST['user_npassword'];
                            unset($_POST['user_password']);
                            unset($_POST['user_npassword']);
                            unset($_POST['user_rpassword']);
                            $success[] = Text::get('user-password-changed');

                        }
                    }
                }

                if($user->save($errors)) {
                    // Refresca la sesión.
                    $user = Model\User::flush();
                    if (isset($_SESSION['recovering'])) unset($_SESSION['recovering']);
                }

                if (empty($errors))
                    Advice::Info(implode('<br />', $success));
                else
                    Advice::Info(implode('<br />', $errors));
            }
            
            // procesamos las preferencias
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save-preferences'])) {

			    $errors = array();

                // campos de preferencias
                $fields = array(
                    'updates',
                    'messages',
                    'progress',
                    'mailing',
                    'sideads'
                );

                $prefs = array();

                foreach ($fields as $field) {
                    $prefs[$field] = in_array($field, $_POST['preferences']);
                }

                if (Model\User::setPreferences($user->id, $prefs, $errors)) {
//                    Advice::Info(Text::get('user-prefer-saved'));
                } else {
                    Advice::Error(implode('<br />', $errors));
                }

            }
            
            $viewData = array(
                'show' => 'preferences', 
                'preferences' => Model\User::getPreferences($user->id),
                'errors' => $errors
            );

            return new View ( 'view/dashboard/index.html.php', $viewData );
            
        }

    }

}
