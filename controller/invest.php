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

    use Base\Core\ACL,
        Base\Core\Error,
        Base\Core\Redirection,
        Base\Model,
        Base\Library\Feed,
        Base\Library\Text,
        Base\Library\Mail,
        Base\Library\Template,
        Base\Library\Advice,
        Base\Library\Paypal,
        Base\Library\Tpv;

/* Esto también quitarlo cuando quitemos lo de sincrona */
    require_once 'library/paypal/stub.php'; // sí, uso el stub de paypal
    require_once 'library/paypal/log.php'; // sí, uso el log de paypal

    class Invest extends \Base\Core\Controller {
        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */

        public function index($booka = null) {
            if (empty($booka))
                throw new Redirection('/', Redirection::TEMPORARY);

            $message = '';

            $bookaData = Model\Booka::get($booka);
            $methods = Model\Invest::methods();

            // si no está en campaña no pueden esta qui ni de coña
            if ($bookaData->status != 3) {
                throw new Redirection('/booka/' . $booka, Redirection::TEMPORARY);
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                $los_datos = $_POST;

                if (empty($_POST['amount'])) {
                    Advice::Error(Text::get('invest-amount-error'));
                    throw new Redirection("/booka/$booka/invest/?confirm=fail");
                }

                // dirección de envio para las recompensas
                // o datoas fiscales del donativo
                $address = array(
                    'name' => $_POST['name'],
                    'nif' => $_POST['nif'],
                    'address' => $_POST['address'],
                    'zipcode' => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'city' => $_POST['city'],
                    'country' => $_POST['country']
                );

                // añadir recompensas que ha elegido
                // añadir recompensas que ha elegido
                $chosen = $_POST['selected_reward'];
                if (empty($chosen) || !isset($_POST['selected_reward'])) {
                    // renuncia a las recompensas, bien por el/ella
                    $resign = true;
                    $reward = false;
                } else {
                    $resign = false;
                    $reward = true;
                }

                // insertamos los datos personales del usuario si no tiene registro aun
                Model\User::setPersonal($_SESSION['user']->id, $address, false);

                $invest = new Model\Invest(
                                array(
                                    'amount' => $_POST['amount'],
                                    'user' => $_SESSION['user']->id,
                                    'booka' => $booka,
                                    'method' => $_POST['method'],
                                    'status' => '-1', // aporte en proceso
                                    'invested' => date('Y-m-d'),
                                    'anonymous' => $_POST['anonymous'],
                                    'resign' => $_POST['resign']
                                )
                );
                if ($reward) {
                    $invest->rewards = array($chosen);
                }
                $invest->address = (object) $address;

                if ($invest->save($errors)) {
                    $method = strtolower($_POST['method']);
                    switch ($method) {
                        case 'tpv':
                            // redireccion al tpv
                            if (Tpv::pay($invest, $errors)) {
                                die;
                            } else {
                                Advice::Error(Text::get('invest-tpv-error_fatal'));
                            }
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            if (Paypal::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Advice::Error(Text::get('invest-paypal-error_fatal'));
                            }
                            break;
                        case 'cash':
                            $invest->setStatus('0');
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection("/invest/confirmed/{$booka}/{$invest->id}");
                            break;
                    }
                } else {
                    Advice::Error(Text::get('invest-create-error'));
                }
            } else {
                Advice::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/booka/$booka/invest/?confirm=fail");
        }

        public function confirmed($booka = null, $id = null) {
            if (empty($booka) || empty($id)) {
                Advice::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_' . $id . '_completed'])) {
                Advice::Info(Text::get('invest-process-completed'));
                throw new Redirection("/booka/$booka/invest/?confirm=ok");
            }

            $invest = Model\Invest::get($id);
            $bookaData = Model\Booka::getMini($booka);

            /* ----------------------------------
             * SOLAMENTE DESARROLLO Y PRUEBAS!!!
              ----------------------------------- */
            if ($invest->method == 'cash') {

                // Evento Feed
                $log = new Feed();
                $log->setTarget($bookaData->id);
                $log_html = Text::html('feed-invest', Feed::item('money', $invest->amount . ' &euro;'), Feed::item('project', $bookaData->name, $bookaData->id));
                if ($invest->anonymous) {
                    $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                } else {
                    $log->populate($_SESSION['user']->name, '/user/profile/' . $_SESSION['user']->id, $log_html, $_SESSION['user']->avatar->id);
                }
                $log->doPublic('users');
                unset($log);
            }
            /* --------------------------------------
             * FIN SOLAMENTE DESARROLLO Y PRUEBAS!!!
              -------------------------------------- */

            if ($invest->method == 'tpv') {
                // si el aporte no está en estado "cobrado" (1) 

                // @FIXME está asi porque la notificación es síncrona, quitar esto y activar comunicación online cuando sea asincrona
                if (isset($_GET['Ds_Response']) && $id == $_GET['Ds_MerchantData']) {
                    $invest = Model\Invest::get($id);

                    $userData = Model\User::getMini($invest->user);
                    $bookaData = Model\Booka::getMini($invest->booka);

                    $response = '';
                    foreach ($_POST as $n => $v) {
                        $response .= "{$n}:'{$v}'; ";
                    }

                    $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
                    $logger = &\Log::singleton('file', 'logs/' . date('Ymd') . '_invest.log', 'caller', $conf);

                    $logger->log("response (sincro): $response");
                    $logger->log('##### END TPV [' . $id . '] ' . date('d/m/Y') . ' ' . $_POST['Ds_MerchantData'] . '#####');
                    $logger->close();


                    if (!empty($_GET['Ds_AuthorisationCode']) && substr($_GET['Ds_Response'], 0, 3) != 'SIS' && $_GET['Ds_Response'] < 100) {
                        $invest->setPayment($_GET['Ds_AuthorisationCode']);
                        $invest->setStatus(1);
                        $doPublic = true;
                        Model\Invest::setDetail($invest->id, 'tpv-response', 'La respuesta del tpv se a completado por navegacion de usuario. Proceso controller/tpv<br /><pre>' . print_r($_GET, 1) . '</pre>');
                    } else {
                        $Cerr = (string) $_GET['Ds_Response'];
                        $errTxt = Base\Controller\Tpv::$errcode[$Cerr];
                        Model\Invest::setDetail($invest->id, 'tpv-response-error', 'El tpv ha comunicado el siguiente Codigo error: ' . $Cerr . ' - ' . $errTxt . '. El aporte a quedado \'En proceso\'. Proceso controller/tpv');
                        $invest->cancel('ERR ' . $Cerr);
                        $doPublic = false;
                    }

                    if ($doPublic) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($bookaData->id);
                        $log_html = Text::html('feed-invest', Feed::item('money', $invest->amount . ' &euro;'), Feed::item('booka', $bookaData->name, $bookaData->id));
                        if ($invest->anonymous) {
                            $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                        } else {
                            $log->populate($userData->name, '/user/profile/' . $userData->id, $log_html, $userData->avatar->id);
                        }
                        $log->doPublic('users');
                    }
                    unset($log);
                } else {
                    echo 'Se esperaban recibir datos del TPV.';
                }
            }

            if ($invest->method == 'paypal') {
                
                if (isset($_GET['token']) && $_GET['token'] == $invest->transaction) {
                    //valido
                    $token = $_GET['token'];
                    $payerid = $_GET['PayerID'];
                    Model\Invest::setDetail($invest->id, 'paypal-completed', 'El usuario ha regresado de PayPal y recibimos el token: '.$token.'  y el PayerID '.$payerid.'.');

                    $invest->setAccount($payerid);
                    $invest->account = $payerid;
                    
                    // completamos con el DoEsxpresscheckout despues de comprobar que está completado y cobrado
                    if (Paypal::pay($invest)) {
                        $invest->setPayment($invest->transaction);
                    } else {
                        Advice::Error('No hemos podido confirmar la operaci&oacute;n, por favor contacte con nosotros. Aporte ID: '.$invest->id);
                        throw new Redirection("/booka/$booka/invest/?confirm=fail");
                    }
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget($bookaData->id);
                $log_html = Text::html('feed-invest', Feed::item('money', $invest->amount . ' &euro;'), Feed::item('project', $bookaData->name, $bookaData->id));
                if ($invest->anonymous) {
                    $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                } else {
                    $log->populate($_SESSION['user']->name, '/user/profile/' . $_SESSION['user']->id, $log_html, $_SESSION['user']->avatar->id);
                }
                $log->doPublic('users');
                unset($log);
            }

            // email de agradecimiento al cofinanciador
            // primero monto el texto de recompensas
            if ($invest->resign) {
                $txt_rewards = Text::get('invest-resign');
                $template = Template::get(28); // plantilla de donativo
            } else {
                $rewards = $invest->rewards;
                array_walk($rewards, function (&$reward) {
                            $reward = ($reward->type == 9999) ? $reward->other_text : $reward->name_es;
                        });
                $txt_rewards = implode(', ', $rewards);
                $template = Template::get(10); // plantilla de agradecimiento
            }


            // Dirección en el mail
            $txt_address = Text::get('invest-mail_info-address');
            if ($invest->resign) {
                $txt_address .= '<br> ' . Text::get('invest-address-name-field') . ': ' . $invest->address->name;
                $txt_address .= '<br> ' . Text::get('invest-address-nif-field') . ': ' . $invest->address->nif;
            }
            $txt_address .= '<br> ' . Text::get('address-address-field') . ': ' . $invest->address->address;
            $txt_address .= '<br> ' . Text::get('address-city-field') . ': ' . $invest->address->city;
            $txt_address .= '<br> ' . Text::get('address-location-field') . ': ' . $invest->address->location;
            $txt_address .= '<br> ' . Text::get('address-zipcode-field') . ': ' . $invest->address->zipcode;
            $txt_address .= '<br> ' . Text::get('address-country-field') . ': ' . $invest->address->country;

            // Agradecimiento al cofinanciador
            // Sustituimos los datos
            $subject = str_replace('%BOOKANAME%', $bookaData->clr_name, $template->title);

            // En el contenido:
            $search = array('%USERNAME%', '%BOOKANAME%', '%BOOKAURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%');
            $replace = array($_SESSION['user']->name, $bookaData->clr_name, SITE_URL . '/booka/' . $bookaData->id, $invest->amount, $txt_rewards, $txt_address);
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();

            $mailHandler->to = $_SESSION['user']->email;
            $mailHandler->toName = $_SESSION['user']->name;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            if ($mailHandler->send($errors)) {
//                Advice::Info(Text::get('booka-invest-thanks_mail-success'));
            } else {
                Advice::Error(Text::get('booka-invest-thanks_mail-fail'));
                Advice::Error(implode('<br />', $errors));
            }

            unset($mailHandler);


            // Notificación a booka
            /*
              $template = Template::get(29);
              // Sustituimos los datos
              $subject = str_replace('%BOOKANAME%', $bookaData->name, $template->title);

              // En el contenido:
              $search  = array('%OWNERNAME%', '%USERNAME%', '%BOOKANAME%', '%SITEURL%', '%AMOUNT%', '%MESSAGEURL%');
              $replace = array('Booka', $_SESSION['user']->name, $bookaData->name, SITE_URL, $invest->amount, SITE_URL.'/user/profile/'.$_SESSION['user']->id.'/message');
              $content = \str_replace($search, $replace, $template->text);

              $mailHandler = new Mail();

              $mailHandler->to = $bookaData->user->email;
              $mailHandler->toName = $bookaData->user->name;
              $mailHandler->subject = $subject;
              $mailHandler->content = $content;
              $mailHandler->html = true;
              $mailHandler->template = $template->id;
              $mailHandler->send();

              unset($mailHandler);
             */

            // marcar que ya se ha completado el proceso de aportar
            $_SESSION['invest_' . $invest->id . '_completed'] = true;

            // mandarlo a la pagina de gracias
            throw new Redirection("/booka/$booka/invest/?confirm=ok");
        }

        /*
         * @params booka id del proyecto
         * @params is id del aporte
         */

        public function fail($booka = null, $id = null) {
            if (empty($booka))
                throw new Redirection('/', Redirection::TEMPORARY);

            if (empty($id))
                throw new Redirection("/booka/$booka/invest", Redirection::TEMPORARY);

            // quitar el preapproval y cancelar el aporte
            $invest = Model\Invest::get($id);
            $invest->setStatus('-1');

            // preparamos url para recuperar aporte en proceso
            Advice::Error('Ha ocurrido un error en PayPal, puedes intentarlo de nuevo o contactarnos facilit&aacute;ndonos el ID: ' . $id);

            // mandarlo a la pagina de aportar para que lo intente de nuevo
            throw new Redirection("/booka/$booka/invest/?confirm=fail");
        }

    }

}