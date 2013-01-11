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
		Base\Library\Text,
		Base\Library\Advice,
		Base\Library\Newsletter as Boletin;

    class Newsletter {

        public static function process ($action = 'list', $id = null) {

            switch ($action) {
                case 'init':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $suject = \strip_tags($_POST['subject']);
                        if ($_POST['test']) {
                            $receivers = Boletin::getTesters();
                        } else {
                            $receivers = Boletin::getReceivers();
                        }
                        if (Boletin::initiateSending($suject, $receivers)) {

                            $mailing = Boletin::getSending();

                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'newsletter',
                                    'file' => 'init',
                                    'mailing' => $mailing,
                                    'receivers' => $receivers
                                )
                            );
                        }
                    }

                    throw new Redirection('/admin/newsletter');

                    break;
                case 'activate':
                    if (Boletin::activateSending()) {
//                        Advice::Info('Se ha activado el envío automático de newsletter');
                    } else {
                        Advice::Error('No se pudo activar el envío. Iniciar de nuevo');
                    }
                    throw new Redirection('/admin/newsletter');
                    break;
                case 'detail':
                    $list = Boletin::getDetail($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'detail',
                            'detail' => $id,
                            'list' => $list
                        )
                    );
                    break;
                default:
                    $mailing = Boletin::getSending();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'newsletter',
                            'file' => 'list',
                            'mailing' => $mailing
                        )
                    );
            }

        }
    }

}
