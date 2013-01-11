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

    use Base\Library\Page,
        Base\Core\Redirection,
        Base\Core\View,
        Base\Library\Text,
        Base\Library\Advice,            
        Base\Library\Mail,
        Base\Library\Template;

    class Contact extends \Base\Core\Controller {
        
        public function index () {

             $page = Page::get('contact');

                $errors = array();

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {

                    // si falta mensaje, email o asunto, error
                    if(empty($_POST['email'])) {
                        $errors['email'] = Text::get('error-contact-email-empty');
                    } elseif(!\Base\Library\Check::mail($_POST['email'])) {
                        $errors['email'] = Text::get('error-contact-email-invalid');
                    } else {
                        $email = $_POST['email'];
                    }

                    if(empty($_POST['subject'])) {
                        $errors['subject'] = Text::get('error-contact-subject-empty');
                    } else {
                        $subject = $_POST['subject'];
                    }

                    if(empty($_POST['message'])) {
                        $errors['message'] = Text::get('error-contact-message-empty');
                    } else {
                        $msg_content = \strip_tags($_POST['message']);
                        $msg_content = nl2br($msg_content);
                    }

                    if (empty($errors)) {
                        $data = array(
                                'subject' => $_POST['subject'],
                                'email'   => $_POST['email'],
                                'message' => $_POST['message']
                        );

                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get(1);

                // Sustituimos los datos
                $subject = str_replace('%SUBJECT%', $subject, $template->title);

                // En el contenido:
                $search  = array('%TONAME%', '%MESSAGE%', '%USEREMAIL%');
                $replace = array('Booka', $msg_content, $email);
                $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Mail();

                        $mailHandler->to = \CONF_MAIL;
                        $mailHandler->toName = 'Booka';
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->fromName = '';
                        $mailHandler->from = $email;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            Advice::Info('Tú mensaje se ha enviado correctamente, gracais por contactar con nosotrxs.');
                            $data = array();
                        } else {
                            Advice::Error('No se ha podido enviar el mensaje, inténtalo más tarde.');
                        }

                        unset($mailHandler);
                    } else {
                        Advice::Error(implode('<br />', $errors));
                    }
                }

                if (isset($_GET['email'])) {

                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'confirm':
                                $subject = 'El email asociado a mi cuenta no está confirmado';
                                $message = 'Me dice que el email asociado a mi cuenta no está confirmado... (accediste con un solo click? has mirado en correo no deseado?) (por favor, danos alguna pista)';
                                break;

                            case 'leave':
                                $subject = 'Estaba intentando darme de baja';
                                $message = 'Estaba intentando dar de baja mi cuenta de usuario y ha ocurrido algo... (has mirado en correo no deseado?) (por favor, danos alguna pista)';
                                break;

                            case 'password':
                                $subject = 'Estaba intentando recuperar mi contraseña';
                                $message = 'Estaba intentando recuperar la contraseña de mi cuenta y ha ocurrido algo... (has mirado en correo no deseado?) (por favor, danos alguna pista)';
                                break;

                            case 'email':
                                $subject = 'Estaba intentando cambiar mi email';
                                $message = 'Estaba intentando cambiar el email asociado a mi cuenta de usuario y ha ocurrido algo... (has mirado en correo no deseado?) (por favor, danos alguna pista)';
                                break;

                            default:
                                $subject = 'Estaba intentando '.$_GET['action'];
                                $message = 'Estaba intentando '.$_GET['action'] . ' y ha ocurrido algo inesperado... (por favor completar)';
                                break;
                        }
                    } else {
                        $subject = '';
                        $message = '';
                    }

                    $data = array(
                            'subject' => $subject,
                            'email'   => $_GET['email'],
                            'message' => $message
                    );
                }

                return new View(
                    'view/about/contact.html.php',
                    array(
                        'text'    => $page->text,
                        'content' => $page->content,
                        'data'    => $data
                    )
                );
            
        }
        
    }
    
}