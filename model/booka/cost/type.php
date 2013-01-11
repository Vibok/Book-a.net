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

namespace Base\Model\Booka\Cost {

    class Type extends \Base\Core\Model {

        public
            $id,
			$name_es,
			$name_en,
			$description_es,
			$description_en;

	 	public static function get ($id) {
            try {
                $sql = "SELECT
                            *,
                            IFNULL(name_".\LANG.", name_es) as name,
                            IFNULL(description_".\LANG.", description_es) as description
                        FROM cost_type
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
                        COUNT(cost.id)
                        FROM cost
                        WHERE cost.type = cost_type.id
                    ) as used
                FROM    cost_type
                ORDER BY name ASC";

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
                        FROM    cost_type
                        ";

                $sql .= " ORDER BY id ASC";

				$query = self::query($sql, $values);
				foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item ) {
                    $array[$item->id] = $item;
                }
				return $array;
			} catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;
            
			$fields = array(
				'id',
				'name_es',
				'name_en',
				'description_es',
				'description_en'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "$field = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO cost_type SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
        		return true;
			} catch(\PDOException $e) {
				$errors[] = "El tipo de coste no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		/**
		 * Eliminar tipo de coste
		 *
		 * @param INT(12) $id  identificador de la tabla cost_type
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':id'=>$this->id
			);

            try {
                self::query("DELETE FROM cost_type WHERE id = :id", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el retorno '. $this->id. '. ' . $e->getMessage();
                return false;
			}
		}

	}

}