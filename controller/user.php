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
        Base\Library\Feed,
        Base\Library\Page,
        Base\Library\Advice;

	class User extends \Base\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id, $show = '') {
		    throw new Redirection('/user/profile/' .  $id . '/' . $show, Redirection::PERMANENT);
		}

		public function raw ($id) {
            $user = Model\User::get($id);
            die(trace($user));
		}

        /**
         * Inicio de sesión.
         * Si no se le pasan parámetros carga el tpl de identificación.
         *
         * @param string $username Nombre de usuario
         * @param string $password Contraseña
         */
        public function login () {

            $page = Page::get('login');
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['login'])) {
                $userid = \strtolower($_POST['userid']);
                $password = $_POST['password'];
                if (false !== ($user = (\Base\Model\User::login($userid, $password)))) {
                    $_SESSION['user'] = $user;
                    if (!empty($user->lang)) {
                        $_SESSION['lang'] = $user->lang;
                    }
                    unset($_SESSION['admin_menu']);
                    if (!empty($_POST['return'])) {
                        throw new Redirection($_POST['return']);
                    } elseif (!empty($_SESSION['jumpto'])) {
                        $jumpto = $_SESSION['jumpto'];
                        unset($_SESSION['jumpto']);
                        throw new Redirection($jumpto);
                    } elseif (isset($user->roles['admin']) || isset($user->roles['superadmin'])) {
                        throw new Redirection('/admin');
                    } else {
                        throw new Redirection('/dashboard');
                    }
                }
                else {
                    Advice::Error(Text::get('login-fail'));
                }
            }

            return new View ('view/user/login.html.php', array(
                'text' => $page->text,
                'content' => $page->content
            ));

        }

        /**
         * Cerrar sesión.
         */
        public function logout() {
            $lang = '?lang='.$_SESSION['lang'];
            session_start();
            session_unset();
            session_destroy();
            session_write_close();
            session_regenerate_id(true);
            throw new Redirection('/'.$lang);
            die;
        }
        /**
         * Registro de usuario.
         */
        public function register () {
            $page = Page::get('login');
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                foreach ($_POST as $key=>$value) {
                    $_POST[$key] = trim($value);
                }

                if ($_POST['confirm'] != 'true') {
                    Advice::Error(Text::get('user-register-must_accept'));
                }
                
            	$errors = array();

				if (strcmp($_POST['email'], $_POST['remail']) !== 0) {
					$errors['remail'] = Text::get('error-register-email-confirm');
				}
				if(strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
					$errors['rpassword'] = Text::get('error-register-password-confirm');
				}

				$user = new Model\User();
				$user->userid = $_POST['userid'];
				$user->name = $_POST['username'];
				$user->email = $_POST['email'];
				$user->password = $_POST['password'];
				$user->active = true;

				$user->save($errors);

				if(empty($errors)) {
				  Advice::Info(Text::get('user-register-success'));
				  Advice::Info(Text::html('user-register-access_data', $user->id, $user->password));
                  
                  throw new Redirection('/user/login');
				} else {
					foreach ($errors as $field=>$text) {
						Advice::Error($text);
					}
				}
			}
			return new View (
				'view/user/login.html.php',
				array(
					'text' => $page->text,
					'content' => $page->content,
                    'errors' => $errors
				)
			);
        }

		/**
		 * Registro de usuario desde oauth
		 */
		public function oauth_register() {

			//comprovar si venimos de un registro via oauth
			if($_POST['provider']) {

				require_once OAUTH_LIBS;

				$provider = $_POST['provider'];

				$oauth = new \SocialAuth($provider);
				//importar els tokens obtinguts anteriorment via POST
				if($_POST['tokens'][$oauth->provider]['token']) $oauth->tokens[$oauth->provider]['token'] = $_POST['tokens'][$oauth->provider]['token'];
				if($_POST['tokens'][$oauth->provider]['secret']) $oauth->tokens[$oauth->provider]['secret'] =$_POST['tokens'][$oauth->provider]['secret'];
				//print_r($_POST['tokens']);print_R($oauth->tokens[$oauth->provider]);die;
				$user = new Model\User();
				$user->userid = $_POST['userid'];
				$user->email = $_POST['email'];
                $user->active = true;

				//resta de dades
				foreach($oauth->user_data as $k => $v) {
					if($_POST[$k]) {
						$oauth->user_data[$k] = $_POST[$k];
						if(in_array($k,$oauth->import_user_data)) $user->$k = $_POST[$k];
					}
				}
				//si no existe nombre, nos lo inventamos a partir del userid
				if(trim($user->name)=='') $user->name = ucfirst($user->userid);

				//print_R($user);print_r($oauth);die;
				//no hará falta comprovar la contraseña ni el estado del usuario
				$skip_validations = array('password','active');

				//si el email proviene del proveedor de oauth, podemos confiar en el y lo activamos por defecto
				if($_POST['provider_email'] == $user->email) {
					$user->confirmed = 1;
				}
				//comprovamos si ya existe el usuario
				//en caso de que si, se comprovará que el password sea correcto
				$query = Model\User::query('SELECT id,password,active FROM user WHERE email = ?', array($user->email));
				if($u = $query->fetchObject()) {
					if ($u->password == sha1($_POST['password'])) {
						//ok, login e importar datos
						//y fuerza que pueda logear en caso de que no esté activo
						if(!$oauth->bookaLogin(true)) {
							//si no: registrar errores
							Advice::Error(Text::get($oauth->last_error));
						}
					}
					else {
						Advice::Error(Text::get('oauth-user-password-exists'));
                        throw new Redirection('/user/login');
                    }
				}
				elseif($user->save($errors,$skip_validations)) {
					//si el usuario se ha creado correctamente, login e importacion de datos
					//y fuerza que pueda logear en caso de que no esté activo
					if(!$oauth->bookaLogin(true)) {
						//si no: registrar errores
						Advice::Error(Text::get($oauth->last_error));
					}
				}
				elseif($errors) {
					foreach($errors as $err => $val) {
						if($err!='email' && $err!='userid') Advice::Error($val);
					}
				}
			}
            
            $page = Page::get('confirm');
			return new View (
				'view/user/confirm.html.php',
				array(
                    'text' => $page->text,
                    'content' => $page->content,
					'oauth' => $oauth
				)
			);
		}
        /**
         * Registro de usuario a traves de Oauth (libreria HybridOauth, openid, facebook, twitter, etc).
         */
        public function oauth () {

			require_once OAUTH_LIBS;

			if( isset( $_GET["provider"] ) && $_GET["provider"] ) {

				$oauth = new \SocialAuth($_GET["provider"]);
				if(!$oauth->authenticate()) {
					//si falla: error, si no siempre se redirige al proveedor
					Advice::Error(Text::get($oauth->last_error));
				}


			}

			//return from provider authentication
			if( isset( $_GET["return"] ) && $_GET["return"] ) {

				//check twitter activation
				$oauth = new \SocialAuth($_GET["return"]);

				if($oauth->login()) {
					//si ok: redireccion de login!
//					Advice::Info("USER INFO:\n".print_r($oauth->user_data,1));
					//si es posible, login (redirecciona a user/dashboard o a user/confirm)
					//y fuerza que pueda logear en caso de que no esté activo
					if(!$oauth->bookaLogin()) {
						//si falla: error o formulario de confirmación
						if($oauth->last_error == 'oauth-user-not-exists') {
                            $page = Page::get('confirm_account');
                            
							return new View (
								'view/user/confirm.html.php',
								array(
									'oauth' => $oauth,
                                    'text' => $page->text,
                                    'content' => $page->content
								)
							);
						}
						elseif($oauth->last_error == 'oauth-user-password-exists') {
                            Advice::Error(Text::get('oauth-user-password-exists'));
                            throw new Redirection('/user/login');
						}
						else Advice::Error(Text::get($oauth->last_error));
					}
				}
				else {
					//si falla: error
					Advice::Error(Text::get($oauth->last_error));
				}
			}

            $page = Page::get('login');
            return new View (
                'view/user/login.html.php',
                array(
                    'text' => $page->text,
                    'content' => $page->content
                )
            );
        }

        /**
         * Perfil público de usuario.
         *
         * @param string $id    Nombre de usuario
         */
        public function profile ($id, $show = 'profile') {

            if (!in_array($show, array('profile', 'message', 'shelves', 'proposal'))) {
                $show = 'profile';
            }

            $user = Model\User::get($id);
            
            if (!$user instanceof Model\User || ($user->hide && $_SESSION['user']->id != $id)) {
                throw new Redirection('/');
            }

            // arreglando los datos de usuario para pintar
            $user->about = nl2br(Text::urlink($user->about));
            
            // añadimos las palabras clave a los intereses
            // pero no cabe
            /*
            if (!empty($user->keywords))
                $user->interests[] = $user->keywords;
             * 
             */
            
            // montamos localidad, ciudad, pais segun lo que tenga
            $address = Model\User::getPersonal($id);
            $user->location = array();
            if (!empty($address->location))
                $user->location[] = $address->location;
            if (!empty($address->city))
                $user->location[] = $address->city;
            if (!empty($address->country))
                $user->location[] = $address->country;
            
            // arreglamos la url
            $user->fullweb = (substr($user->web, 0, strlen('http')) != 'http' ) ? 'http://'.$user->web : $user->web;

            $supports = $user->support;
            $user->num_bookas = (int) $supports['bookas'];
            if ($user->num_bookas > 0 && $user->level == 0) {
                $user->level = 1;
                $user->setLevel(1);
            }
            //--- para usuarios públicos---
            if (empty($_SESSION['user'])) {
                // necesita cierto nivel para poder ver los eprfiles de la gente
                $_SESSION['jumpto'] = '/user/profile/' .  $id . '/' . $show;
                Advice::Info(Text::get('user-login-required-to_see'));
                throw new Redirection("/user/login");
            }
            //--- el resto pueden seguir ---

            $viewData = array();
            $viewData['user'] = $user;
            $viewData['show'] = $show;

            if ($show == 'profile') {
                // Bookas impulsados
                $viewData['invest_on'] = Model\User::invested($id, true);
            }
            
            if ($show == 'message') $show = 'profile';

            return new View ('view/user/'.$show.'.html.php', $viewData);
        }

        /**
         * Activación usuario.
         *
         * @param type string	$token
         */
        public function activate($token) {
            $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
            if($id = $query->fetchColumn()) {
                $user = Model\User::get($id);
                if(!$user->confirmed) {
                    $user->confirmed = true;
                    $user->active = true;
                    if($user->save($errors)) {
                        Advice::Info(Text::get('user-activate-success'));
                        $_SESSION['user'] = $user;

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($user->id, 'user');
                        $log->populate($user->name, '/user/profile/'.$user->id, Text::html('feed-new_user'), 1);
                        $log->doPublic('users');
                        unset($log);

                    }
                    else {
                        Advice::Error($errors);
                    }
                }
                else {
                    Advice::Info(Text::get('user-activate-already-active'));
                }
            }
            else {
                Advice::Error(Text::get('user-activate-fail'));
            }
            throw new Redirection('/dashboard');
        }

        /**
         * Cambiar dirección de correo.
         *
         * @param type string	$token
         */
        public function changeemail($token) {
            $token = base64_decode($token);
            if(count(explode('¬', $token)) > 1) {
                $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
                if($id = $query->fetchColumn()) {
                    $user = Model\User::get($id);
                    $old_email = $user->email;
                    $user->email = $token;
                    $errors = array();
                    if($user->save($errors)) {
                        Advice::Info(Text::get('user-changeemail-success'));

                        // Refresca la sesión.
                        Model\User::flush();
                    }
                    else {
                        Advice::Error($errors);
                        throw new Redirection('/contact?action=email&email='.$old_email);
                    }
                }
                else {
                    Advice::Error(Text::get('user-changeemail-fail'));
                }
            }
            else {
                Advice::Error(Text::get('user-changeemail-fail'));
            }
            throw new Redirection('/dashboard');
        }

        /**
         * Recuperacion de contraseña
         * - Si no llega nada, mostrar formulario para que pongan su username y el email correspondiente
         * - Si llega post es una peticion, comprobar que el username y el email que han puesto son válidos
         *      si no lo son, dejarlos en el formulario y mensaje de error
         *      si son válidos, enviar email con la url y mensaje de ok
         *
         * - Si llega un hash, verificar y darle acceso hasta su dashboard /profile/access para que la cambien
         *
         * @param string $token     Codigo
         */
        public function recover ($token = null) {

            // si el token mola, logueo este usuario y lo llevo a su dashboard
            if (!empty($token)) {
                $token = base64_decode($token);
                $parts = explode('¬', $token);
                if(count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if($id = $query->fetchColumn()) {
                        if(!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            Model\User::query('UPDATE user SET active = 1 WHERE id = ?', array($id));
                            $user = Model\User::get($id);
                            $_SESSION['user'] = $user;
                            $_SESSION['recovering'] = $user->id;
                            throw new Redirection('/dashboard/preferences#password');
                        }
                    }
                }

                Advice::Error(Text::get('recover-token-incorrect'));
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recover'])) {
                $username = $_POST['username'];
                $email    = $_POST['email'];
                if ((!empty($username) || !empty($email)) && Model\User::recover($username, $email)) {
                    Advice::Info(Text::get('recover-email-sended'));
                    unset($_POST['username']);
                    unset($_POST['email']);
                }
                elseif (!empty($email)) {
                    Advice::Error(Text::get('recover-request-fail'));
                    throw new Redirection('/contact?action=password&email='.$email);
                } else {
                    Advice::Error(Text::get('recover-request-fail'));
                }
            }

            $page = Page::get('recover');
            return new View (
                'view/user/recover.html.php',
                array(
                    'text'   => $page->text,
                    'content' => $page->content
                )
            );

        }

        /**
         * Darse de baja
         * - Si no llega nada, mostrar formulario para que pongan el email de su cuenta
         * - Si llega post es una peticion, comprobar que el email que han puesto es válido
         *      si no es, dejarlos en el formulario y mensaje de error
         *      si es válido, enviar email con la url y mensaje de ok
         *
         * - Si llega un hash, verificar y dar de baja la cuenta (desactivar y ocultar)
         *
         * @param string $token     Codigo
         */
        public function leave ($token = null) {

            // si el token mola, lo doy de baja
            if (!empty($token)) {
                $token = base64_decode($token);
                $parts = explode('¬', $token);
                if(count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if($id = $query->fetchColumn()) {
                        if(!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            if (Model\User::cancel($id)) {
                                Advice::Info(Text::get('leave-process-completed'));
                                throw new Redirection('/user/login');
                            } else {
                                Advice::Error(Text::get('leave-process-fail'));
                                throw new Redirection('/contact?action=leave&email='.$parts[1]);
                            }
                        }
                    }
                }

                $error = Text::get('leave-token-incorrect');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['leaving'])) {
                if (Model\User::leaving($_POST['email'], $_POST['reason'])) {
                    Advice::Info(Text::get('leave-email-sended'));
                    unset($_POST['email']);
                    unset($_POST['reason']);
                }
                else {
                    Advice::Error(Text::get('leave-request-fail'));
                }
            }

            $page = Page::get('leave');
            return new View (
                'view/user/leave.html.php',
                array(
                    'text'   => $page->text,
                    'content' => $page->content
                )
            );

        }

    }

}