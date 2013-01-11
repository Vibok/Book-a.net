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

namespace Base\Model {

    use Base\Library\Text,
        Base\Library\Feed;

    class Message extends \Base\Core\Model {

        public
            $id,
            $user,
            $booka,
            $date, // timestamp del momento en que se creó el mensaje
            $message, // el texto del mensaje en si
            $timeago;

        /*
         *  Devuelve datos de un mensaje
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT  *
                    FROM    message
                    WHERE   id = :id
                    ", array(':id' => $id));
                $message = $query->fetchObject(__CLASS__);
                
                // datos del usuario
                $message->user = User::getMini($message->user);

                // reconocimiento de enlaces y saltos de linea
                $message->message = nl2br(Text::urlink($message->message));

                //hace tanto
                $message->timeago = Feed::time_ago($message->date);

                return $message;
        }

        /*
         * Lista de hilos de un proyecto
         */
        public static function getAll ($booka, $lang = null) {

            $messages = array();

            $query = static::query("
                SELECT
                    *
                FROM  message
                WHERE   message.booka = :booka
                ORDER BY date DESC, id DESC
                ", array(':booka'=>$booka));
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $message) {
                // datos del usuario
                $message->user = User::getMini($message->user);
                
                // reconocimiento de enlaces y saltos de linea
                $message->text = nl2br(Text::urlink($message->message));

                //hace tanto
                $message->timeago = Feed::time_ago($message->date);

                $messages[] = $message;
            }

            return $messages;
        }

        /*
         * Numero de comentarios de cierto usuario a un proyecto
         */
        public static function getMine ($booka, $user) {

            $messages = array();

            $query = static::query("
                SELECT
                    COUNT(id)
                FROM  message
                WHERE   message.booka = :booka
                AND message.user = :user
                ", array(':booka'=>$booka, ':user'=>$user));
            return $query->fetchColumn();
        }


        public function validate (&$errors = array()) { 
            if (empty($this->user))
                $errors[] = 'Falta usuario';

            if (empty($this->booka))
                $errors[] = 'Falta proyecto booka';

            if (empty($this->message))
                $errors[] = 'Falta texto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            if (\is_object($this->user)) {
                $this->user = $this->user->id;
            }

            $fields = array(
                'id',
                'user',
                'booka',
                'message'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if (!empty($this->$field)) {
                    if ($set != '') $set .= ", ";
                    $set .= "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
            }

            try {
                $sql = "REPLACE INTO message SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "El mensaje no se ha grabado correctamente. Por favor, inténtelo de nuevo." . $e->getMessage();
                return false;
            }
        }

        /*
         * Para que el admin pueda borrar mensajes que no aporten nada
         */
        public function delete () {

            if ($this->blocked == 1) {
                return false;
            }

            $sql = "DELETE FROM message WHERE id = ?";
            if (self::query($sql, array($this->id))) {
                return true;
            } else {
                return false;
            }

        }


        /*
         * Numero de comentarios en un libro-semilla
         */
        public static function numMessages ($id) {
            $sql = "SELECT COUNT(message.id) FROM message WHERE booka = :id";
            $query = self::query($sql, array(':id'=>$id));
            $num = $query->fetchColumn();

            if (empty($num)) {
                return false;
            } else {
                return $num;
            }
        }

        /*
         * Numero de usuarios mensajeros de un proyecto
         */
        public static function numMessegers ($id) {
            $sql = "SELECT COUNT(DISTINCT(message.user)) FROM message WHERE booka = :id";
            $query = self::query($sql, array(':id'=>$id));
            $num = $query->fetchColumn();

            if (empty($num)) {
                return false;
            } else {
                return $num;
            }
        }

        /*
         * Lista de usuarios mensajeros de un proyecto
         */
        public static function getMessegers ($id) {
            $list = array();

            $sql = "SELECT 
                        DISTINCT(message.user) as id, 
                        user.name as name,
                        user.avatar as avatar
                    FROM message
                    INNER JOIN user
                        ON user.id = message.user
                    WHERE booka = :id";
            $query = self::query($sql, array(':id'=>$id));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $user) {
                $user->avatar = Image::get($user->avatar);
                if (empty($user->avatar->id) || !$user->avatar instanceof Image) {
                    $user->avatar = Image::get(1);
                }

                $list[] = $user;
            }

            return $list;
        }

    }
    
}