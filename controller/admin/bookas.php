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
		Base\Library\Feed,
        Base\Library\Advice,
        Base\Model;

    class Bookas {

        public static function process ($action = 'list', $id = null, $filters = array()) {
            
            $log_text = null;
            $errors = array();

            if ($_SERVER['REQUEST_METHOD']=='POST') {

                $projData = Model\Booka::get($_POST['id']);
                if (empty($projData->id)) {
                    Advice::Error('El proyecto '.$_POST['id'].' no existe');
                    break;
                }

                if (isset($_POST['save-dates'])) {
                    $fields = array(
                        'created',
                        'updated',
                        'published',
                        'success',
                        'closed'
                        );

                    $set = '';
                    $values = array(':id' => $projData->id);

                    foreach ($fields as $field) {
                        if ($set != '') $set .= ", ";
                        $set .= "`$field` = :$field ";
                        if (empty($_POST[$field]) || $_POST[$field] == '0000-00-00')
                            $_POST[$field] = null;

                        $values[":$field"] = $_POST[$field];
                    }

                    if ($set == '') {
                        break;
                    }

                    try {
                        $sql = "UPDATE booka SET " . $set . " WHERE id = :id";
                        if (Model\Booka::query($sql, $values)) {
                            $log_text = 'El admin %s ha <span class="red">tocado las fechas</span> del proyecto '.$projData->name.' %s';
                        } else {
                            $log_text = 'Al admin %s le ha <span class="red">fallado al tocar las fechas</span> del proyecto '.$projData->name.' %s';
                        }
                    } catch(\PDOException $e) {
                        Advice::Error("Ha fallado! " . $e->getMessage());
                    }
                }

                if ($action == 'images') {
                    if (!empty($_POST['move']) && in_array($_POST['action'], array('up', 'down', 'up2', 'down2'))) {
                        $direction = $_POST['action'];
                        Model\Booka\Image::$direction($id, $_POST['move']);
                    }

                    $hash = (in_array($_POST['action'], array('up2', 'down2'))) ? '/#content' : '';
                    throw new Redirection('/admin/bookas/images/'.$id.$hash);

                }


            }

            /*
             * switch action,
             * proceso que sea,
             * redirect
             *
             */
            if (isset($id)) {
                $booka = Model\Booka::get($id);
            }
            switch ($action) {
                case 'review':
                    // pasar un proyecto a revision
                    if ($booka->ready($errors)) {
                        $redir = '/admin/reviews/add/'.$booka->id;
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Revisión</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Revisión</span>';
                    }
                    throw new Redirection('/admin/bookas');
                    break;
                case 'publish':
                    // poner un proyecto en campaña
                    if ($booka->publish($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">en Campaña</span>';
                        // Evento Feed
                        $log = new Feed();
                        $log_html = Text::html('feed-new_booka');
                        $log->unique = true;
                        $log->populate($booka->name, '/booka/'.$booka->id, $log_html, $booka->image->id);
                        $log->setTarget($booka->id);
                        $log->doPublic('community');
                        unset($log);
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">en Campaña</span>';
                    }
                    throw new Redirection('/admin/bookas');
                    break;
                case 'cancel':
                    // descartar un proyecto por malo
                    if ($booka->cancel($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Descartado</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Descartado</span>';
                    }
                    throw new Redirection('/admin/bookas');
                    break;
                case 'enable':
                    // si no está en edición, recuperarlo
                    if ($booka->enable($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Edición</span>';
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Edición</span>';
                    }
                    throw new Redirection('/admin/bookas');
                    break;
                case 'complete':
                    // dar un proyecto por financiado manualmente
                    if ($booka->succeed($errors)) {
                        $log_text = 'El admin %s ha pasado el proyecto %s al estado <span class="red">Financiado</span>';
                        // Evento Feed
                        $log = new Feed();
                        $log_html = \vsprintf('Se ha completado la financiación del %s', array(
                            Feed::item('booka', 'libro semilla')
                        ));
                        $log->populate($booka->name, '/booka/'.$booka->id, $log_html, $booka->image->id);
                        $log->setTarget($booka->id);
                        $log->doPublic('community');
                        unset($log);
                    } else {
                        $log_text = 'Al admin %s le ha fallado al pasar el proyecto %s al estado <span class="red">Financiado</span>';
                    }
                    throw new Redirection('/admin/bookas');
                    break;
            }

            if ($action == 'report') {
                // informe financiero
                // Datos para el informe de transacciones correctas
                $reportData = Model\Invest::getReportData($booka->id, $booka->status);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'bookas',
                        'file' => 'report',
                        'booka' => $booka,
                        'reportData' => $reportData
                    )
                );
            }

            if ($action == 'dates') {
                // cambiar fechas
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'bookas',
                        'file' => 'dates',
                        'booka' => $booka
                    )
                );
            }

            if ($action == 'add') {
                // poner el id y crear
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'bookas',
                        'file' => 'add'
                    )
                );
            }

            if ($action == 'images') {
                // imágenes
                $images = Model\Booka\Image::get($booka->id, 'booka');
                $images2 = Model\Booka\Image::get($booka->id, 'booka2');

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'bookas',
                        'file' => 'images',
                        'booka' => $booka,
                        'images' => $images,
                        'images2' => $images2
                    )
                );
            }


            if (!empty($filters['filtered'])) {
                $bookas = Model\Booka::getList($filters);
            } else {
                $bookas = array();
            }
            $status = Model\Booka::status();
            $categories = Model\Booka\Category::getAll();
            $collections = Model\Collection::getList();
            $orders = array(
                'name' => 'Nombre',
                'updated' => 'Enviado a revision'
            );

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'bookas',
                    'file' => 'list',
                    'bookas' => $bookas,
                    'filters' => $filters,
                    'status' => $status,
                    'categories' => $categories,
                    'collections' => $collections,
                    'orders' => $orders
                )
            );
            
        }

    }

}
