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

    use \Base\Model\Icon,
        \Base\Model\License;

    class Reward extends \Base\Core\Model {

        public
            $id,
			$booka,
			$reward_es,
			$reward_en,
			$description_es,
			$description_en,
			$amount,
			$units,
			$other_text_es,
			$other_text_en,
            $taken = 0, // recompensas comprometidas por aporte
            $none; // si no quedan unidades de esta recompensa

	 	public static function get ($id) {
            try {
                $sql = "SELECT
                            *,
                            IFNULL(reward_".\LANG.", reward_es) as reward,
                            IFNULL(description_".\LANG.", description_es) as description,
                            IFNULL(other_text_".\LANG.", other_text_es) as other_text
                        FROM reward
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

                $values = array(
                    ':booka' => $booka
                );

                $sql = "SELECT  
                            *,
                            IFNULL(reward_".\LANG.", reward_es) as reward,
                            IFNULL(description_".\LANG.", description_es) as description,
                            IFNULL(other_text_".\LANG.", other_text_es) as other_text
                        FROM    reward
                        WHERE   booka = :booka
                        ";

                $sql .= " ORDER BY reward.id ASC";

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
            // Estos son errores que no permiten continuar
            if (empty($this->booka))
                $errors[] = 'No hay proyecto al que asignar la recompensa';
            
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
				'booka',
				'reward_es',
				'reward_en',
				'description_es',
				'description_en',
				'type',
				'amount',
				'units',
				'other_text_es',
				'other_text_en'
				);

			$set = '';
			$values = array();

			foreach ($fields as $field) {
				if ($set != '') $set .= ", ";
				$set .= "$field = :$field ";
				$values[":$field"] = $this->$field;
			}

			try {
				$sql = "REPLACE INTO reward SET " . $set;
				self::query($sql, $values);
            	if (empty($this->id)) $this->id = self::insertId();
        		return true;
			} catch(\PDOException $e) {
				$errors[] = "El retorno {$this->reward} no se ha grabado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
			}
		}

		/**
		 * Quitar un retorno de un proyecto
		 *
		 * @param varchar(50) $booka id de un proyecto
		 * @param INT(12) $id  identificador de la tabla reward
		 * @param array $errors
		 * @return boolean
		 */
		public function remove (&$errors = array()) {
			$values = array (
				':booka'=>$this->booka,
				':id'=>$this->id,
			);

            try {
                self::query("DELETE FROM reward WHERE id = :id AND booka = :booka", $values);
				return true;
			} catch(\PDOException $e) {
				$errors[] = 'No se ha podido quitar el retorno '. $this->id. '. ' . $e->getMessage();
                //Text::get('remove-reward-fail');
                return false;
			}
		}

        /**
         * Calcula y actualiza las unidades de recompensa comprometidas por aporte
         * @param void
         * @return numeric
         */
        public function getTaken () {

            // cuantas de esta recompensa en aportes no cancelados
            $sql = "SELECT
                        COUNT(invest_reward.reward) as taken
                    FROM invest_reward
                    INNER JOIN invest
                        ON invest.id = invest_reward.invest
                        AND invest.status IN ('0', '1', '3')
                        AND invest.booka = :booka
                    WHERE invest_reward.reward = :reward
                ";

            $values = array(
                ':booka' => $this->booka,
                ':reward' => $this->id
            );

            $query = self::query($sql, $values);
            if ($taken = $query->fetchColumn(0)) {
                return $taken;
            } else {
                return 0;
            }
        }

	}

}