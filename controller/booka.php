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
        Base\Core\View,
        Base\Library\Text,
        Base\Library\Mail,
        Base\Library\Template,
        Base\Library\Advice,
        Base\Model;

    class  Booka extends \Base\Core\Controller {

        private $hash = '';
        
        public function index($id = null, $show = 'home', $post = null) {
            if ($id !== null) {
                return $this->view($id, $show, $post);
            } else if (isset($_GET['create'])) {
                throw new Redirection("/booka/create");
            } else {
                throw new Redirection("/admin");
            }
        }

        public function raw ($id) {
            $booka = Model\Booka::get($id);
            $booka->check();
            \trace($booka);
            die;
        }

        public function delete ($id) {
            $booka = Model\Booka::get($id);
            $errors = array();
            if ($booka->delete($errors)) {
//                Advice::Info("Has borrado los datos del proyecto '<strong>{$booka->name}</strong>' correctamente");
            } else {
                Advice::Info("No se han podido borrar los datos del proyecto '<strong>{$booka->name}</strong>'. Error:" . implode(', ', $errors));
            }
                throw new Redirection("/admin");
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        public function edit ($id) {
            $booka = Model\Booka::get($id, null);

            if ($booka->status != 1 && !ACL::check('/booka/edit/todos')) {
                Advice::Error('La edición del proyecto está cerrada');
                throw new Redirection("/admin");
                 
            } else {
                // todos los pasos, entrando en descripcion por defecto
                $step = 'overview';

                $steps = array('overview', 'milestones', 'costs', 'rewards');
            }
            
//            if ($_SERVER['REQUEST_METHOD'] === 'POST') echo \trace($_POST);
            
            foreach ($_REQUEST as $k=>$v) {                
                if (strncmp($k, 'view-step-', 10) === 0 && in_array(substr($k, 10), $steps)) {
//                    echo 'encuentra que: '.$k . '<br />';
                    $step = substr($k, 10);
                }                
            }

//            echo 'step: '.$step;
//            die;
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id) {
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$booka, &$errors))) {
                        $booka->process_step = $id;
                    }
                }

                // guardamos los datos que hemos tratado
                $booka->save($errors);

                if (!empty($errors)) {
                    Advice::Error(implode('<br />', $errors));
                }
                
                if (isset($_POST['save-exit'])) {
                    throw new Redirection("/admin/bookas");
                }
                
                // si estan enviando el proyecto a revisión
                if (isset($_POST['process_review']) && isset($_POST['finish'])) {
                    $errors = array();
                    if ($booka->ready($errors)) {
                        Advice::Info('Se ha enviado a revisión para que el admin le de el visto bueno final');

                        // email al admin
                        /*
                        $mailHandler = new Mail();

                        $mailHandler->to = \CONF_MAIL;
                        $mailHandler->toName = 'Revisor de Bookas';
                        $mailHandler->subject = 'Proyecto ' . $booka->name . ' enviado a valoración';
                        $mailHandler->content = '<p>Han enviado un nuevo proyecto a revisión</p><p>El nombre del proyecto es: <span class="message-highlight-blue">'.$booka->name.'</span> <br />y se puede ver en <span class="message-highlight-blue"><a href="'.SITE_URL.'/booka/'.$booka->id.'">'.SITE_URL.'/booka/'.$booka->id.'</a></span></p>';
                        $mailHandler->fromName = "{$booka->user->name}";
                        $mailHandler->from = $booka->user->email;
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Advice::Info(Text::get('booka-review-request_mail-success'));
                        } else {
                            Advice::Error(Text::get('booka-review-request_mail-fail'));
                            Advice::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);
                         *
                         *
                        // email al autor
                         * 
                         * NO HAY MAIL AL AUTOR porque es la misma plataforma
                         *
                         *
                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(8);

                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $booka->name, $template->title);

                        // En el contenido:
                        $search  = array('%USERNAME%', '%PROJECTNAME%');
                        $replace = array($booka->user->name, $booka->name);
                        $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Mail();

                        $mailHandler->to = $booka->user->email;
                        $mailHandler->toName = $booka->user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            Advice::Info(Text::get('booka-review-confirm_mail-success'));
                        } else {
                            Advice::Error(Text::get('booka-review-confirm_mail-fail'));
                            Advice::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);
                        */

                        if (isset($_SESSION['user']->roles['superadmin'])) {
                            throw new Redirection("/admin");
                        } else {
                            throw new Redirection("/dashboard");
                        }
                    } else {
                        Advice::Error(implode('<br />', $errors));
                    }
                }

                if (!empty($this->hash)) {
                    throw new Redirection("/booka/edit/{$booka->id}#{$this->hash}");
                }

            }

            //re-evaluar el proyecto
