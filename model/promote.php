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

    use \Base\Library\Text,
        \Base\Model\Booka,
        \Base\Library\Check;

    class Promote extends \Base\Core\Model {

        public
            $id,
            $booka,
            $name,
            $title,
            $description,
            $order,
            $active;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($booka) {
                $query = static::query("
                    SELECT  
                        *
                    FROM    promote
                    WHERE promote.booka = :booka
                    ", array(':booka'=>$booka));
                $promote = $query->fetchObject(__CLASS__);
                $promote->booka = Booka::getMedium($promote->booka);

                return $promote;
        }

        /*
         * Lista de Bookas destacados
         */
        public static function getAll ($activeonly = false) {

            // estados
            $status =  Booka::status();

            $promos = array();

            $sqlFilter = ($activeonly) ? " AND promote.active = 1" : '';

            $query = static::query("
                SELECT
                    *
                FROM    promote
                WHERE promote.booka IS NOT NULL
                $sqlFilter
                ORDER BY `order` ASC
                ");
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->booka = Booka::getMedium($promo->booka);
                $promo->status = $status[$booka->status];
                $promos[] = $promo;
            }

            return $promos;
        }

        /*
         * Lista de Bookas disponibles para destacar
         */
        public static function available ($current = null) {

            if (!empty($current)) {
                $sqlCurr = " WHERE booka != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    booka.id as id,
                    booka.name_es as name,
                    booka.status as status
                FROM    booka
                WHERE status > 2
                AND booka.id NOT IN (SELECT booka FROM promote {$sqlCurr} )
                ORDER BY name ASC
                ");

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) { 
            if (empty($this->booka))
                $errors[] = 'No se ha indicado booka para destacar';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'booka',
                'order',
                'active'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO promote SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto destacado
         */
        public static function delete ($booka) {
            
            $sql = "DELETE FROM promote WHERE booka = :booka";
            if (self::query($sql, array(':booka'=>$booka))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un destacado
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE promote SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga antes  (disminuir el order)
         */
        public static function up ($booka) {
            return Check::reorder($booka, 'up', 'promote', 'id', 'order');
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($booka) {
            return Check::reorder($booka, 'down', 'promote', 'id', 'order');
        }

        /*
         *
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM promote');
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }
    
}