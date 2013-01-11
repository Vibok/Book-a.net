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

    use Base\Library\Check,
        Base\Library\Text;

    class Faq extends \Base\Core\Model {

        public
            $id,
            $section,
            $title,
            $description,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        *
                    FROM faq
                    WHERE faq.id = :id
                    ", array(':id' => $id));
                $faq = $query->fetchObject(__CLASS__);

                return $faq;
        }

        /*
         * Lista de Bookas destacados
         */
        public static function getAll ($section = 'main') {

            $values = array(':section' => $section);

            $sql = "
                SELECT
                    id,
                    section,
                    IFNULL(title_".\LANG.", title_es) as title,
                    IFNULL(description_".\LANG.", description_es) as description,
                    `order`
                FROM faq
                WHERE faq.section = :section
                ORDER BY `order` ASC, title ASC
                ";

            $query = static::query($sql, $values);
            
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) { 
            if (empty($this->section))
                $errors[] = 'Falta seccion';

            if (empty($this->title_es))
                $errors[] = 'Falta título';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'section',
                'title_es',
                'title_en',
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
                $sql = "REPLACE INTO faq SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                $extra = array(
                    'section' => $this->section
                );
                Check::reorder($this->id, $this->move, 'faq', 'id', 'order', $extra);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM faq WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id) {
            $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
            $faq = $query->fetchObject();
            $extra = array(
                'section' => $faq->section
            );
            return Check::reorder($id, 'up', 'faq', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            $query = static::query("SELECT section FROM faq WHERE id = ?", array($id));
            $faq = $query->fetchObject();
            $extra = array(
                'section' => $faq->section
            );
            return Check::reorder($id, 'down', 'faq', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($section = 'main') {
            $query = self::query('SELECT MAX(`order`) FROM faq WHERE section = :section'
                , array(':section'=>$section));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'main' => Text::get('faq-main-section-header'),
                'bookas' => Text::get('faq-bookas-section-header'),
                'investors' => Text::get('faq-investors-section-header'),
                'readers' => Text::get('faq-readers-section-header'),
                'mixers' => Text::get('faq-mixers-section-header')
            );
        }

    }
    
}