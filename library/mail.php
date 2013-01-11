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

namespace Base\Library {

	use Base\Core\Model,
        Base\Core\Exception,
        Base\Core\View;

    class Mail {

        public
            $from = CONF_MAIL_FROM,
            $fromName = CONF_MAIL_NAME,
            $to = CONF_MAIL_FROM,
            $toName = CONF_MAIL_NAME,
            $subject,
            $content,
            $cc = false,
            $bcc = false,
            $reply = false,
            $replyName,
            $attachments = array(),
            $html = true,
            $template = null;

        /**
         * Constructor.
         */
        function __construct() {
            require_once PHPMAILER_CLASS;
            require_once PHPMAILER_SMTP;
            require_once PHPMAILER_POP3;

            // Inicializa la instancia PHPMailer.
            $mail = new \PHPMailer();

            // Define  el idioma para los mensajes de error.
            $mail->SetLanguage("es", PHPMAILER_LANGS);

            // Define la codificación de caracteres del mensaje.
            $mail->CharSet = "UTF-8";

            // Define el ajuste de texto a un número determinado de caracteres en el cuerpo del mensaje.
            $mail->WordWrap = 50;

            // Define el tipo de gestor de correo
            switch(CONF_MAIL_TYPE) {
                default:
                case "mail":
                    $mail->isMail(); // set mailer to use PHP mail() function.
                    break;
                case "sendmail":
                    $mail->IsSendmail(); // set mailer to use $Sendmail program.
                    break;
                case "qmail":
                    $mail->IsQmail(); // set mailer to use qmail MTA.
                    break;
                case "smtp":
                    $mail->IsSMTP(); // set mailer to use SMTP
                    $mail->SMTPAuth = CONF_MAIL_SMTP_AUTH; // enable SMTP authentication
                    $mail->SMTPSecure = CONF_MAIL_SMTP_SECURE; // sets the prefix to the servier
                    $mail->Host = CONF_MAIL_SMTP_HOST; // specify main and backup server
                    $mail->Port = CONF_MAIL_SMTP_PORT; // set the SMTP port
                    $mail->Username = CONF_MAIL_SMTP_USERNAME;  // SMTP username
                    $mail->Password = CONF_MAIL_SMTP_PASSWORD; // SMTP password
                    break;
            }
            $this->mail = $mail;
        }

        /**
         * Validar mensaje.
         * @param type array	$errors
         */
		public function validate(&$errors = array()) {
		    if(empty($this->to)) {
		        $errors['email'] = 'El mensaje no tiene destinatario.';
		    }
		    if(empty($this->content)) {
		        $errors['content'] = 'El mensaje no tiene contenido.';
		    }
            if(empty($this->subject)) {
                $errors['subject'] = 'El mensaje no tiene asunto.';
            }
            return empty($errors);
		}

