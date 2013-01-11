<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci�n Fuentes Abiertas (see README for details)
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
        Base\Model,
        Base\Library\Feed,
        Base\Library\Mail,
        Base\Library\Template,
        Base\Library\Text;

    class Message extends \Base\Core\Controller {

        public function index ($booka = null) {
            if (empty($booka))
                throw new Redirection('/', Redirection::PERMANENT);

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                $bookaData = Model\Booka::getMedium($booka);

                if ($bookaData->status < 3) {
                    \Base\Library\Advice::Error(Text::get('booka-messages-closed'));
                    throw new Redirection("/booka/{$booka}");
                }

                $message = new Model\Message(array(
                    'user' => $_SESSION['user']->id,
                    'booka' => $booka,
                    'message' => $_POST['message']
                ));

                if ($message->save($errors)) {
                    
                    // Evento Feed
                    $log = new Feed();
                    $log_html = Text::get('feed-booka-message',
                        Feed::item('booka', $bookaData->clr_name, $bookaData->id.'/messages#comment'.$message->id)
                    );
                    $log->populate($_SESSION['user']->name, '/user/profile/'.$_SESSION['user']->id, $log_html, $_SESSION['user']->avatar->id);
                    $log->setTarget($bookaData->id, 'booka');
                    $log->doPublic('users');
                    unset($log);

                    /*
                    // mensaje al autor del proyecto
                    // Obtenemos la plantilla para asunto y contenido
                    $template = Template::get(30);

                    // Sustituimos los datos
                    $subject = str_replace('%bookaNAME%', $bookaData->name, $template->title);

                    $response_url = SITE_URL . '/user/profile/' . $_SESSION['user']->id . '/message';
                    $booka_url = SITE_URL . '/booka/' . $bookaData->id . '/messages#message'.$message->id;

                    $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%bookaNAME%', '%bookaURL%', '%RESPONSEURL%');
                    $replace = array($_POST['message'], $bookaData->user->name, $_SESSION['user']->name, $bookaData->name, $booka_url, $response_url);
                    $content = \str_replace($search, $replace, $template->text);

                    $mailHandler = new Mail();

                    $mailHandler->to = $bookaData->user->email;
                    $mailHandler->toName = $bookaData->user->name;
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    $mailHandler->send($errors);

                    unset($mailHandler);
                     *
                     */


                }
			}

            throw new Redirection("/booka/{$booka}/messages#comment".$message->id, Redirection::TEMPORARY);
        }

        public function edit ($id, $booka) {

            if (isset($_POST['message'])) {
                $message = Model\Message::get($id);
                $message->user = $message->user->id;
                $message->message = ($_POST['message']);

                $message->save();
            }

            throw new Redirection("/booka/{$booka}/messages", Redirection::TEMPORARY);
        }

        public function delete ($id, $booka) {

            Model\Message::get($id)->delete();

            throw new Redirection("/booka/{$booka}/messages", Redirection::TEMPORARY);
        }

        /*
         * Este metodo envia un mensaje
         */
        public function direct ($booka = null) {
            if (empty($booka))
                throw new Redirection('/', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // verificamos token
                if (!isset($_POST['msg_token']) || $_POST['msg_token']!=$_SESSION['msg_token']) {
//                    throw new Error(Error::BAD_REQUEST);
                    header("HTTP/1.1 418");
                    die('Temporalmente no disponible');
                }

                // sacamos el mail del responsable del proyecto
                $booka = Model\Booka::getMini($booka);
                $ownerData = Model\User::getMini($booka->owner);

                $msg_content = \nl2br(\strip_tags($_POST['message']));

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(3);

                // Sustituimos los datos
                // En el asunto: %bookaNAME% por $booka->name
                $subject = str_replace('%bookaNAME%', $booka->name, $template->title);

                $response_url = SITE_URL . '/user/profile/' . $_SESSION['user']->id . '/message';

                // En el contenido:  nombre del autor -> %OWNERNAME% por $booka->contract_name
                // el mensaje que ha escrito el productor -> %MESSAGE% por $msg_content
                // nombre del usuario que ha aportado -> %USERNAME% por $_SESSION['user']->name
                // nombre del proyecto -> %bookaNAME% por $booka->name
                // url de la plataforma -> %SITEURL% por SITE_URL
                $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%bookaNAME%', '%SITEURL%', '%RESPONSEURL%');
                $replace = array($msg_content, $ownerData->name, $_SESSION['user']->name, $booka->name, SITE_URL, $response_url);
                $content = \str_replace($search, $replace, $template->text);
                
                $mailHandler = new Mail();

                $mailHandler->to = $ownerData->email;
                $mailHandler->toName = $ownerData->name;
                // blind copy a Base desactivado durante las verificaciones
//              $mailHandler->bcc = 'comunicaciones@Base.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send($errors)) {
                    // ok
//                    \Base\Library\Advice::Info(Text::get('regular-message_success'));
                } else {
                    \Base\Library\Advice::Error(Text::get('regular-message_fail') . '<br />' . implode(', ', $errors));
                }

                unset($mailHandler);
			}

            throw new Redirection("/booka/{$booka->id}/messages", Redirection::TEMPORARY);
        }

        /*
         * Este metodo envia un mensaje personal
         */
        public function personal ($id = null) {
            // verificacion de que esté autorizasdo a enviar mensaje
            /*
            if (!isset($_SESSION['message_autorized']) || $_SESSION['message_autorized'] !== true) {
                \Base\Library\Advice::Info('Temporalmente no disponible. Disculpen las molestias');
                throw new Redirection('/');
            } else {
                // y quitamos esta autorización
                unset($_SESSION['message_autorized']);
            }
             * 
             */

            if (empty($id))
                throw new Redirection('/', Redirection::PERMANENT);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {

                // verificamos token
                if (!isset($_POST['msg_token']) || $_POST['msg_token']!=$_SESSION['msg_token']) {
                    header("HTTP/1.1 418");
                    die('Temporalmente no disponible');
                }

                // sacamos el mail del responsable del proyecto
                $user = Model\User::get($id);

                if (!$user instanceof Model\User) {
                    throw new Redirection('/', Redirection::TEMPORARY);
                }

                $msg_content = \nl2br(\strip_tags($_POST['message']));


                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(4);

                // Sustituimos los datos
                if (isset($_POST['subject']) && !empty($_POST['subject'])) {
                    $subject = $_POST['subject'];
                } else {
                    // En el asunto por defecto: %USERNAME% por $_SESSION['user']->name
                    $subject = str_replace('%USERNAME%', $_SESSION['user']->name, $template->title);
                }

                $remite = $_SESSION['user']->name . ' ' . Text::get('regular-from') . ' ';
                $remite .= CONF_MAIL_NAME;
                
                $response_url = SITE_URL . '/user/profile/' . $_SESSION['user']->id . '/message';
                $profile_url = SITE_URL."/user/profile/{$user->id}/sharemates";
                // En el contenido:  nombre del destinatario -> %TONAME% por $user->name
                // el mensaje que ha escrito el usuario -> %MESSAGE% por $msg_content
                // nombre del usuario -> %USERNAME% por $_SESSION['user']->name
                // url del perfil -> %PROFILEURL% por ".SITE_URL."/user/profile/{$user->id}/sharemates"
                $search  = array('%MESSAGE%','%TONAME%',  '%USERNAME%', '%PROFILEURL%', '%RESPONSEURL%');
                $replace = array($msg_content, $user->name, $_SESSION['user']->name, $profile_url, $response_url);
                $content = \str_replace($search, $replace, $template->text);


                $mailHandler = new Mail();
                $mailHandler->fromName = $remite;
                $mailHandler->to = $user->email;
                $mailHandler->toName = $user->name;
                // blind copy a Base desactivado durante las verificaciones
//                $mailHandler->bcc = 'comunicaciones@Base.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send($errors)) {
                    // ok
//                    \Base\Library\Advice::Info(Text::get('regular-message_success'));
                } else {
                    \Base\Library\Advice::Error(Text::get('regular-message_fail') . '<br />' . implode(', ', $errors));
                }

                unset($mailHandler);
			}

            throw new Redirection("/user/profile/{$id}", Redirection::TEMPORARY);
        }

        /*
         * Metodo para publicar un comentario en un post
         */
        public function post ($post, $booka = null) {

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
                $comment = new Model\Post\Comment(array(
                    'user' => $_SESSION['user']->id,
                    'post' => $post,
                    'date' => date('Y-m-d H:i:s'),
                    'text' => $_POST['message']
                ));

                if ($comment->save($errors)) {
                    // a ver los datos del post
                    $postData = Model\Post::get($post);

                    // Evento Feed
                    $log = new Feed();
                    $log_html = Text::get('feed-new_comment', array(
                        Feed::item('comment', 'comentario', $postData->id.'#comment'.$comment->id),
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id)
                    ));
                    $log->populate($postData->title, '/blog/'.$postData->id, $log_html, $postData->image->id);
                    $log->setTarget($_SESSION['user']->id, 'user');
                    $log->doPublic('community');
                    unset($log);

                    //Notificación al autor del proyecto
                    // Obtenemos la plantilla para asunto y contenido
                    /*
                    $template = Template::get(31);

                    // Sustituimos los datos
                    $subject = str_replace('%bookaNAME%', $bookaData->name, $template->title);

                    $response_url = SITE_URL . '/user/profile/' . $_SESSION['user']->id . '/message';
                    $booka_url = SITE_URL . '/booka/' . $bookaData->id . '/updates/'.$postData->id.'#comment'.$comment->id;

                    $search  = array('%MESSAGE%', '%OWNERNAME%', '%USERNAME%', '%bookaNAME%', '%bookaURL%', '%RESPONSEURL%');
                    $replace = array($_POST['message'], $bookaData->user->name, $_SESSION['user']->name, $bookaData->name, $booka_url, $response_url);
                    $content = \str_replace($search, $replace, $template->text);

                    $mailHandler = new Mail();

                    $mailHandler->to = $bookaData->user->email;
                    $mailHandler->toName = $bookaData->user->name;
                    $mailHandler->subject = $subject;
                    $mailHandler->content = $content;
                    $mailHandler->html = true;
                    $mailHandler->template = $template->id;
                    $mailHandler->send($errors);

                    unset($mailHandler);
                    */
                    
                } else {
                    // error
                }
			}

            if (!empty($booka)) {
                throw new Redirection("/booka/{$booka}/updates/{$post}#comment".$comment->id, Redirection::TEMPORARY);
            } else {
                throw new Redirection("/blog/{$post}#comment".$comment->id, Redirection::TEMPORARY);
            }
        }

    }

}