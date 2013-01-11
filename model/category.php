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

    use Base\Library\Check;
    
    class Category extends \Base\Core\Model {

        public
            $id,
            $name_es,
            $name_en,
            $move, // para ordenar al crear
            $used; // numero de Bookas que usan la categoria

        /*
         *  Devuelve datos de una categoria
         */
        public static function get ($id) {
            
            $query = static::query("
                SELECT
                    *,
                    IFNULL(name_".LANG.", name_es) as name
                FROM    category
                WHERE category.id = :id
                ", array(':id' => $id));
            $category = $query->fetchObject(__CLASS__);

            return $category;
        }

        /*
         * Lista de categorias para Bookas
         * @TODO añadir el numero de usos
         */
        public static function getAll ($limit = null) {

            $list = array();

            $sql = "
                SELECT
                    *,
                    IFNULL(name_".LANG.", name_es) as name
                FROM    category
                ORDER BY `order` ASC
                ";
            
            if (!empty($limit)) {
                $sql .= "LIMIT $limit";
            }
            
            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $category) {
                $category->used = static::used($category->id);
                $list[$category->id] = $category;
            }

            return $list;
        }

        /**
         * Lista simple id=>nombre
         *
         * @param void
         * @return array
         */
		public static function getList () {
            $array = array ();
            try {
                $sql = "SELECT 
                            id, name_es
                        FROM category
                        ORDER BY category.order ASC";

                $query = static::query($sql);
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
		}

        
        public function validate (&$errors = array()) { 
            if (empty($this->name_es))
                $errors[] = 'Falta nombre';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name_es',
                'name_en',
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
                $sql = "REPLACE INTO category SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una catgoria de la tabla
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM category WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {
            return Check::reorder($id, 'up', 'category', 'id', 'order');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'category', 'id', 'order');
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM category');
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        /*
         * Cuenta el numero de bookas que la usan
         */
        public static function used ($id) {
            $query = self::query('SELECT COUNT(DISTINCT(booka)) FROM booka_category WHERE category = ? ', array($id));
            $num = $query->fetchColumn(0);
            return $num;

        }

    }
    
}