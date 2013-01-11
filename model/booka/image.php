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

    use Base\Library\Check,
        Base\Library\Text,
        Base\Model;

    class Image extends \Base\Core\Model {

        public
            $id,
            $image,
            $section,
            $order;

        function validate(&$errors = array()) {
            asort($errors);
            return true;
        }
        
        function save(&$errors = array()) {
            asort($errors);
           return null;
        }
        
        /**
         * Get the images for a booka
         * @param varchar(50) $id  Project identifier
         * @param varchar(50) $section 'booka' (banda) or 'booka2' (contenido)
         * 
         * @return array of categories identifiers
         */
        public static function get ($id, $section = 'booka') {

            if (!in_array($section, array('booka', 'booka2'))) $section = 'booka';

            $array = array ();
            try {
                $values = array(':id' => $id);
                
                $sql = "SELECT * 
                    FROM {$section}_image
                    WHERE {$section} = :id
                    ORDER BY `order`";
                
                $query = static::query($sql, $values);
                $images = $query->fetchAll(\PDO::FETCH_OBJ);
                foreach ($images as $image) {
                    $array[] = Model\Image::get($image->image);
                }

                return $array;
            } catch(\PDOException $e) {
                throw new \Base\Core\Exception($e->getMessage());
            }
        }

        /*
         * la primera para el widget
         */
        public static function getFirst ($id) {

            try {
                $sql = "SELECT image FROM booka_image WHERE booka = ? ORDER BY `order` ASC LIMIT 1";
                $query = self::query($sql, array($id));
                $first = $query->fetchColumn(0);
                return Model\Image::get($first);
                
            } catch(\PDOException $e) {
                return false;
            }

        }
        
        /*
         * Solo imágenes para galeria
         */
        public static function getGallery ($id, $section = 'booka') {

            if (!in_array($section, array('booka', 'booka2'))) $section = 'booka';

            $gallery = array();

            try {
                $sql = "SELECT image FROM {$section}_image WHERE {$section} = ? ORDER BY `order` ASC";
                $query = self::query($sql, array($id));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $image) {
                    $gallery[] = Model\Image::get($image['image']);
                }

                return $gallery;
            } catch(\PDOException $e) {
                return false;
            }

        }
        
        
        /*
         * Para aplicar una seccion o un enlace
        public static function update ($booka, $image, $field, $value) {
            
            $sql = "UPDATE booka_image SET `$field` = :val WHERE booka = :booka AND image = :image";
            if (self::query($sql, array(':booka'=>$booka, ':image'=>$image, ':val'=>$value))) {
                return true;
            } else {
                return false;
            }

        }
         */

        /*
         * Para que una imagen salga antes  (disminuir el order)
         */
        public static function up ($booka, $image) {
            return Check::reorder($image, 'up', 'booka_image', 'image', 'order', array('booka' => $booka));
        }

        /*
         * Para que una imagen salga despues  (aumentar el order)
         */
        public static function down ($booka, $image) {
            return Check::reorder($image, 'down', 'booka_image', 'image', 'order', array('booka' => $booka));
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($booka) {
            $query = self::query('SELECT MAX(`order`) FROM booka_image WHERE booka = :booka'
                , array(':booka'=>$booka));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        /*
         * Para que una imagen de contenido salga antes  (disminuir el order)
         */
        public static function up2 ($booka, $image) {
            return Check::reorder($image, 'up', 'booka2_image', 'image', 'order', array('booka2' => $booka));
        }

        /*
         * Para que una imagen de contenido salga despues  (aumentar el order)
         */
        public static function down2 ($booka, $image) {
            return Check::reorder($image, 'down', 'booka2_image', 'image', 'order', array('booka2' => $booka));
        }

        /*
         * Orden para añadirlo al final de las imágenes de contenido
         */
        public static function next2 ($booka) {
            $query = self::query('SELECT MAX(`order`) FROM booka2_image WHERE booka2 = :booka'
                , array(':booka'=>$booka));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function sections () {
            return array(
                'booka' => 'Banda',
                'booka2' => 'Contenido'
            ); 
       }

    }
    
}