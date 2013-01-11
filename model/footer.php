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
        Base\Library\Check;
    
    class Footer extends \Base\Core\Model {

        public
            $id,
            $title_es,
            $title_en,
            $url,
            $column,
            $order; // para ordenar

        /*
         *  Devuelve enlaces de una columna
         */
        public static function get ($id) {
            $list = array();

            $sql = "
                SELECT
                    *,
                    IFNULL(title_".LANG.", title_es) as title
                FROM    footer
                WHERE `id` = :id
                ORDER BY `order` ASC
                ";
            
            $query = static::query($sql, array(':id'=>$id));

            $item = $query->fetchObject(__CLASS__);

            return $item;
        }

        /*
         * Lista de elementos de footer
         */
        public static function getAll ($filters = array()) {

            $list = array();

            $values = array();
            $and = " WHERE";
            $sqlFilter = "";

            if (!empty($filters['column'])) {
                $sqlFilter = $and." `column` = :column ";
                $values[':column'] = $filters['column'];
                $and = " AND";
            }
            
            if (!empty($filters['nonews'])) {
                $sqlFilter .= $and." `column` != 'news' ";
            }

            $sql = "
                SELECT
                    *,
                    IFNULL(title_".LANG.", title_es) as title
                FROM    footer
                $sqlFilter
                ORDER BY `order` ASC
                ";
           
            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[] = $item;
            }

            return $list;
        }

        /**
         * Lista simple de columnas
         *
         * @param void
         * @return array
         */
		public static function getList () {
            $array = array (
                'news' => Text::get('footer-header-news'),
                'about' => Text::get('footer-header-about'),
                'faq' => Text::get('footer-header-faq')
            );
            
            return $array;
		}

        
        public function validate (&$errors = array()) { 
            return true;
                
            if (empty($this->title_es))
                $errors[] = 'Falta titulo';

            if (empty($this->url))
                $errors[] = 'Falta url';

            if (empty($this->column))
                $errors[] = 'Falta columna';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'title_es',
                'title_en',
                'column',
                'url',
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
                $sql = "REPLACE INTO footer SET " . $set;
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
            
            $sql = "DELETE FROM footer WHERE id = :id";
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
            return Check::reorder($id, 'up', 'footer', 'id', 'order');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'footer', 'id', 'order');
        }

        /*
         * Orden para aÃ±adirlo al final
         */
        public static function next ($column = null) {
            $values = array();

            $sql = 'SELECT MAX(`order`) FROM footer';

            if (!empty($column)) {
                $sql .= ' WHERE `column` = :column';
                $values[':column'] = $column;
            }
            $query = self::query($sql, $values);
            $order = $query->fetchColumn(0);
            return ++$order;

        }

    }
    
}