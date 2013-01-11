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

namespace Base\Model\Booka {

    use Base\Core\Error,
        Base\Library\Text;
    
    class Cost extends \Base\Core\Model {

        public
            $id,
            $booka,
            $cost,
			$description,
            $type,
            $stage,
            $amount,
            $date;

	 	public static function get ($id) {
            try {
                $sql = "SELECT
                            *,
                            IFNULL(cost_".\LANG.", cost_es) as cost,
                            IFNULL(description_".\LANG.", description_es) as description
                        FROM cost
                        WHERE id = :id";
                $query = static::query($sql, array(':id' => $id));
                return $query->fetchObject(__CLASS__);
            } catch(\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
		}

		public static function getAll ($booka) {
            try {
                $array = array();

                $sql = "
                    SELECT
                        *,
                        IFNULL(cost_".\LANG.", cost_es) as cost,
                        IFNULL(description_".\LANG.", description_es) as description
                    FROM cost
                    WHERE cost.booka = :booka
                    ORDER BY cost.id ASC";

				$query = self::query($sql, array(':booka'=>$booka));
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                    $array[$item->id] = $item;
                }
				return $array;
			} catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
			}
		}

		public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->booka))
                $errors[] = 'No hay proyecto al que asignar el coste';

            if (empty($this->cost))
                $errors[] = 'No hay nombre de la tarea';

            if (empty($this->stage))
                $errors[] = 'No hay etapa';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

		public function save (&$errors = array()) {

			$fields = array(
				'id',
				'booka',
				'cost_es',
				'cost_en',
				'description_es',
				'description_en',
				'stage',
				'type',
				'amount',
				'date'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "`$field` = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO cost SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
				return true;
			} catch(\PDOException $e) {
                $errors[] = "El coste {$this->cost} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		/**
		 * Quitar un coste de un proyecto
		 *
		 * @param varchar(50) $booka id de un proyecto
		 * @param INT(12) $id  identificador de la tabla cost
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':booka'=>$this->booka,
				':id'=>$this->id,
			);

            try {
                self::query("DELETE FROM cost WHERE id = :id AND booka = :booka", $values);
				return true;
			} catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar el coste del proyecto ' . $this->booka . ' ' . $e->getMessage();
                return false;
			}
		}

        static public function stages() {
            return array(
                '1' => Text::get('stage1-name'),
                '2' => Text::get('stage2-name'),
                '3' => Text::get('stage3-name')
            );
        }
    
        
	}

}