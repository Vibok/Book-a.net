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
        Base\Core\View,
        Base\Core\Redirection,
        Base\Model,
		Base\Library\Lang,
        Base\Library\Page,
        Base\Library\Mail,
        Base\Library\Template,
        Base\Library\Advice,
        Base\Library\Newsletter;

	class Admin extends \Base\Core\Controller {

            // Array de los gestores que existen
            static public $options = array(
                    'accounts' => array(
                        'label' => 'Operaciones económicas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'details' => array('label' => 'Detalles del aporte', 'item' => true),
                            'update' => array('label' => 'Cambiando el estado al aporte', 'item' => true),
                            'move'  => array('label' => 'Reubicando el aporte', 'item' => true),
                            'execute' => array('label' => 'Ejecución del cargo', 'item' => true),
                            'cancel' => array('label' => 'Cancelando aporte', 'item' => true),
                            'report' => array('label' => 'Informe de proyecto', 'item' => true),
                            'viewer' => array('label' => 'Viendo logs', 'item' => false)
                        ),
                        'filters' => array('id'=>'', 'methods'=>'', 'investStatus'=>'all', 'bookas'=>'', 'collections'=>'', 'name'=>'', 'review'=>'', 'types'=>'', 'date_from'=>'', 'date_until'=>'', 'issue'=>'all')
                    ),
                    'bookas' => array(
                        'label' => 'Bookas',
                        'actions' => array(
                            'list'   => array('label' => 'Listando', 'item' => false),
                            'add'    => array('label' => 'Creando uno nuevo', 'item' => false),
                            'dates'  => array('label' => 'Fechas del booka', 'item' => true),
                            'images'  => array('label' => 'Ordenando imágenes del booka', 'item' => true),
                            'report' => array('label' => 'Informe Financiero del booka', 'item' => true)
                        ),
                        'filters' => array('status'=>'-1', 'collection'=>'', 'category'=>'', 'name'=>'', 'order'=>'')
                    ),
                    'blog' => array(
                        'label' => 'Blog',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Entrada', 'item' => false),
                            'edit' => array('label' => 'Editando Entrada', 'item' => true),
                            'home' => array('label' => 'Ordenando Entradas en Home', 'item' => false),
                            'footer' => array('label' => 'Ordenando Entradas en Footer', 'item' => false),
                            'top' => array('label' => 'Ordenando Entradas en Top', 'item' => false)
                        ),
                        'filters' => array('show'=>'')
                    ),
                    'campaigns' => array(
                        'label' => 'Campañas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'manage'  => array('label' => 'Gestionando la campaña del booka', 'item' => true)
                        )
                    ),
                    'categories' => array(
                        'label' => 'Categorías',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Categoría', 'item' => false),
                            'edit' => array('label' => 'Editando Categoría', 'item' => true)
                        )
                    ),
                    'collections' => array(
                        'label' => 'Colecciones',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Colección', 'item' => false),
                            'edit' => array('label' => 'Editando Colección', 'item' => true),
                            'bookas' => array('label' => 'Titulos de la Colección', 'item' => true)
                        )
                    ),
                    'costs' => array(
                        'label' => 'Tipos costes',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Tipo de coste', 'item' => false),
                            'edit' => array('label' => 'Editando Tipo de coste', 'item' => true)
                        )
                    ),
                    'faq' => array(
                        'label' => 'FAQs',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Pregunta', 'item' => false),
                            'edit' => array('label' => 'Editando Pregunta', 'item' => true)
                        ),
                        'filters' => array('section'=>'main')
                    ),
                    'footer' => array(
                        'label' => 'Footer',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo elemento', 'item' => false),
                            'edit' => array('label' => 'Editando Elemento', 'item' => true)
                        ),
                        'filters' => array('column'=>'')
                    ),
                    'invests' => array(
                        'label' => 'Aportes',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'details' => array('label' => 'Detalles del aporte', 'item' => true),
                            'edit' => array('label' => 'Modificando el aporte', 'item' => true),
                            'add'  => array('label' => 'Aporte manual', 'item' => false)
                        ),
                        'filters' => array('methods'=>'', 'status'=>'all', 'investStatus'=>'all', 'bookas'=>'', 'name'=>'', 'types'=>'')
                    ),
                    'mailing' => array(
                        'label' => 'Comunicaciones',
                        'actions' => array(
                            'list' => array('label' => 'Seleccionando destinatarios', 'item' => false),
                            'edit' => array('label' => 'Escribiendo contenido', 'item' => false),
                            'send' => array('label' => 'Comunicación enviada', 'item' => false)
                        ),
                        'filters' => array('booka'=>'', 'type'=>'', 'status'=>'-1', 'method'=>'', 'interest'=>'', 'role'=>'', 'name'=>'', 'workshopper'=>'', 'lang'=>'es')
                    ),
                    'newsletter' => array(
                        'label' => 'Boletín',
                        'actions' => array(
                            'list' => array('label' => 'Estado del envío automático', 'item' => false),
                            'init' => array('label' => 'Iniciando un nuevo boletín', 'item' => false),
                            'init' => array('label' => 'Viendo listado completo', 'item' => true)
                        )
                    ),
                    'pages' => array(
                        'label' => 'Páginas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Página', 'item' => true),
                            'add' => array('label' => 'Nueva Página', 'item' => true)
                        )
                    ),
                    'promote' => array(
                        'label' => 'Bookas destacados',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Destacado', 'item' => false),
                            'edit' => array('label' => 'Editando Destacado', 'item' => true)
                        )
                    ),
                    'rewards' => array(
                        'label' => 'Tipos recompensas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Tipo de recompensa', 'item' => false),
                            'edit' => array('label' => 'Editando Tipo de recompensa', 'item' => true)
                        )
                    ),
                    'sended' => array(
                        'label' => 'Historial envíos',
                        'actions' => array(
                            'list' => array('label' => 'Emails enviados', 'item' => false)
                        ),
                        'filters' => array('user'=>'', 'template'=>'')
                    ),
                    'tags' => array(
                        'label' => 'Tags de blog',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Tag', 'item' => false),
                            'edit' => array('label' => 'Editando Tag', 'item' => true)
                        )
                    ),
                    'templates' => array(
                        'label' => 'Plantillas de email',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Plantilla', 'item' => true)
                        )
                    ),
                    'texts' => array(
                        'label' => 'Textos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Texto', 'item' => true)
                        ),
                        'filters' => array('group'=>'', 'text'=>'')
                    ),
                    'users' => array(
                        'label' => 'Usuarios',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add' => array('label' => 'Creando Usuario', 'item' => true),
                            'edit' => array('label' => 'Editando Usuario', 'item' => true),
                            'manage' => array('label' => 'Gestionando Usuario', 'item' => true),
                            'impersonate' => array('label' => 'Suplantando al Usuario', 'item' => true)
                        ),
                        'filters' => array('status'=>'active', 'interest'=>'', 'role'=>'', 'id'=>'', 'name'=>'', 'order'=>'', 'booka'=>'', 'type' => '')
                    )
                );

        public function index ($option = 'index', $action = 'list', $id = null, $subaction = null) {
            if ($option == 'index') {
                $BC = self::menu(array('option'=>$option, 'action'=>null, 'id' => null));
                define('ADMIN_BCPATH', $BC);
                return new View('view/admin/index.html.php');
            } else {
                $BC = self::menu(array('option'=>$option, 'action'=>$action, 'id' => $id));
                define('ADMIN_BCPATH', $BC);
                $SubC = 'Base\Controller\Admin' . \chr(92) . \ucfirst($option);
                return $SubC::process($action, $id, self::setFilters($option), $subaction);
            }
        }

        /*
         *  historial de emails enviados
         *
        public function sended($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Sended::process($action, $id, self::setFilters(__FUNCTION__));
        }
        */
        
        /*
         * Menu de secciones, opciones, acciones y config para el panel Admin
         */
        public static function menu($BC = array()) {

            $admin_label = 'Portada';

            $options = self::$options;
            $menu = null;

            // El menu del panel admin dependerá del rol del usuario que accede
            $autorized = array('root', 'superadmin', 'admin', 'director', 'vip-blog', 'vip-booka');
            foreach ($_SESSION['user']->roles as $rol=>$rolName) {
                if (in_array($rol, $autorized)) {
                    $menu = self::setMenu($rol, $_SESSION['user']->id);
                    break;
                }
            }
            
            if (!isset($menu)) {
                // ningun otro rol está autorizadoa  estar aqui
                Advice::Error('Usted no debería haber accedido al panel de admin');
                throw new Redirection('/user/logout');
            }

            // si el breadcrumbs no es un array vacio,
            // devolveremos el contenido html para pintar el camino de migas de pan
            // con enlaces a lo anterior
            if (empty($BC)) {
                return $menu;
            } else {

                // a ver si puede estar aqui!
                if ($BC['option'] != 'index') {
                    $puede = false;
                    foreach ($menu as $sCode=>$section) {
                        if (isset($section['options'][$BC['option']])) {
                            $puede = true;
                            break;
                        }
                    }

                    if (!$puede) {
                        Advice::Error('No tienes permisos para acceder a <strong>'.$options[$BC['option']]['label'].'</strong>');
                        throw new Redirection('/admin');
                    }
                }

                // Los últimos serán los primeros
                $path = '';
                
                // si el BC tiene Id, accion sobre ese registro
                // si el BC tiene Action
                if (!empty($BC['action']) && $BC['action'] != 'list') {

                    // si es una accion no catalogada, mostramos la lista
                    if (!in_array(
                            $BC['action'],
                            array_keys($options[$BC['option']]['actions'])
                        )) {
                        $BC['action'] = '';
                        $BC['id'] = null;
                    }

                    $action = $options[$BC['option']]['actions'][$BC['action']];
                    // si es de item , añadir el id (si viene)
                    if ($action['item'] && !empty($BC['id'])) {
                        $path = " &gt; <strong>{$action['label']}</strong> {$BC['id']}";
                    } else {
                        $path = " &gt; <strong>{$action['label']}</strong>";
                    }
                }

                // si el BC tiene Option, enlace a la portada de esa gestión (a menos que sea laaccion por defecto)
                if (!empty($BC['option']) && isset($options[$BC['option']])) {
                    $option = $options[$BC['option']];
                    if ($BC['action'] == 'list') {
                        $path = " &gt; <strong>{$option['label']}</strong>";
                    } else {
                        $path = ' &gt; <a href="/admin/'.$BC['option'].'">'.$option['label'].'</a>'.$path;
                    }
                }

                // si el BC tiene section, facil, enlace al admin
                if ($BC['option'] == 'index') {
                    $path = "<strong>{$admin_label}</strong>";
                } else {
                    $path = '<a href="/admin">'.$admin_label.'</a>' . $path;
                }

                return $path;
            }


        }

        /*
         * Si no tenemos filtros para este gestor los cogemos de la sesion
         */
        private static function setFilters($option) {

            // arary de fltros para el sub controlador
            $filters = array();

            if (isset($_GET['reset']) &&  $_GET['reset'] == 'filters') {
                unset($_SESSION['admin_filters'][$option]);
                foreach (self::$options[$option]['filters'] as $field=>$default) {
                    $filters[$field] = $default;
                }
                return $filters;
            }

            // si hay algun filtro
            $filtered = false;

            // filtros de este gestor:
            // para cada uno tenemos el nombre del campo y el valor por defecto
            foreach (self::$options[$option]['filters'] as $field=>$default) {
                if (isset($_GET[$field])) {
                    // si lo tenemos en el get, aplicamos ese a la sesión y al array
                    $filters[$field] = (string) $_GET[$field];
                    $_SESSION['admin_filters'][$option][$field] = (string) $_GET[$field];
                    $filtered = true;
                } elseif (!empty($_SESSION['admin_filters'][$option][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['admin_filters'][$option][$field];
                    $filtered = true;
                } elseif (empty($filters[$field])) {
                    // si no tenemos en sesion, ponemos el valor por defecto
                    $filters[$field] = $default;
                }
            }

            if ($filtered) {
                $filters['filtered'] = 'yes';
            }

            return $filters;
        }

        /*
         * Diferentes menus para diferentes perfiles
         */
        public static function setMenu($role, $user = null) {

            $options = self::$options;
            
            switch ($role) {
                // director
                case 'director':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Director de colección',
                            'options' => array (
                                'blog' => $options['blog'],
                                'collections' => $options['collections']
                            )
                        )
                    );
                    break;
                    
                // colaborador Blog
                case 'vip-blog':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Colaborador',
                            'options' => array (
                                'blog' => $options['blog']
                            )
                        )
                    );
                    break;
                    
                // colaborador Booka
                case 'vip-booka':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Colaborador',
                            'options' => array (
                                'bookas' => $options['bookas']
                            )
                        )
                    );
                    break;
                    
                // administrador de bajo nivel
                case 'admin':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Contenidos',
                            'options' => array (
                                'texts' => $options['texts'],
                                'faq' => $options['faq'],
                                'pages' => $options['pages'],
                                'templates' => $options['templates'],
                                'footer' => $options['footer']
                            )
                        ),
                        'bookas' => array(
                            'label'   => 'Bookas',
                            'options' => array (
                                'bookas' => $options['bookas'],
//                                'campaigns' => $options['campaigns'],
                                'invests' => $options['invests']
                            )
                        ),
                        /*
                        'users' => array(
                            'label'   => 'Usuarios',
                            'options' => array (
                                'users'   => $options['users'],
//                                'mailing' => $options['mailing'],
//                                'sended'  => $options['sended']
                            )
                        ),
                         */
                        'home' => array(
                            'label'   => 'Portada',
                            'options' => array (
                                'blog'    => $options['blog'],
                                'promote' => $options['promote']
                            )
                        ),
                        'aux' => array(
                            'label'   => 'Auxiliares',
                            'options' => array (
                                'categories'  => $options['categories'],
                                'collections' => $options['collections'],
                                'rewards'     => $options['rewards'],
                                'tags'        => $options['tags']
                            )
                        ),
                        /*
                        'services' => array(
                            'label'   => 'Servicios',
                            'options' => array (
                                'newsletter' => $options['newsletter']
                            )
                        )
                         */
                    );
                    break;
                
                // super administrador de la plataforma
                case 'superadmin':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Contenidos',
                            'options' => array (
                                'texts' => $options['texts'],
                                'faq' => $options['faq'],
                                'pages' => $options['pages'],
                                'templates' => $options['templates'],
                                'footer' => $options['footer']
                            )
                        ),
                        'bookas' => array(
                            'label'   => 'Bookas',
                            'options' => array (
                                'bookas' => $options['bookas'],
                                'promote' => $options['promote'],
                                'invests' => $options['invests']
                            )
                        ),
                        'campaigns' => array(
                            'label'   => 'Seguimiento',
                            'options' => array (
                                'campaigns' => $options['campaigns'],
                                'accounts' => $options['accounts']
                            )
                        ),
                        'users' => array(
                            'label'   => 'Usuarios',
                            'options' => array (
                                'users'   => $options['users'],
//                                'mailing' => $options['mailing'],
//                                'sended'  => $options['sended']
                            )
                        ),
                        'home' => array(
                            'label'   => 'Portada',
                            'options' => array (
                                'blog'    => $options['blog'],
                                'promote' => $options['promote']
                            )
                        ),
                        'aux' => array(
                            'label'   => 'Auxiliares',
                            'options' => array (
                                'categories'  => $options['categories'],
                                'collections' => $options['collections'],
//                                'costs'       => $options['costs'],
                                'rewards'     => $options['rewards'],
                                'tags'        => $options['tags']
                            )
                        ),
                        /*
                        'services' => array(
                            'label'   => 'Servicios',
                            'options' => array (
                                'newsletter' => $options['newsletter']
                            )
                        )
                         */
                    );
                    break;
                
                // implementador
                case 'root':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Contenidos',
                            'options' => array (
                                'texts' => $options['texts'],
                                'faq' => $options['faq'],
                                'pages' => $options['pages'],
                                'templates' => $options['templates'],
                                'footer' => $options['footer']
                            )
                        ),
                        'bookas' => array(
                            'label'   => 'Bookas',
                            'options' => array (
                                'bookas' => $options['bookas'],
                                'campaigns' => $options['campaigns'],
                                'accounts' => $options['accounts'],
                                'invests' => $options['invests']
                            )
                        ),
                        'users' => array(
                            'label'   => 'Usuarios',
                            'options' => array (
                                'users'   => $options['users'],
                                'mailing' => $options['mailing'],
                                'sended'  => $options['sended']
                            )
                        ),
                        'home' => array(
                            'label'   => 'Portada',
                            'options' => array (
                                'blog'    => $options['blog'],
                                'promote' => $options['promote']
                            )
                        ),
                        'aux' => array(
                            'label'   => 'Auxiliares',
                            'options' => array (
                                'categories'  => $options['categories'],
                                'collections' => $options['collections'],
                                'costs'       => $options['costs'],
                                'rewards'     => $options['rewards'],
                                'tags'        => $options['tags']
                            )
                        ),
                        'services' => array(
                            'label'   => 'Servicios',
                            'options' => array (
                                'newsletter' => $options['newsletter']
                            )
                        )
                    );
                    break;
            }

            return $menu;
        }


	}

}
