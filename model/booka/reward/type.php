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

namespace Base\Model\Booka\Reward {

    use Base\Library\Check,
        Base\Library\Text;
    
    class Type extends \Base\Core\Model {

        public
            $id,
			$name_es,
			$name_en,
			$description_es,
			$description_en;

	 	public static function get ($id) {
            
            if ($id == 9999) {
                return new self(
                        array(
                            'id' => 9999,
                            'name' => 'Otro',
                            'name_es' => 'Otro',
                            'name_en' => 'Other',
                            'description' => '',
                            'description_es' => '',
                            'description_en' => ''
                        )
                    );
            }
            
            try {
                $sql = "SELECT
                            *,
                            IFNULL(name_".\LANG.", name_es) as name,
                            IFNULL(description_".\LANG.", description_es) as description
                        FROM reward_type
                        WHERE id = :id";
                $query = static::query($sql, array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
		}

		public static function getList () {

            $list = array();

            $sql = "
                SELECT
                    *,
                    IFNULL(name_".\LANG.", name_es) as name,
                    (   SELECT
                        COUNT(reward.id)
                        FROM reward
                        WHERE reward.type = reward_type.id
                    ) as used
                FROM    reward_type
                ORDER BY `order` ASC";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[$item->id] = $item;
            }

            return $list;
        }

		public static function getAll () {
            try {
                $array = array();

                $sql = "SELECT  
                            *,
                            IFNULL(name_".\LANG.", name_es) as name,
                            IFNULL(description_".\LANG.", description_es) as description
                        FROM    reward_type
                        ";

                $sql .= " ORDER BY `order` ASC";

				$query = self::query($sql, $values);
				foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item ) {
                    $array[$item->id] = $item;
                }
                
                $array['9999'] = new self(
                        array(
                            'id' => 9999,
                            'name' => 'Otro',
                            'name_es' => 'Otro',
                            'name_en' => 'Other',
                            'description' => '',
                            'description_es' => '',
                            'description_en' => ''
                        )
                    );
                
				return $array;
			} catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            if (empty($this->name_es)) {
                $errors[] = 'Falta el nombre';
                return false;
            }
            
            return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;
            
			$fields = array(
				'id',
				'name_es',
				'name_en',
				'description_es',
				'description_en',
                'order'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "`$field` = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO reward_type SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
        		return true;
			} catch(\PDOException $e) {
				$errors[] = "El tipo de recompensa no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id) {
            return Check::reorder($id, 'up', 'reward_type', 'id', 'order');
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'reward_type', 'id', 'order');
        }

        /*
         * Orden para aÃ±adirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM reward_type'
                , array(':section'=>$section));
            $order = $query->fetchColumn(0);
            return ++$order;

        }
        
		/**
		 * Eliminar tipo de recompensa
		 *
		 * @param INT(12) $id  identificador de la tabla reward_type
		 * @param array $errors
		 * @return boolean
		 */
		public function delete ($id) {
			$values = array (
				':id'=>$id
			);

            try {
                self::query("DELETE FROM reward_type WHERE id = :id", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el tipo de recompensa '. $id. '. ' . $e->getMessage();
                return false;
			}
		}

	}

}