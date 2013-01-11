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

    class Category extends \Base\Core\Model {

        public
                $id,
                $booka;

        /**
         * Get the categories for a booka
         * @param varcahr(50) $id  Booka identifier
         * @return array of categories identifiers
         */
        public static function get($id) {
            $array = array();
            try {
                $query = static::query("SELECT category FROM booka_category WHERE booka = ?", array($id));
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[0];
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
        }

        /**
         * Get all categories available
         *
         * @param void
         * @return array
         */
        public static function getAll() {
            $array = array();
            try {
                $sql = "
                    SELECT
                        category.id as id,
                        IFNULL(category.name_" . \LANG . ", category.name_es) as name
                    FROM    category
                    ORDER BY name ASC
                    ";

                $query = static::query($sql);
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
        }

        /**
         * Get all categories for this booka by name
         *
         * @param void
         * @return array
         */
        public static function getNames($booka = null, $limit = null) {
            $array = array();
            $values = array();
            try {
                $sqlFilter = "";
                if (!empty($booka)) {
                    $sqlFilter = "INNER JOIN booka_category ON booka_category.category = category.id AND booka = :booka";
                    $values[':booka'] = $booka;
                }

                $sql = "SELECT 
                            category.id,
                            IFNULL(category.name_" . \LANG . ", category.name_es) as name
                        FROM category
                        $sqlFilter
                        ORDER BY RAND() DESC
                        ";
                if (!empty($limit)) {
                    $sql .= "LIMIT $limit";
                }
                $query = static::query($sql, $values);
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch (\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
        }

        public function validate(&$errors = array()) {
            // Estos son errores que no permiten continuar
            if (empty($this->id))
                $errors[] = 'No hay ninguna categoria para guardar';

            if (empty($this->booka))
                $errors[] = 'No hay ningun proyecto al que asignar';

            //cualquiera de estos errores hace fallar la validación
            if (!empty($errors))
                return false;
            else
                return true;
        }

        public function save(&$errors = array()) {
            if (!$this->validate($errors))
                return false;

            try {
                $sql = "REPLACE INTO booka_category (booka, category) VALUES(:booka, :category)";
                $values = array(':booka' => $this->booka, ':category' => $this->id);
                self::query($sql, $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = "La categoria {$category} no se ha asignado correctamente. Por favor, revise los datos." . $e->getMessage();
                return false;
            }
        }

        /**
         * Quitar una palabra clave de un proyecto
         *
         * @param varchar(50) $booka id de un proyecto
         * @param INT(12) $id  identificador de la tabla keyword
         * @param array $errors 
         * @return boolean
         */
        public function remove(&$errors = array()) {
            $values = array(
                ':booka' => $this->booka,
                ':category' => $this->id,
            );

            try {
                self::query("DELETE FROM booka_category WHERE category = :category AND booka = :booka", $values);
                return true;
            } catch (\PDOException $e) {
                $errors[] = 'No se ha podido quitar la categoria ' . $this->id . ' del proyecto ' . $this->booka . ' ' . $e->getMessage();
                //Text::get('remove-category-fail');
                return false;
            }
        }

    }

}