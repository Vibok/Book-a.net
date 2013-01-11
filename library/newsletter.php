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
        Base\Library\Template,
        Base\Core\View;
	/*
	 * Clase para montar el contenido de la newsletter
	 *
	 */
    class Newsletter {

		static public function getTesters () {
            $list = array();
            $list[] = (object) array(
                'user' => 'root',
                'email' => 'test@example.com',
                'name' => 'Tester'
            );

            return $list;

        }

        /*
         * Usuarios actualmente activos que no tienen bloqueado el envio de newsletter
         */
		static public function getReceivers () {

            $list = array();

            $sql = "SELECT
                        user.id as user,
                        user.name as name,
                        user.email as email
                    FROM user
                    LEFT JOIN user_prefer
                        ON user_prefer.user = user.id
                    WHERE user.id != 'root'
                    AND user.active = 1
                    AND (user_prefer.mailing = 0 OR user_prefer.mailing IS NULL)
                    ORDER BY user.id ASC
                    ";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                    $list[] = $receiver;
                }
            }

            return $list;

        }

		static public function initiateSending ($subject, $receivers) {

            /*
             * Grabar el contenido para el sinoves en la tabla mail, obtener el id y el codigo para sinoves
             *
             */


            try {
                Model::query("START TRANSACTION");
                // eliminamos los datos del envío
                Model::query("DELETE FROM mailer_content");
                // eliminamos los destinatarios
                Model::query("DELETE FROM mailer_send");

                // contenido (plantilla, mas contenido de newsletter
                $Template = Template::get(33);
                $content = Newsletter::getContent($Template->text);

                $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
                $values = array (
                    ':email' => 'any',
                    ':html' => $content,
                    ':template' => 33
                );
                $query = Model::query($sql, $values);

                $mailId = Model::insertId();
                $sql = "INSERT INTO `mailer_content` (`id`, `active`, `mail`, `subject`)
                    VALUES ('' , '0', :mail, :subject)";
                Model::query($sql, array(':subject'=>$subject, ':mail'=>$mailId));

                // destinatarios
                $sql = "INSERT INTO `mailer_send` (`id`, `user`, `email`, `name`) VALUES ('', :user, :email, :name)";
                foreach ($receivers as $user) {
                    Model::query($sql, array(':user'=>$user->user, ':email'=>$user->email, ':name'=>$user->name));
                }

                Model::query("COMMIT");
                return true;

            } catch(\PDOException $e) {
                echo "HA FALLADO!!" . $e->getMessage();
                die;
                return false;
            }

        }

		static public function getSending () {
            try {
                // recuperamos los datos del envío
                $query = Model::query("
                    SELECT
                        mailer_content.id as id,
                        mailer_content.active as active,
                        mailer_content.mail as mail,
                        mailer_content.subject as subject,
                        DATE_FORMAT(mailer_content.datetime, '%d|%m|%Y %H:%i:%s') as date,
                        mailer_content.blocked as blocked
                    FROM mailer_content
                    ORDER BY active DESC, id DESC
                    LIMIT 1
                    ");
                $mailing = $query->fetchObject();

                // y el estado
                $query = Model::query("
                SELECT
                        COUNT(mailer_send.id) AS receivers,
                        SUM(IF(mailer_send.sended = 1, 1, 0)) AS sended,
                        SUM(IF(mailer_send.sended = 0, 1, 0)) AS failed,
                        SUM(IF(mailer_send.sended IS NULL, 1, 0)) AS pending
                FROM	mailer_send
                ");
                $sending = $query->fetchObject();

                $mailing->receivers = $sending->receivers;
                $mailing->sended    = $sending->sended;
                $mailing->failed    = $sending->failed;
                $mailing->pending   = $sending->pending;

                return $mailing;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!" . $e->getMessage();
                return false;
            }
        }

        /*
         * Listado completo de destinatarios/envaidos/fallidos/pendientes 
         */
		static public function getDetail ($detail = 'receivers') {

            $list = array();

            switch ($detail) {
                case 'sended':
                    $sqlFilter = " AND mailer_send.sended = 1";
                    break;
                case 'failed':
                    $sqlFilter = " AND mailer_send.sended = 0";
                    break;
                case 'pending':
                    $sqlFilter = " AND mailer_send.sended IS NULL";
                    break;
                case 'receivers':
                default:
                    $sqlFilter = '';
            }

            $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                INNER JOIN mailer_send
                    ON mailer_send.user = user.id
                    $sqlFilter
                ORDER BY user.id";

            if ($query = Model::query($sql, $values)) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $user) {
                    $list[] = $user;
                }
            }

            return $list;

        }


		static public function activateSending () {
            // marcamos como activo el envio
            $query = Model::query("UPDATE mailer_content SET active = 1 ORDER BY id DESC LIMIT 1");
            return ($query->rowCount() == 1);
        }

		static public function getContent ($content) {
            // Bookas destacados
            $promotes_content = '';
            $home_promotes  = \Base\Model\Promote::getAll(true);

            if (!empty($home_promotes)) {
//                    $promotes_content = '<div class="section-tit">'.Text::get('home-promotes-header').'</div>';
                foreach ($home_promotes as $key => $promote) {
                    try {
                        $the_booka = \Base\Model\Booka::getMedium($promote->booka);
                        $promotes_content .= new View('view/email/newsletter_booka.html.php', array('promote'=>$promote, 'booka'=>$the_booka));
                    } catch (\Base\Core\Error $e) {
                        continue;
                    }
                }
            }

            // montammos el contenido completo
            $tmpcontent = $content;
            foreach (\array_keys($order) as $item) {
                $var = $item.'_content';
                $tmpcontent .= $$var;
            }

            return $tmpcontent;
		}

	}
	
}