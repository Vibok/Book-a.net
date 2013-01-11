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

namespace Base\Controller\Admin {

    use Base\Core\View,
        Base\Core\Redirection,
        Base\Core\Error,
		Base\Library\Tpv,
		Base\Library\Paypal,
		Base\Library\Feed,
		Base\Library\Advice,
        Base\Model;

    class Accounts {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

           // reubicando aporte,
           if ($action == 'move') {

                // el aporte original
                $original = Model\Invest::get($id);
                $userData = Model\User::getMini($original->user);
                $bookaData = Model\Booka::getMini($original->booka);

                //el original tiene que ser de tpv o cash y estar como 'cargo ejecutado'
                if ($original->method == 'paypal' || $original->status != 1) {
                    AdviceError('No se puede reubicar este aporte!');
                    throw new Redirection('/admin/accounts');
                }


                // generar aporte manual y caducar el original
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['move']) ) {

                    // si falta proyecto, error

                    $bookaNew = $_POST['booka'];

                    // @TODO a saber si le toca dinero de alguna convocatoria
                    $campaign = null;

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $original->amount,
                            'user'      => $original->user,
                            'booka'   => $bookaNew,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => $original->charged,
                            'anonymous' => $original->anonymous,
                            'resign'    => $original->resign,
                            'admin'     => $_SESSION['user']->id,
                            'campaign'  => $campaign
                        )
                    );
                    //@TODO si el proyecto seleccionado

                    if ($invest->save($errors)) {

                        //recompensas que le tocan (si no era resign)
                        if (!$original->resign) {
                            // sacar recompensas
                            $rewards = Model\Booka\Reward::getAll($bookaNew, 'individual');

                            foreach ($rewards as $rewId => $rewData) {
                                $invest->setReward($rewId); //asignar
                            }
                        }

                        // cambio estado del aporte original a 'Reubicado' (no aparece en cofinanciadores)
                        // si tuviera que aparecer lo marcaríamos como caducado
                        if ($original->setStatus('5')) {
                            AdviceInfo('Aporte reubicado correctamente');
                            throw new Redirection('/admin/accounts');
                        } else {
                            $errors[] = 'A fallado al cambiar el estado del aporte original ('.$original->id.')';
                        }
                    } else{
                        $errors[] = 'Ha fallado algo al reubicar el aporte';
                    }

                }

                $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'move',
                    'original' => $original,
                    'user'     => $userData,
                    'booka'  => $bookaData
                );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia dereubicar
           }

           // cambiando estado del aporte aporte,
           if ($action == 'update') {

                // el aporte original
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    AdviceError('No tenemos registro del aporte '.$id);
                    throw new Redirection('/admin/accounts');
                }

                $status = Model\Invest::status();

                $new = isset($_POST['status']) ? $_POST['status'] : null;

                if ($invest->issue && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && $_POST['resolve'] == 1) {
                    Model\Invest::unsetIssue($id);
                    Model\Invest::setDetail($id, 'issue-solved', 'La incidencia se ha dado por resuelta por el usuario ' . $_SESSION['user']->name);
                    Advice::Info('La incidencia se ha dado por resuelta');
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && isset($new) && isset($status[$new])) {
                    
                    if ($new != $invest->status) {
                        if (Model\Invest::query("UPDATE invest SET status=:status WHERE id=:id", array(':id'=>$id, ':status'=>$new))) {
                            Model\Invest::setDetail($id, 'status-change'.rand(0, 9999), 'El admin ' . $_SESSION['user']->name . ' ha cambiado el estado del apote a '.$status[$new]);
                        } else {
                            Advice::Error('Ha fallado al actualizar el estado del aporte');
                        }
                    } else {
                        Advice::Error('No se ha cambiado el estado');
                    }
                    throw new Redirection('/admin/accounts/details/'.$id);
                }

                return new View('view/admin/index.html.php', array(
                    'folder' => 'accounts',
                    'file' => 'update',
                    'invest' => $invest,
                    'status' => $status
                ));

                // fin de la historia actualizar estado
           }

           // resolviendo incidencias
           if ($action == 'solve') {

                // el aporte original
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Advice::Error('No tenemos registro del aporte '.$id);
                    throw new Redirection('/admin/accounts');
                }
                $bookaData = Model\Booka::getMini($invest->booka);

                $errors = array();

                // primero cancelar
                switch ($invest->method) {
                    case 'paypal':
                        $err = array();
                        if (Paypal::cancelPreapproval($invest, $err)) {
                            $errors[] = 'Preaproval paypal cancelado.';
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo al cancelar el preapproval en paypal: ' . $txt_errors;
                            if ($invest->cancel()) {
                                $errors[] = 'Aporte cancelado';
                            } else{
                                $errors[] = 'Fallo al cancelar el aporte';
                            }
                        }
                        break;
                    case 'tpv':
                        $err = array();
                        if (Tpv::cancelPay($invest, $err)) {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Aporte cancelado correctamente. ' . $txt_errors;
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo en la operación. ' . $txt_errors;
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $errors[] = 'Aporte cancelado';
                        } else{
                            $errors[] = 'Fallo al cancelar el aporte';
                        }
                        break;
                }

                // luego resolver
                if ($invest->solve($errors)) {
                    Model\Invest::setDetail($id, 'invest-solve', "El admin {$_SESSION['user']->name} ha dado por resuelta la incidencia");
                    Advice::Info('La incidencia se ha dado por resuelta, el aporte se ha pasado a manual y cobrado');
                    throw new Redirection('/admin/accounts');
                } else {
                    Advice::Error('Ha fallado al resolver la incidencia: ' . implode (',', $errors));
                    throw new Redirection('/admin/accounts/details/'.$id);
                }
           }

            // Informe de la financiación de un proyecto
            if ($action == 'report') {
                // estados de aporte
                $booka = Model\Booka::get($id);
                if (!$booka instanceof Model\Booka) {
                    Advice::Error('Instancia de proyecto no valida');
                    throw new Redirection('/admin/accounts');
                }
                $invests = Model\Invest::getAll($id);
                $booka->investors = Model\Invest::investors($id, false, true);
                $users = array();
                foreach ($booka->investors as $investor) {
                    $users[$investor->user] = $investor->name;
                }
                $investStatus = Model\Invest::status();

                // Datos para el informe de transacciones correctas
                $Data = Model\Invest::getReportData($booka->id, $booka->status, $booka->round, $booka->passed);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'report',
                        'invests' => $invests,
                        'booka' => $booka,
                        'status' => $status,
                        'users' => $users,
                        'investStatus' => $investStatus,
                        'Data' => $Data
                    )
                );
            }

            // cancelar aporte antes de ejecución, solo aportes no cargados
            if ($action == 'cancel') {
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Advice::Error('No tenemos objeto para el aporte '.$id);
                    throw new Redirection('/admin/accounts');
                }
                $booka = Model\Booka::get($invest->booka);
                $userData = Model\User::get($invest->user);

                if ($booka->status > 3 && $booka->status < 6) {
                    $errors[] = 'No debería poderse cancelar un aporte cuando el proyecto ya está financiado. Si es imprescindible, hacerlo desde el panel de paypal o tpv y cambiar estado del aporte manualmente';
                    break;
                }

                switch ($invest->method) {
                    case 'paypal':
                        $err = array();
                        if (Paypal::cancelPreapproval($invest, $err)) {
                            $errors[] = 'Preaproval paypal cancelado.';
                            $log_text = "El admin %s ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo al cancelar el preapproval en paypal: ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                            if ($invest->cancel()) {
                                $errors[] = 'Aporte cancelado';
                            } else{
                                $errors[] = 'Fallo al cancelar el aporte';
                            }
                        }
                        break;
                    case 'tpv':
                        $err = array();
                        if (Tpv::cancelPay($invest, $err)) {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Aporte cancelado correctamente. ' . $txt_errors;
                            $log_text = "El admin %s ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo en la operación. ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al solicitar la cancelación del cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $log_text = "El admin %s ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s";
                            $errors[] = 'Aporte cancelado';
                        } else{
                            $log_text = "El admin %s ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ";
                            $errors[] = 'Fallo al cancelar el aporte';
                        }
                        break;
                }

                Model\Invest::setDetail($invest->id, 'manually-canceled', \vsprintf($log_text, array(
                        $_SESSION['user']->name,
                        $userData->name,
                        $invest->amount.' &euro;',
                        $invest->id,
                        $booka->name,
                        date('d/m/Y', strtotime($invest->invested))
                )));
            }

            // ejecutar cargo ahora!!, solo aportes no ejecutados
            // si esta pendiente, ejecutar el cargo ahora (como si fuera final de ronda), deja pendiente el pago secundario
            if ($action == 'execute' && $invest->status == 0) {
                $invest = Model\Invest::get($id);
                if (!$invest instanceof Model\Invest) {
                    Advice::Error('No tenemos objeto para el aporte '.$id);
                    throw new Redirection('/admin/accounts');
                }
                $booka = Model\Booka::get($invest->booka);
                $userData = Model\User::get($invest->user);
                
                switch ($invest->method) {
                    case 'paypal':
                        // a ver si tiene cuenta paypal
                        $bookaAccount = Model\Booka\Account::get($invest->booka);

                        if (empty($bookaAccount->paypal)) {
                            // Erroraco!
                            $errors[] = 'El proyecto no tiene cuenta paypal!!, ponersela en la seccion Contrato del dashboard del autor';
                            $log_text = null;
                            break;
                        }

                        $invest->account = $bookaAccount->paypal;
                        if (Paypal::pay($invest, $errors)) {
                            $errors[] = 'Cargo paypal correcto';
                            $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                            $invest->status = 1;
                            
                            // si era incidencia la desmarcamos
                            if ($invest->issue) {
                                Model\Invest::unsetIssue($invest->id);
                                Model\Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutar el aporte manualmente por el admin ' . $_SESSION['user']->name);
                            }
                            
                            
                        } else {
                            $txt_errors = implode('; ', $errors);
                            $errors[] = 'Fallo al ejecutar cargo paypal: ' . $txt_errors . '<strong>POSIBLE INCIDENCIA NO COMUNICADA Y APORTE NO CANCELADO, HAY QUE TRATARLA MANUALMENTE</strong>';
                            $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'tpv':
                        if (Tpv::pay($invest, $errors)) {
                            $errors[] = 'Cargo sermepa correcto';
                            $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                            $invest->status = 1;
                        } else {
                            $txt_errors = implode('; ', $errors);
                            $errors[] = 'Fallo al ejecutar cargo sermepa: ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'cash':
                        $invest->setStatus('1');
                        $errors[] = 'Aporte al contado, nada que ejecutar.';
                        $log_text = "El admin %s ha dado por ejecutado el aporte manual a nombre de %s por la cantidad de %s (id: %s) al proyecto %s del dia %s";
                        $invest->status = 1;
                        break;
                }

                if (!empty($log_text)) {
                    Model\Invest::setDetail($invest->id, 'manually-executed', $log->html);
                }
            }

            // visor de logs
            if ($action == 'viewer') {
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'viewer'
                    )
                );
            }

            if (!empty($errors)) {
                Advice::Error(implode('<br />', $errors));
            }

            // tipos de aporte
            $methods = Model\Invest::methods();
            // estados del proyecto
            $status = Model\Booka::status();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $bookas = Model\Invest::bookas(true);
            // usuarios cofinanciadores
            $users = Model\Invest::users(true);
            // colecciones
            $collections = Model\Collection::getList();

            // extras
            $types = array(
                'donative' => 'Solo los donativos',
                'anonymous' => 'Solo los anónimos',
                'manual' => 'Solo los manuales',
                'campaign' => 'Solo con riego',
            );
            
            // filtros de revisión de proyecto
            $review = array(
                'collect' => 'Recaudado',
                'paypal'  => 'Rev. PayPal',
                'tpv'     => 'Rev. TPV',
                'online'  => 'Pagos Online'
            );

            $issue = array(
                'show' => 'Solamente las incidencias',
                'hide' => 'Ocultar las incidencias'
            );


            /// detalles de una transaccion
            if ($action == 'details') {
                $invest = Model\Invest::get($id);
                $booka = Model\Booka::get($invest->booka);
                $userData = Model\User::get($invest->user);
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'details',
                        'invest'=>$invest,
                        'booka'=>$booka,
                        'user'=>$userData,
                        'status'=>$status,
                        'investStatus'=>$investStatus,
                        'collections'=>$collections
                    )
                );
            }

            // listado de aportes
            if ($filters['filtered'] == 'yes') {
                $list = Model\Invest::getList($filters);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'bookas'        => $bookas,
                    'review'        => $review,
                    'methods'       => $methods,
                    'types'         => $types,
                    'status'        => $status,
                    'issue'         => $issue,
                    'investStatus'  => $investStatus,
                    'collections'=>$collections
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
