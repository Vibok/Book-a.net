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

namespace Base\Model\User {

    use Base\Model\Image;

    class Interest extends \Base\Model\Category {

        public
            $id,
            $user;


        /**
         * Get the interests for a user
         * @param varcahr(50) $id  user identifier
         * @return array of interests identifiers
         */
	 	public static function get ($id) {
            $array = array ();
            try {
                $query = static::query("SELECT interest FROM user_interest WHERE user = ?", array($id));
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    $array[$int[0]] = $int[0];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
		}

        /**
         * Get all categories available
         *
         *
         * @param user isset get all categories of a user
         * @return array
         */
		public static function getAll ($user = null) {
            $array = array ();
            try {
                $values = array();
                $sql = "
                    SELECT
                        category.id as id,
                        IFNULL(category.name_".LANG.", category.name_es) as name
                    FROM    category
                    ";
                if (!empty($user)) {
                    $sql .= "INNER JOIN user_interest
                                ON  user_interest.interest = category.id
                                AND user_interest.user = :user
                                ";
                    $values[':user'] = $user;
                }
                $sql .= "ORDER BY name ASC
                        ";

                $query = static::query($sql, $values);
                $interests = $query->fetchAll();
                foreach ($interests as $int) {
                    $array[$int[0]] = $int[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ningun interes para guardar';
                //Text::get('validate-interest-noid');

            if (empty($this->user))
                $errors[] = 'No hay ningun usuario al que asignar';
                //Text::get('validate-interest-nouser');

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $values = array(':user'=>$this->user, ':interest'=>$this->id);

			try {
	            $sql = "REPLACE INTO user_interest (user, interest) VALUES(:user, :interest)";
				self::query($sql, $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = "El interés {$this->id} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
				return false;
			}

		}

		/**
		 * Quitar una palabra clave de un proyecto
		 *
		 * @param varchar(50) $user id de un proyecto
		 * @param INT(12) $id  identificador de la tabla keyword
		 * @param array $errors 
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':user'=>$this->user,
				':interest'=>$this->id,
			);

            try {
                self::query("DELETE FROM user_interest WHERE interest = :interest AND user = :user", $values);
				return true;
			} catch(\PDOException $e) {
                $errors[] = 'No se ha podido quitar el interes ' . $this->id . ' del usuario ' . $this->user . ' ' . $e->getMessage();
                //Text::get('remove-interest-fail');
                return false;
			}
		}

        /*
         * Lista de usuarios que comparten intereses con el usuario
         *
         * Si recibimos una categoría de interés, solamente los que comparten esa categoría
         *
         */
        public static function share ($user, $category = null, $limit = null) {
             $array = array ();
            try {

                $values = array(':me'=>$user);

               $sql = "SELECT 
                            DISTINCT(user_interest.user) as id, 
                            user.name as name,
                            user.avatar as avatar
                        FROM user_interest
                        INNER JOIN user_interest as mine
                            ON user_interest.interest = mine.interest
                            AND mine.user = :me
                        INNER JOIN user
                            ON  user.id = user_interest.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE user_interest.user != :me
                        ";
               if (!empty($category)) {
                   $sql .= "AND user_interest.interest = :interest
                       ";
                   $values[':interest'] = $category;
               }
               $sql .= " ORDER BY RAND()";
               if (!empty($limit)) {
                   $sql .= " LIMIT $limit";
               }
               
               /*
               if ($_SESSION['user']->id == 'root') {
                   echo $sql . '<br />';
                   echo \trace($values);
               }
                */

                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar vienen en la sentencia, hay que sacar la imagen
                    $share['user'] = $share['id'];
                    $share['avatar'] = (empty($share['avatar'])) ? Image::get(1) : Image::get($share['avatar']);
                    if (!$share['avatar'] instanceof Image) {
                        $share['avatar'] = Image::get(1);
                    }
                    
                    // lista de intereses
                    $share['interests'] = static::getAll($share['id']);
                    
                    $array[] = (object) $share;
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
        }

        /*
         * Lista de usuarios de la comunidad que comparten un interés
         *
         */
        public static function shareAll ($category) {
             $array = array ();
            try {

                $values = array(':interest'=>$category);

               $sql = "SELECT DISTINCT(user_interest.user) as id
                        FROM user_interest
                        INNER JOIN user
                            ON  user.id = user_interest.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE user_interest.interest = :interest
                        ";

                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar
                    $user = \Base\Model\User::get($share['id']);
                    if (empty($user->avatar)) $user->avatar = (object) array('id'=>1);
                    // meritocracia
                    $support = (object) $user->support;
                    // proyectos publicados
                    $query = self::query('SELECT COUNT(id) FROM project WHERE owner = ? AND status > 2', array($share['id']));
                    $projects = $query->fetchColumn(0);

                    $array[] = (object) array(
                        'user' => $share['id'],
                        'avatar' => $user->avatar,
                        'name' => $user->name,
                        'projects' => $projects,
                        'invests' => $support->invests
                    );
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
        }

	}
    
}