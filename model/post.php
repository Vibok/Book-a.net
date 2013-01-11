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

    use \Base\Model\Booka\Media,
        \Base\Model\Image,
        \Base\Model\Booka,
        \Base\Model\User,
        \Base\Library\Text,
        \Base\Library\Check,
        \Base\Library\Advice;

    class Post extends \Base\Core\Model {

        public
            $id,
            $title,
            $clr_title,
            $subtitle,
            $text,
            $image,
            $author,
            $media,
            $legend,
            $date,
            $url,
            $publish,
            $home,
            $footer,
            $booka, // booka asociado a la noticia
            $tags = array(),
            $gallery = array(), // array de instancias image de post_image
            $num_comments = 0,
            $comments = array();

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        *,
                        DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                        IFNULL(title_".\LANG." , title_es) as title,
                        IFNULL(text_".\LANG."  , text_es) as text,
                        IFNULL(media_".\LANG." , media_es) as media,
                        IFNULL(legend_".\LANG.", legend_es) as legend
                    FROM    post
                    WHERE post.id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                // titulo sin (sobre)saltos :P
                $post->clr_title = str_replace('<br />', ' ', $post->title);
                
                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }

                // galeria
                $post->gallery = Image::getAll($id, 'post');
                $post->image = $post->gallery[0];

                $post->comments = Post\Comment::getAll($id);
                $post->num_comments = count($post->comments);
                
                $post->user = (!empty($post->author)) ? User::getMini($post->author) : User::getMini('booka');

                //tags
                $post->tags = Post\Tag::getAll($id);

                if (!empty($post->booka)) {
                    $post->bookaData = Booka::getMini($post->booka);
                }
                // autor
                if (!empty($post->author)) $post->user = User::getMini($post->author);

                return $post;
        }

        /*
         *  Devuelve datos mínimos de una entrada
         */
        public static function getMini ($id) {
                $query = static::query("
                    SELECT
                        IFNULL(title_".\LANG." , title_es) as title
                    FROM    post
                    WHERE post.id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                // titulo sin (sobre)saltos :P
                $post->clr_title = str_replace('<br />', ' ', $post->title);
                
                // imagen
                $post->image = Image::getFirst($id, 'post');

                return $post;
        }

        /*
         * Lista de entradas filtradas
         * de mas nueva a mas antigua
         * para gestion y para publicacion
         */
        public static function getAll ($filters = array(), $published = true, $limit = null) {

            $list = array();

            $sqlOrder = "
                ORDER BY post.date DESC, post.id DESC
                ";

            $sql = "
                SELECT
                    *,
                    DATE_FORMAT(post.date, '%d | %m | %Y') as fecha,
                    IFNULL(title_".\LANG." , title_es) as title,
                    IFNULL(text_".\LANG."  , text_es) as text,
                    IFNULL(media_".\LANG." , media_es) as media,
                    IFNULL(legend_".\LANG.", legend_es) as legend
                FROM    post
                WHERE id IS NOT NULL
                ";

            if (!empty($filters['tag'])) {
                $sql .= " AND post.id IN (SELECT post FROM post_tag WHERE tag = :tag)
                ";
                $values[':tag'] = $filters['tag'];
            }

            if (!empty($filters['collection'])) {
                $sql .= " AND post.booka IN (SELECT id FROM booka WHERE collection = :collection)
                ";
                $values[':collection'] = $filters['collection'];
            }

            if (!empty($filters['author'])) {
                $sql .= " AND post.author = :author
                ";
                $values[':author'] = $filters['author'];
            }

            // solo las publicadas
            if ($published || $filters['show'] == 'published') {
                $sql .= " AND post.publish = 1
                ";
            }

            // segun top, home, footer
            if (!empty($filters['show']) && in_array($filters['show'], array('top', 'home', 'footer'))) {
                $sql .= " AND post.{$filters['show']} = 1
                    ";
                $sqlOrder = "ORDER BY post.order ASC
                    ";
            }

            // segun estado del booka asociado
            // en campaña y financiados (estado 3 y 4)
            if ($filters['show'] == 'allow') {
                $sql .= " AND post.booka IN (SELECT id FROM booka WHERE status IN (3, 4))
                ";
            }

            // producidos (estado 5)
            if ($filters['show'] == 'ateca') {
                $sql .= " AND post.booka IN (SELECT id FROM booka WHERE status = 5)
                ";
            }

            // disponibles (estado 6)
            if ($filters['show'] == 'alacarte') {
                $sql .= " AND post.booka IN (SELECT id FROM booka WHERE status = 6)
                ";
            }

            $sql .= $sqlOrder;
            
            if (!empty($limit)) {
                $sql .= " LIMIT $limit";
            }

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                // galeria
                $post->gallery = Image::getAll($post->id, 'post');
                $post->image = $post->gallery[0];

                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }
                
                $post->user = (!empty($post->author)) ? User::getMini($post->author) : User::getMini('booka');

                if (!empty($post->booka)) {
                    $post->bookaData = Booka::getMini($post->booka);
                }

                $post->num_comments = Post\Comment::getCount($post->id);

                //tags
                $post->tags = Post\Tag::getAll($post->id);

                $list[] = $post;
            }

            return $list;
        }

        /*
         * Lista simple de entradas en posicion home/footer/top
         */
        public static function getList ($type, $published = true, $limit = null) {
            $list = array();

            $sql = "
                SELECT
                    id,
                    title_es
                FROM    post
                WHERE id IS NOT NULL
                ";
            // solo las entradas publicadas
            if ($published) {
                $sql .= " AND post.publish = 1
                ";
            }
            if (in_array($type, array('top', 'home', 'footer'))) {
                $sql .= " AND post.{$type} = 1
                    ";
            }
            $sql .= "ORDER BY post.date DESC, post.id DESC
                ";
            if (!empty($limit)) {
                $sql .= "LIMIT $limit";
            }

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                $list[$post->id] = $post->title_es;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->title_es))
                $errors['title_es'] = 'Falta título';

            if (empty($this->text_es))
                $errors['text_es'] = 'Falta texto';

            if (empty($this->date))
                $errors['date'] = 'Falta fecha';

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
                'text_es',
                'text_en',
                'media_es',
                'media_en',
                'legend_es',
                'legend_en',
                'date',
                'url',
                'allow',
                'publish',
                'home',
                'footer',
                'top',
                'booka',
                'author'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO post SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                // Luego la imagen
                if (!empty($this->id) && is_array($this->image) && !empty($this->image['name'])) {
                    $image = new Image($this->image);
                    if ($image->save($errors)) {
                        $this->gallery[] = $image;

                        /**
                         * Guarda la relación NM en la tabla 'post_image'.
                         */
                        if(!empty($image->id)) {
                            self::query("REPLACE post_image (post, image) VALUES (:post, :image)", array(':post' => $this->id, ':image' => $image->id));
                        }
                    }
                    else {
                        Advice::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    }
                }

                // y los tags, si hay
                if (!empty($this->id) && is_array($this->tags)) {
                    static::query('DELETE FROM post_tag WHERE post= ?', $this->id);
                    foreach ($this->tags as $tag) {
                        $new = new Post\Tag(
                                array(
                                    'post' => $this->id,
                                    'tag' => $tag
                                )
                            );
                        $new->assign($errors);
                        unset($new);
                    }
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada de posicion
         */
        public static function remove ($id, $from = null) {
            
            if (!in_array($from, array('home', 'footer', 'top'))) {
                return false;
            }

            $sql = "UPDATE post SET `$from`=0, `order`=NULL WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         *  Actualizar una entrada en portada
         * si es de nodo se guarda en otra tabla con el metodo update_node
         */
        public function update (&$errors = array()) {
            if (!$this->id) return false;

            $fields = array(
                'order',
                'home',
                'footer',
                'top'
                );

            $set = '';
            $values = array(':id'=>$this->id);

            foreach ($fields as $field) {
                if (!isset ($this->$field))
                    continue;
                
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            if ($set == '') {
                $errors[] = 'Sin datos';
                return false;
            }

            try {
                $sql = "UPDATE post SET " . $set . " WHERE post.id = :id";
                self::query($sql, $values);

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM post WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {

                // que elimine tambien sus imágenes
                $sql = "DELETE FROM post_image WHERE post = :id";
                self::query($sql, array(':id'=>$id));

                return true;
            } else {
                return false;
            }

        }

        
        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $type) {
            $extra = array (
                    $type => 1
                );
            return Check::reorder($id, 'up', 'post', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $type) {
            $extra = array (
                    $type => 1
                );
            return Check::reorder($id, 'down', 'post', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($type) {
            $query = self::query('SELECT MAX(`order`) FROM post WHERE '.$type.'=1');
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        /*
         * Para sacar la entrada anterior y la siguiente
         * $way puede ser 'prev' para anterior y 'next' para siguiente
         */
        public static function navi ($id) {
            
            $list = array();
            $prev = null;
            $next = null;
            
            $sql = "SELECT id FROM post WHERE publish = 1 ORDER BY post.date DESC, post.id DESC";
            
            $query = self::query($sql, array(':id' => $id));
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $post) {
                $list[$prev]['next'] = $post->id;
                $list[$post->id] = array('prev'=>$prev, 'next'=>$next);
                $prev = $post->id;
            }

            return $list[$id];
        }

        /*
         *  Para saber si una entrada permite comentarios
         */
        public static function allowed ($id) {
                $query = static::query("
                    SELECT
                        allow
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                if ($post->allow > 0) {
                    return true;
                } else {
                    return false;
                }
        }

    }
    
}