		/**
		 * Enviar mensaje.
		 * @param type array	$errors
		 */
        public function send(&$errors = array()) {
            if($this->validate($errors)) {
                $mail = $this->mail;
                try {
                    // Construye el mensaje
                    $mail->From = $this->from;
                    $mail->FromName = $this->fromName;
                    
                    $mail->AddAddress($this->to, $this->toName);
                    if($this->cc) {
                        $mail->AddCC($this->cc);
                    }
                    if($this->bcc) {
                        $mail->AddBCC($this->bcc);
                    }
                    if($this->reply) {
                        $mail->AddReplyTo($this->reply, $this->replyName);
                    }
                    if (!empty($this->attachments)) {
                        foreach ($this->attachments as $attachment) {
                            if (!empty($attachment['filename'])) {
                                $mail->AddAttachment($attachment['filename'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                            } else {
                                $mail->AddStringAttachment($attachment['string'], $attachment['name'], $attachment['encoding'], $attachment['type']);
                            }
                        }
                    }
                    $mail->Subject = $this->subject;
                    if($this->html) {
                        $mail->IsHTML(true);
                        $mail->Body    = $this->bodyHTML();
                        $mail->AltBody = $this->bodyText();

                        // incrustar el logo
                        $mail->AddEmbeddedImage(CONF_PATH . '/booka_logo.png', 'logo', 'Booka', 'base64', 'image/png');
                    }
                    else {
                        $mail->IsHTML(false);
                        $mail->Body    = $this->bodyHTML(true);
                    }

                    // si estoy en entorno local ni lo intento, en vez de eso muestro el email en pantalla
                    if (CONF_LOCAL === true) {
                        if (CONF_DEBUG == true) die($mail->Body);
                        return true;
                    }

                    // Envía el mensaje
                    if ($mail->Send($errors)) {
                        return true;
                    } else {
                        $errors[] = 'Fallo del servidor de correo interno';
                        return false;
                    }

            	} catch(\PDOException $e) {
                    $errors[] = "No se ha podido enviar el mensaje: " . $e->getMessage();
                    return false;
    			} catch(phpmailerException $e) {
    			    $errors[] = $e->getMessage();
    			    return false;
    			}
            }
            return false;
		}

        /**
         * Cuerpo del mensaje en texto plano para los clientes de correo sin formato.
         */
        private function bodyText() {
            return strip_tags($this->content);
        }

        /**
         * Cuerpo del texto en HTML para los clientes de correo con formato habilitado.
         *
         * Se mete el contenido alrededor del diseño de email de Diego
         *
         */
        private function bodyHTML($plain = false) {

            $viewData = array('content' => $this->content);

            // grabamos el contenido en la tabla de envios
            // especial para newsletter, solo grabamos un sinoves
            if ($this->template == 33) {
                if (!empty($_SESSION['NEWSLETTER_SENDID']) ) {
                    $sendId = $_SESSION['NEWSLETTER_SENDID'];
                } else {
                    $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
                    $values = array (
                        ':email' => 'any',
                        ':html' => $this->content,
                        ':template' => $this->template
                    );
                    $query = Model::query($sql, $values);

                    $sendId = Model::insertId();
                    $_SESSION['NEWSLETTER_SENDID'] = $sendId;
                }
                $the_mail = 'any';
            } else {
                $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
                $values = array (
                    ':email' => $this->to,
                    ':html' => $this->content,
                    ':template' => $this->template
                );
                $query = Model::query($sql, $values);

                $sendId = Model::insertId();
                $the_mail = $this->to;
            }

            if (!empty($sendId)) {
                // token para el sinoves
                $token = md5(uniqid()) . '¬' . $the_mail  . '¬' . $sendId;
                $viewData['sinoves'] = \SITE_URL . '/mail/' . base64_encode($token) . '/?email=' . $this->to;
            } else {
                $viewData['sinoves'] = \SITE_URL . '/contact';
            }
            $_SESSION['MAILING_TOKEN'] = $viewData['sinoves'];

            $viewData['baja'] = \SITE_URL . '/user/leave/?email=' . $this->to;

            if ($plain) {
                return strip_tags($this->content) . '

                ' . $viewData['sinoves'];
            } else {
                // para plantilla boletin
                if ($this->template == 33) {
                    $viewData['baja'] = \SITE_URL . '/user/leave/?unsuscribe=newsletter&email=' . $this->to;
                    return new View (CONF_PATH.'/view/email/newsletter.html.php', $viewData);
                } else {
                    return new View ('view/email/default.html.php', $viewData);
                }
            }
        }

        /**
         *
         * Adjuntar archivo.
         * @param type string	$filename
         * @param type string	$name
         * @param type string	$encoding
         * @param type string	$type
         */
        private function attachFile($filename, $name = false, $encoding = 'base64', $type = 'application/pdf') {
            $this->attachments[] = array(
                'filename' => $filename,
                'name' => $name,
                'encoding' => $encoding,
                'type' => $name
            );
        }

        /**
         *
         * Adjuntar cadena como archivo.
         * @param type string	$string
         * @param type string	$name
         * @param type string	$encoding
         * @param type string	$type
         */
        private function attachString($string, $name = false, $encoding = 'base64', $type = 'application/pdf') {
            $this->attachments[] = array(
                'string' => $string,
                'name' => $name,
                'encoding' => $encoding,
                'type' => $name
            );
        }


        /**
         *
         * @param array $filters    user (nombre o email),  template
         */
        public function getSended($filters = array(), $limit = 999) {

            $values = array();
            $sqlFilter = '';
            $and = " WHERE";

            if (!empty($filters['user'])) {
                $sqlFilter .= $and . " ( user.id LIKE :user OR user.name LIKE :user OR user.email LIKE :user  OR mail.email LIKE :user )";
                $and = " AND";
                $values[':user'] = "%{$filters['user']}%";
            }

            if (!empty($filters['template'])) {
                $sqlFilter .= $and . " mail.template = :template";
                $and = " AND";
                $values[':template'] = $filters['template'];
            }

            $sql = "SELECT
                        mail.id as id,
                        user.name as user,
                        mail.email as email,
                        mail.template as template,
                        DATE_FORMAT(mail.date, '%d|%m|%Y %H:%i') as date
                    FROM mail
                    INNER JOIN user
                        ON user.email = mail.email
                    $sqlFilter
                    ORDER BY mail.date DESC
                    LIMIT {$limit}";

            $query = Model::query($sql, $values);
            return $query->fetchAll(\PDO::FETCH_OBJ);
            
        }

	}

}