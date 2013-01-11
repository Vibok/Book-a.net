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

    use Base\Model\Invest,
        Base\Model\Booka,
        Base\Model\User,
        Base\Core\Error,
		Base\Library\Feed,
		Base\Library\Text,
        Base\Core\Redirection;

    require_once 'library/paypal/stub.php'; // sí, uso el stub de paypal
    require_once 'library/paypal/log.php'; // sí, uso el log de paypal
    
    class Tpv extends \Base\Core\Controller {

        //@TODO: hacer los errorcode para sermepa
        public static $errcode = array(
            '101' => 'Tarjeta caducada',
            '102' => 'Tarjeta en excepción transitoria o bajo sospecha de fraude',
            '104' => 'Operación no permitida para esa tarjeta o terminal',
            '116' => 'Disponible insuficiente',
            '118' => 'Tarjeta no registrada',
            '129' => 'Código de seguridad (CVV2/CVC2) incorrecto',
            '180' => 'Tarjeta ajena al servicio',
            '184' => 'Error en la autenticación del titular',
            '190' => 'Denegación sin especificar Motivo',
            '191' => 'Fecha de caducidad errónea',
            '202' => 'Tarjeta en excepción transitoria o bajo sospecha de fraude con retirada de tarjeta',
            '912' => 'Emisor no disponible',
            '0900' => 'Transacción autorizada para devoluciones y confirmaciones',
            '9104' => 'Operación no permitida para esa tarjeta o terminal',
            '9912' => 'Emisor no disponible',
            'SIS0051' => 'Pedido repetido. Se envía notificación con código 913.',
            'SIS0078' => 'Método de pago no disponible para su tarjeta. Se envía notificación con código 118',
            'SIS0093' => 'Tarjeta no válida. Se envía notificación con código 180.',
            'SIS0094' => 'Error en la llamada al MPI sin controlar. Se envía notificación con código 184',
            'SIS0218' => 'El comercio no permite preautorización por la entrada XML.',
            'SIS0256' => 'El comercio no puede realizar preautorizaciones.',
            'SIS0257' => 'Esta tarjeta no permite operativa de preautorizaciones.',
            'SIS0261' => 'Operación detenida por superar el control de restricciones en la entrada al SIS.',
            'SIS0270' => 'El comercio no puede realizar autorizaciones en diferido.',
            'SIS0274' => 'Tipo de operación desconocida o no permitida por esta entrada al SIS.'            
        );

        public function index () {
            throw new Redirection('/', Error::BAD_REQUEST);
        }
        

        public function comunication () {
            if (isset($_POST['Ds_Response'])) {
                
                // el lio de que los espacios para el numero de pedido estan fijados por la idiosincracia del tpv
                if ($_POST['Ds_MerchantData'] == \substr($_POST['Ds_Order'], 0, -4)) {
                    $id = $_POST['Ds_MerchantData'];
                } else {
                    $id = \substr($_POST['Ds_Order'], 0, -4);
                }
                
                $invest = Invest::get($id);

                $userData = User::getMini($invest->user);
                $bookaData = Booka::getMini($invest->booka);

                $response = '';
                foreach ($_POST as $n => $v) {
                    $response .= "{$n}:'{$v}'; ";
                }

                $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
                $logger = &\Log::singleton('file', 'logs/'.date('Ymd').'_invest.log', 'caller', $conf);

                $logger->log("response (asincro): $response");
                $logger->log('##### END TPV ['.$id.'] '.date('d/m/Y').' '.$_POST['Ds_MerchantData'].'#####');
                $logger->close();


                if (!empty($_POST['Ds_AuthorisationCode']) && substr($_POST['Ds_Response'], 0, 3) != 'SIS' && $_POST['Ds_Response'] < 100) {
                    $invest->setPayment($_POST['Ds_AuthorisationCode']);
                    $invest->setStatus(1);
                    $_POST['result'] = 'Transaccion ok';

                    $doPublic = true;

                    echo '$*$OKY$*$';
                    Invest::setDetail($invest->id, 'tpv-response', 'La comunicación online del tpv se a completado correctamente. Proceso controller/tpv');
                    
                } else {

                    $Cerr = (string) $_POST['Ds_Response'];
                    $errTxt = self::$errcode[$Cerr];
                    Invest::setDetail($invest->id, 'tpv-response-error', 'El tpv ha comunicado el siguiente Codigo error: '.$Cerr.' - '.$errTxt.'. El aporte a quedado \'En proceso\'. Proceso controller/tpv');
                    $invest->cancel('ERR '.$Cerr);
                    $_POST['result'] = 'Fail';

                    $doPublic = false;
                }

                if ($doPublic) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($bookaData->id);
                    $log_html = Text::html('feed-invest',
                                        Feed::item('money', $invest->amount.' &euro;'),
                                        Feed::item('booka', $bookaData->name, $bookaData->id));
                    if ($invest->anonymous) {
                        $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                    } else {
                        $log->populate($userData->name, '/user/profile/'.$userData->id, $log_html, $userData->avatar->id);
                    }
                    $log->doPublic('users');
                }
                unset($log);
                
                
            } else {
                echo 'Se esperaban recibir datos de comunicación online del TPV.';
            }

            die;
        }

        public function simulacrum () {
            echo 'Simulacrum<br />';
            echo \trace($_GET);
            echo \trace($_POST);
            die;
        }

    }
    
}