//            $booka->check();
            
            // variables para la vista
            $viewData = array(
                'booka' => $booka,
                'steps' => $steps,
                'step' => $step
            );


            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                case 'overview':
                    $viewData['categories'] = Model\Booka\Category::getAll();
                    $viewData['collections'] = Model\Collection::getList();
                    break;

                case 'milestones':
                    break;

                case 'costs':
                    $viewData['stages'] = Model\Booka\Cost::stages();
                    $viewData['types'] = Model\Booka\Cost\Type::getAll();
                    
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/cost-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }
                        
                        if (!empty($_POST['cost-add'])) {
                            $last = end($booka->costs);
                            if ($last !== false) {
                                $viewData["cost-{$last->id}-edit"] = true;
                            }
                        }
                    }
                    break;

                case 'rewards':
                    $viewData['types'] = Model\Booka\Reward\Type::getAll();
                    
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/reward-(\d+)-edit/', $k, $r)) {                                
                                $viewData[$k] = true;
                            }                            
                        }
                        
                        if (!empty($_POST['reward-add'])) {
                            $last = end($booka->rewards);
                            if ($last !== false) {
                                $viewData["reward-{$last->id}-edit"] = true;
                            }
                        }
                    }

                    
                    break;

                case 'translate':
                    // paso de traducción de textos del proyecto
                    break;

                case 'review':
                    // quizás se ponga luego
                    break;
            }

            $view = new View (
                "view/booka/edit.html.php",
                $viewData
            );

            return $view;

        }

        public function create () {

            if (empty($_SESSION['user'])) {
                $_SESSION['jumpto'] = '/booka/create';
                Advice::Info(Text::get('user-login-required-to_create'));
                throw new Redirection("/user/login");
            }

            if ($_POST['action'] != 'continue' || $_POST['confirm'] != 'true') {
                throw new Redirection("/about/howto");
            }

            $errors = array();

            $input = array();
            if (isset($_POST['id'])) {
                $input['id'] = Model\Booka::idealiza($_POST['id']);
            }

            $booka = new Model\Booka;
            if ($booka->create($input, $errors)) {
                throw new Redirection("/booka/edit/{$booka->id}");
            } else {
                Advice::Error(implode('<br />', $errors));
                throw new Redirection("/admin/bookas/add");
            }

            throw new \Base\Core\Exception('Fallo al crear un nuevo proyecto');
        }

        private function view ($id, $show, $post = null) {
            
            // -- ojo a los usuarios publicos
            if (empty($_SESSION['user'])) {
                // --- loguearse para aportar
                if ($show == 'invest') {
                    $_SESSION['jumpto'] = '/booka/' .  $id . '/invest';
                    if (isset($_GET['amount'])) {
                        $_SESSION['jumpto'] .= '?amount='.$_GET['amount'];
                    }
                    Advice::Info(Text::get('user-login-required-to_invest'));
                    throw new Redirection("/user/login");
                } elseif ($show != 'home') {
                    $_SESSION['jumpto'] = '/booka/' .  $id . '/' . $show;
                    Advice::Info(Text::get('user-login-required-to_see'));
                    throw new Redirection('/booka/' .  $id);
                }
            }

            
            
            
            $booka = Model\Booka::get($id);

            $types = Model\Booka\Reward\Type::getAll();
            
            // recompensas
            foreach ($booka->rewards as &$reward) {
                
                if ($reward->type == 9999) {
                    $reward->name = $reward->other_text;
                } else {
                    $reward->name = $types[$reward->type]->name;
                }
                
                $reward->none = false;
                $reward->taken = $reward->getTaken(); // cofinanciadores quehan optado por esta recompensas
                // si controla unidades de esta recompensa, mirar si quedan
                if ($reward->units > 0 && $reward->taken >= $reward->units) {
                    $reward->none = true;
                }
            }


            // solamente se puede ver publicamente si
            // - es el dueño
            // - es un admin con permiso
            // - es otro usuario y el proyecto esta available: en campaña, financiado, retorno cumplido o caducado (que no es desechado)
            if (($booka->status > 2) ||
                $booka->owner == $_SESSION['user']->id ||
                isset($_SESSION['user']->roles['superadmin']) ||
                isset($_SESSION['user']->roles['admin']) ||
                isset($_SESSION['user']->roles['vip-booka']) ||
                (isset($_SESSION['user']->roles['director']) && $booka->collection == $_SESSION['user']->collection)
                ) {
                // lo puede ver

                $viewData = array(
                        'booka' => $booka,
                        'show' => $show
                    );

                if ($show == 'needs') {
                    $viewData['stages'] = Model\Booka\Cost::stages();
                }
                
                //tenemos que tocar esto un poquito para gestionar los pasos al aportar
                if ($show == 'invest') {

                    // si no está en campaña no pueden estar aqui ni de coña
                    if ($booka->status != 3) {
                        Advice::Info(Text::get('booka-invest-closed'));
                        throw new Redirection('/booka/'.$id, Redirection::TEMPORARY);
                    }

                    if (isset($_GET['confirm'])) {
                        if (\in_array($_GET['confirm'], array('ok', 'fail'))) {
                            $invest = $_GET['confirm'];
                        } else {
                            $invest = 'start';
                        }
                    } else {
                        $invest = 'start';
                    }
                    $viewData['invest'] = $invest;
                    $viewData['personal'] = Model\User::getPersonal($_SESSION['user']->id);
                }

                if ($show == 'messages' && $booka->status < 3) {
                    Advice::Info(Text::get('booka-messages-closed'));
                }

                return new View('view/booka/view.html.php', $viewData);

            } else {
                // no lo puede ver
                Advice::Info(Text::get('booka-not_public'));
                throw new Redirection("/");
            }
        }

        public function buy ($id) {
            $booka = Model\Booka::getMedium($id);
            return new View('view/booka/buy.html.php', array('booka'=>$booka));
        }

        public function read ($id) {
            $booka = Model\Booka::getMedium($id);
            return new View('view/booka/read.html.php', array('booka'=>$booka));
        }



        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/booka
        // primero añadir y luego quitar para que no se pisen los indices
        // En vez del hidden step, va a comprobar que esté definido en el post el primer campo del proceso
        //-----------------------------------------------
        /*
         * Paso 1 - DESCRIPCIÓN
         */
        private function process_overview(&$booka, &$errors) {
            if (!isset($_POST['process_overview'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'name_es',
                'collection',
                'author',
                'info_es',
                'description_es',
                'motivation_es',
                'about_es',
                'goal_es',
                'related_es',
                'keywords_es',
                'caption_es',
                // y la traducción
                'name_en',
                'info_en',
                'description_en',
                'motivation_en',
                'about_en',
                'goal_en',
                'related_en',
                'keywords_en',
                'caption_en'
            );

            foreach ($fields as $field) {
                $booka->$field = $_POST[$field];
            }
            
            // tratar la imagen que suben
            if(!empty($_FILES['image_upload']['name'])) {
                $booka->image = $_FILES['image_upload'];
                $this->hash = 'gallery';
            }

            // tratar las imagenes que quitan
            foreach ($booka->gallery as $key=>$image) {
                if (!empty($_POST["gallery-{$image->id}-remove"])) {
                    $image->remove('booka');
                    unset($booka->gallery[$key]);
                    $this->hash = 'gallery';
                }
            }

            // IMAGENES DE PROCESO (2)
            // tratar la imagen que suben
            if(!empty($_FILES['image2_upload']['name'])) {
                $booka->image2 = $_FILES['image2_upload'];
                $this->hash = 'gallery2';
            }

            // tratar las imagenes que quitan
            foreach ($booka->gallery2 as $key2=>$image2) {
                if (!empty($_POST["gallery2-{$image2->id}-remove"])) {
                    $image2->remove('booka2');
                    unset($booka->gallery2[$key2]);
                    $this->hash = 'gallery2';
                }
            }

            //categorias
            // añadir las que vienen y no tiene
            $tiene = $booka->categories;
            if (isset($_POST['categories'])) {
                $viene = $_POST['categories'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $category = new Model\Booka\Category(array('id'=>$cat,'booka'=>$booka->id));
                $booka->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($booka->categories[$key]);
            }

            $quedan = $booka->categories; // truki para xdebug

            return true;
        }

        /*
         * Paso 2 - OBJETIVOS
         */
        private function process_milestones(&$booka, &$errors) {
            if (!isset($_POST['process_milestones'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'milestone1_es',
                'milestone2_es',
                'milestone3_es',
                'milestone4_es',
                'media_es',
                'media_usubs',
                'media_caption_es',
                // y la traducción
                'milestone1_en',
                'milestone2_en',
                'milestone3_en',
                'milestone4_en',
                'media_en',
                'media_caption_en'
            );

            foreach ($fields as $field) {
                $booka->$field = $_POST[$field];
            }
            
            // Media
            if (!empty($booka->media_es)) {
                $booka->media_es = new Model\Booka\Media($booka->media_es);
            }

            return true;
        }

        /*
         * Paso 3 - COSTES
         */
        private function process_costs(&$booka, &$errors) {
            if (!isset($_POST['process_costs'])) {
                return false;
            }

            //tratar costes existentes
            foreach ($booka->costs as $key => $cost) {
                
                if (!empty($_POST["cost-{$cost->id}-remove"])) {
                    unset($booka->costs[$key]);
                    continue;
                }

                if (isset($_POST['cost-' . $cost->id . '-cost'])) {
                    $cost->cost_es = $_POST['cost-' . $cost->id . '-cost_es'];
                    $cost->cost_en = $_POST['cost-' . $cost->id . '-cost_en'];
                    $cost->description_es = $_POST['cost-' . $cost->id .'-description_es'];
                    $cost->description_en = $_POST['cost-' . $cost->id .'-description_en'];
                    $cost->amount = $_POST['cost-' . $cost->id . '-amount'];
                    $cost->stage = $_POST['cost-' . $cost->id . '-stage'];
                    $cost->type = $_POST['cost-' . $cost->id . '-type'];
                    $cost->date = $_POST['cost-' . $cost->id . '-date'];
                }
            }

            //añadir nuevo coste
            if (!empty($_POST['cost-new-add'])) {
                
                $booka->costs[] = new Model\Booka\Cost(array(
                    'booka' => $booka->id,
                    'cost_es'  => $_POST['cost-new-cost_es'],
                    'cost_en'  => $_POST['cost-new-cost_en'],
                    'description_es'  => $_POST['cost-new-description_es'],
                    'description_en'  => $_POST['cost-new-description_en'],
                    'amount' => $_POST['cost-new-amount'],
                    'stage' => $_POST['cost-new-stage'],
                    'type'  => $_POST['cost-new-type'],
                    'date' => $_POST['cost-new-date']
                ));
            }
           
            return true;
        }

        /*
         * Paso 4 - RECOMPENSAS
         */
        private function process_rewards(&$booka, &$errors) {
            if (!isset($_POST['process_rewards'])) {
                return false;
            }

            //tratar recompensas
            foreach ($booka->rewards as $k => $reward) {
                
                if (!empty($_POST["reward-{$reward->id}-remove"])) {
                    unset($booka->rewards[$k]);
                    continue;
                }

                if (isset($_POST['reward-' . $reward->id . '-reward'])) {
                    $reward->reward_es = $_POST['reward-' . $reward->id . '-reward_es'];
                    $reward->reward_en = $_POST['reward-' . $reward->id . '-reward_en'];
                    $reward->description_es = $_POST['reward-' . $reward->id . '-description_es'];
                    $reward->description_en = $_POST['reward-' . $reward->id . '-description_en'];
                    $reward->type = $_POST['reward-' . $reward->id . '-type'];
                    $reward->amount = $_POST['reward-' . $reward->id . '-amount'];
                    $reward->units = $_POST['reward-' . $reward->id . '-units'];
                    $reward->other_text_es = $_POST['reward-' . $reward->id . '-type'] == 9999 ? $_POST['reward-' . $reward->id . '-other_text_es'] : '';
                    $reward->other_text_en = $_POST['reward-' . $reward->id . '-type'] == 9999 ? $_POST['reward-' . $reward->id . '-other_text_en'] : '';
                }
                
            }

            // tratar nuevos retornos
            if (!empty($_POST['reward-new-add'])) {
                $booka->rewards[] = new Model\Booka\Reward(array(
                    'booka' => $booka->id,
                    'reward_es' => $_POST['reward-new-reward_es'],
                    'reward_en' => $_POST['reward-new-reward_en'],
                    'description_es' => $_POST['reward-new-description_es'],
                    'description_en' => $_POST['reward-new-description_en'],
                    'type' => $_POST['reward-new-type'],
                    'amount' => $_POST['reward-new-amount'],
                    'units' => $_POST['reward-new-units'],
                    'other_text_es' => $_POST['reward-new-type'] == 9999 ? $_POST['reward-new-other_text_es'] : '',
                    'other_text_en' => $_POST['reward-new-type'] == 9999 ? $_POST['reward-new-other_text_en'] : ''
                ));
            }

            return true;
            
        }

        /*
         * Paso 6 - Traduccion
         */
         private function process_translate(&$booka, &$errors) {
            if (!isset($_POST['process_translate'])) {
                return false;
            }

            // campos que guarda este paso
            // la descripcion en ingles
            $fields = array(
            );

            foreach ($fields as $field) {
                $booka->$field = $_POST[$field];
            }

            // Media
            if (!empty($booka->media_en)) {
                $booka->media_en = new Model\Booka\Media($booka->media_en);
            }

            return true;
        }

        /*
         * Paso 7 - REVIEW
         */
        private function process_review(&$booka) {
            if (!isset($_POST['process_review'])) {
                return false;
            }

            $booka->comment = $_POST['comment'];

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}