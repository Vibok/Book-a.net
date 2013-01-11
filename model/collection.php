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
        Base\Model;
    
    class Collection extends \Base\Core\Model {

        public
            $id,
            $name_es,
            $name_en,
            $keywords_es,
            $keywords_en,
            $description_es,
            $description_en,
            $text_es,
            $text_en,
            $director,
            $image,
            $color,
            $move, // para ordenar al crear
            $used, // numero de Bookas que usan la colección
            $user = null; // los datos del usuario director
        
        /*
         *  Devuelve datos de una colección
         */
        public static function get ($id) {
            
            $query = static::query("
                SELECT
                    *,
                    IFNULL(name_".LANG.", name_es) as name,
                    IFNULL(keywords_".LANG.", keywords_es) as keywords,
                    IFNULL(description_".LANG.", description_es) as description,
                    IFNULL(text_".LANG.", text_es) as text
                FROM    collection
                WHERE collection.id = :id
                ", array(':id' => $id));
            $collection = $query->fetchObject(__CLASS__);

            $collection->image = Model\Image::get($collection->image);
            
            // usuario director de esta colección
            $director = static::getDirector($collection->id);
            if (!empty($director)) {
                $collection->user = Model\User::getMini($director);
            } else {
                $collection->user = null;
            }

            return $collection;
        }

        /*
         * Lista de coleccións para Bookas
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $list = array();

            $sql = "
                SELECT
                    *,
                    IFNULL(name_".LANG.", name_es) as name,
                    IFNULL(keywords_".LANG.", keywords_es) as keywords,
                    IFNULL(description_".LANG.", description_es) as description
                FROM    collection
                ORDER BY `order` ASC
                ";
            
            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $collection) {
                $collection->used = static::used($collection->id);
                $collection->image = Model\Image::get($collection->image);
                
                // usuario director de esta colección
                $director = static::getDirector($collection->id);
                if (!empty($director)) {
                    $collection->user = Model\User::getMini($director);
                } else {
                    $collection->user = null;
                }
                
                $list[$collection->id] = $collection;
            }

            return $list;
        }

        /**
         * Lista simple id=>nombre
         *
         * @param void
         * @return array
         */
		public static function getList ($usedOnly = false, $postedOnly = false) {
            $array = array ();
            try {
                $sql = "SELECT 
                            collection.id, 
                            collection.name_es
                        FROM collection
                        ";
                if ($usedOnly) {
                    $sql .= "INNER JOIN booka
                        ON collection.id = booka.collection 
                        ";
                }
                if ($postedOnly) {
                    $sql .= "INNER JOIN post
                        ON post.booka = booka.id
                        AND post.publish = 1
                        ";
                }
                $sql .= "ORDER BY collection.order ASC";

                $query = static::query($sql);
                $collections = $query->fetchAll();
                foreach ($collections as $col) {
                    $array[$col[0]] = $col[1];
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
                'keywords_es',
                'keywords_en',
                'description_es',
                'description_en',
                'text_es',
                'text_en',
                'director',
                'image',
                'color',
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
                $sql = "REPLACE INTO collection SET " . $set;
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
            
            $sql = "DELETE FROM collection WHERE id = :id";
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
            return Check::reorder($id, 'up', 'collection', 'id', 'order');
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'collection', 'id', 'order');
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM collection');
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        /*
         * Cuenta el numero de bookas que la usan
         */
        public static function used ($id) {
            $query = self::query('SELECT COUNT(DISTINCT(id)) FROM booka WHERE collection = ? ', array($id));
            $num = $query->fetchColumn(0);
            return $num;

        }

        /**
         * Para sacar el director de la colección
         *
         * @return type array
         */
        public static function getDirector ($id) {
            $query = self::query('SELECT
                                        user
                                  FROM user_collection
                                  WHERE collection = ?'
                , array($id));

            $data = $query->fetchColumn();
            return $data;
        }
        
        
        /**
         * Metodo para sacar el top de usuarios que aportan a una colección
         *
         * @param void
         * @return array
         */
		public static function getinvestors ($id, $limit = null) {

             $array = array ();
            try {

                $values = array(':id'=>$id);

               $sql = "SELECT 
                            invest.user as id, 
                            user.name as name,
                            user.avatar as avatar,
                            DATE_FORMAT(invest.invested, '%d | %m | %Y') as date,
                            SUM(invest.amount) as amount
                        FROM invest
                        INNER JOIN booka
                            ON invest.booka = booka.id
                            AND booka.collection = :id
                        INNER JOIN user
                            ON  user.id = invest.user
                            AND (user.hide = 0 OR user.hide IS NULL)
                        WHERE invest.status IN ('0', '1', '3')
                        AND (invest.anonymous = 0 OR invest.anonymous IS NULL)
                        GROUP BY invest.user
                        ORDER BY amount DESC
                    ";
               if (!empty($limit)) {
                   $sql .= " LIMIT $limit";
               }
               
                $query = static::query($sql, $values);
                $shares = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($shares as $share) {

                    // nombre i avatar vienen en la sentencia, hay que sacar la imagen
                    $share['user'] = $share['id'];
                    $queryI = static::query("SELECT COUNT(DISTINCT(booka)) FROM invest WHERE user = ? AND status IN ('0', '1', '3')", array($share['id']));
                    $share['bookas'] = $queryI->fetchColumn(0);
                    $queryP = static::query("SELECT SUM(amount) FROM invest WHERE user = ? AND status IN ('0', '1', '3')", array($share['id']));
                    $share['amount'] = $queryP->fetchColumn(0);
                    $share['avatar'] = (empty($share['avatar'])) ? Image::get(1) : Image::get($share['avatar']);
                    if (!$share['avatar'] instanceof Image) {
                        $share['avatar'] = Image::get(1);
                    }
                    
                    $array[] = (object) $share;
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Base\Core\Exception($e->getMessage());
            }
		}
        
    }
    
}