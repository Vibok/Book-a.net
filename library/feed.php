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
namespace Base\Library {

	use Base\Core\Model,
        Base\Model\Post,
        Base\Model\Booka,
        Base\Model\User,
        Base\Model\Image,
        Base\Library\Text;

	/*
	 * Clase para loguear eventos
	 */
    class Feed {

        public
            $id,
            $title, // titulo entrada o nombre usuario
            $url = null, // enlace del titulo
            $image = null, // enlace del titulo
            $scope = 'admin', // ambito del evento (public, admin, private)
            $type =  'system', // tipo de evento  ($public_types , $admin_types, $private_types)
            $timeago, // el hace tanto
            $date, // fecha y hora del evento
            $html, // contenido del evento en codigo html
            $unique = false, // si es un evento unique, no lo grabamos si ya hay un evento con esa url
            $unique_issue = false, // si se encuentra con que esta duplicando el feed
            $text,  // id del texto dinamico
            $params,  // (array serializado en bd) parametros para el texto dinamico
            $target_type, // tipo de objetivo del evento (user, project, call, node, etc..) normalmente un libro semilla
            $target_id; // id registro del objetivo (normalmente varchar(50))

        static public $admin_types = array(
            'all' => array(
                'label' => 'Todo',
                'color' => 'light-blue'
            ),
            'admin' => array(
                'label' => 'Administrador',
                'color' => 'red'
            ),
            'user' => array(
                'label' => 'Usuario',
                'color' => 'blue'
            ),
            'project' => array(
                'label' => 'Proyecto',
                'color' => 'light-blue'
            ),
            'call' => array(
                'label' => 'Convocatoria',
                'color' => 'light-blue'
            ),
            'money' => array(
                'label' => 'Transferencias',
                'color' => 'violet'
            ),
            'system' => array(
                'label' => 'Sistema',
                'color' => 'grey'
            )
        );

        static public $public_types = array(
            'community' => array(
                'label' => 'Novedades'
            ),
            'users' => array(
                'label' => 'Intercambios'
            )
        );

        static public $color = array(
            'user' => 'blue',
            'booka' => 'light-blue',
            'blog' => 'grey',
            'money' => 'violet',
            'relevant' => 'red',
            'comment' => 'green',
            'update-comment' => 'grey',
            'message' => 'green',
            'system' => 'grey',
            'update' => 'grey'
        );

        static public $page = array(
            'user' => '/user/profile/',
            'booka' => '/booka/',
            'blog' => '/blog/',
            'relevant' => '',
            'comment' => '/blog/',
            'message' => '/booka/',
            'system' => '/admin/'
        );

        /**
         * Metodo que rellena instancia
         * No usamos el __construct para no joder el fetch_CLASS
         */
        public function populate($title, $url, $html, $image = null) {
            $this->title = $title;
            $this->url = $url;
            $this->html = $html;
            $this->image = $image;
        }

        /**
         * Metodo que establece el elemento al que afecta el evento
         *
         * Sufridor del evento: tipo (tabla) & id registro
         *
         * @param $id string normalmente varchar(50)
         * @param $type string (project, user, node, call, etc...)
         */
        public function setTarget ($id, $type = 'booka') {
            $this->target_id = $id;
            $this->target_type = $type;
        }

        /*
         * Metodo que pone el texto dinámico
         */
        public function setText ($id) {
            $this->text = $id;
        }

        /*
         * Metodo que pone los parametros para el texto
         */
        public function setParams ($array) {
            $this->params = serialize($array);
        }

        public function doAdmin ($type = 'system') {
            $this->doEvent('admin', $type);
        }

        public function doPublic ($type = 'community') {
            $this->doEvent('public', $type);
        }

        private function doEvent ($scope = 'admin', $type = 'system') {
            $this->scope = $scope;
            $this->type = $type;
            $this->add();
        }

        /**
		 *  Metodo para sacar los eventos
         *
         * @param string $type  tipo de evento (public: columnas comunidad;  admin: categorias de filtro)
         * @param string $scope ambito de eventos (public | admin)
         * @return array list of items
		 */
		public static function getAll($type = 'all', $scope = 'public', $limit = '99') {

            $list = array();

            try {
                $values = array(':scope' => $scope);

                $sqlType = '';
                if ($type != 'all') {
                    $sqlType = " AND feed.type = :type";
                    $values[':type'] = $type;
                }

                $sql = "SELECT
                            feed.id as id,
                            feed.title as title,
                            feed.url as url,
                            feed.image as image,
                            feed.datetime as timer,
                            feed.html as html,
                            feed.target_type as target_type,
                            feed.target_id as target_id
                        FROM feed
                        WHERE feed.scope = :scope $sqlType
                        ORDER BY feed.datetime DESC
                        LIMIT $limit
                        ";
                        //  , feed.text as text, feed.params as params

                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                    //hace tanto
                    $item->timeago = self::time_ago($item->timer);

                    // para la miniatura rectangular de booka
                    if ($item->target_type == 'booka') {
                        $item->booka = Booka::getMini($item->target_id);
                        if ($item->booka->image instanceof Image) {
                            $item->booka->image = $item->booka->image->id;
                        } else {
                            // imagen por defecto para bookas
                            $item->booka->image = 2;
                        }
                    }

                    $list[] = $item;
                }
                return $list;
            } catch (\PDOException $e) {
                throw new Exception('FATAL ERROR SQL: ' . $e->getMessage() . "<br />$sql<br /><pre>" . print_r($values, 1) . "</pre>");
            }
		}

        /**
		 * Metodo para sacar novedades desde la base de datos y no desde el feed
         * Saca un array con las dos colmnas
         * 
         * 
         * @param string $type para tipos de eventos que queremos obtener
         * @param int $limit para limitar la cantidad de registros que sacamos
         * @return array list of items (como getAll)
         * 
         */
		public static function getCommunity($limit = 99) {
            $community = array();
            $bookas    = array();
            $posts     = array();

            try {
                // primero novedades
                    // nuevo booka publicado, entrada en blog, comentario en blog
                    // sql para sacar estos registros cada uno de su tabla y ordenar por fecha
                $sql = "
(	SELECT 
		CONCAT('post') as type, 
		post.id as item, 
		post.id as id, 
		user.name as author, 
		user.id as user, 
		CONCAT(post.date, ' ', DATE_FORMAT(feed.datetime, '%H:%i:%s')) as timer
	FROM post
	LEFT JOIN feed
		ON feed.target_type = 'post'
		AND feed.target_id = post.id
		AND feed.scope = 'public'
		AND feed.type = 'community'
    INNER JOIN user
        ON user.id = post.author
	WHERE post.publish = 1
	GROUP BY post.id
	)
UNION
(	SELECT 
		CONCAT('comment') as type, 
		comment.post as item, 
		comment.id as id, 
		user.name as author, 
		user.id as user, 
		DATE_FORMAT(comment.date, '%Y-%m-%d %H:%i:%s') as timer
	FROM comment
    INNER JOIN post
        ON post.id = comment.post
        AND post.publish = 1
    INNER JOIN user
        ON user.id = comment.user
	)
UNION
(	SELECT 
		CONCAT('booka') as type, 
		booka.id as item, 
		booka.id as id, 
		booka.author as author, 
		booka.owner as user, 
		CONCAT(booka.published, ' ', DATE_FORMAT(feed.datetime, '%H:%i:%s')) as timer
	FROM booka 
	LEFT JOIN feed
		ON feed.target_type = 'booka'
		AND feed.target_id = booka.id
		AND feed.scope = 'public'
		AND feed.type = 'community'
	WHERE status = 3
	GROUP BY booka.id
)
ORDER BY timer DESC
LIMIT {$limit}
";
                
                $query = Model::query($sql, $values);
                foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {

                    // para cada registro, segun el tipo, le sacamos la imagen, el título, el enlace y el html (autor)
                        // miro si tenemos el post/booka en el array
                            // si lo tengo lo cojo de ahí
                            // si no lo tengo, e hago un getMini, lo cojo y lo pongo
                    switch ($item->type) {
                        case 'booka':
                            if (!isset($bookas[$item->item])) {
                                $bookas[$item->item] = Booka::getMedium($item->item);
                            }
                            $item->url = '/booka/'.$item->item;
                            if ($bookas[$item->item]->image instanceof Image) {
                                $item->image = $bookas[$item->item]->image->id;
                            } else {
                                // imagen por defecto para un libro-semilla
                                $item->image = 2;
                            }
                            $item->title = $bookas[$item->item]->clr_name;
                            $item->html = Text::html('feed-new_booka', static::item('relevant', $item->author));
                            
                            break;
                        case 'post':
                            if (!isset($posts[$item->item])) {
                                $posts[$item->item] = Post::getMini($item->item);
                            }
                            $item->url = '/blog/'.$item->item;
                            if ($posts[$item->item]->image instanceof Image) {
                                $item->image = $posts[$item->item]->image->id;
                            } else {
                                // imagen por defecto para un post
                                $item->image = 3;
                            }
                            $item->title = $posts[$item->item]->clr_title;
                            $item->html = Text::get('feed-new_post', 
                                    static::item('blog', 'el blog', '/'),
                                    static::item('user', $item->author, $item->user)
                            );
                            
                            break;
                        case 'comment':
                            if (!isset($posts[$item->item])) {
                                $posts[$item->item] = Post::getMini($item->item);
                            }
                            $item->url = '/blog/'.$item->item.'#comment'.$item->id;
                            if ($posts[$item->item]->image instanceof Image) {
                                $item->image = $posts[$item->item]->image->id;
                            } else {
                                // imagen por defecto para un post
                                $item->image = 3;
                            }
                            $item->title = $posts[$item->item]->clr_title;
                            $item->html = Text::get('feed-new_comment', 
                                self::item('comment', Text::get('regular-comment'), $item->item.'#comment'.$item->id),
                                self::item('user', $item->author, $item->user)
                            );
                            
                            break;
                    }
                    
                    //hace tanto
                    $item->timeago = self::time_ago($item->timer);
                    $community[] = $item;
                }
                
                
                // luego las acciones de usuario, directamente desde feed
                //@TODO sacarlo igual que community para traducir
                $users = self::getAll('users', 'public', $limit);
                
                $list = array(
                    'community' => $community,
                    'users' => $users
                );
                
                return $list;
            } catch (\PDOException $e) {
                return array();
            }
		}

		/**
		 *  Metodo para grabar eventos
         *
         *  Los datos del evento estan en el objeto
         *
         *
         * @param array $errors
         *
         * @access public
         * @return boolean true | false   as success
         *
		 */
		public function add() {

            if (empty($this->html)) {
                return false;
            }

            // primero, verificar si es unique, no duplicarlo
            if ($this->unique === true) {
                Model::query("DELETE FROM feed WHERE scope = :scope AND type = :type AND target_type = :target_type AND target_id = :target_id",
                    array(
                    ':scope' => $this->scope,
                    ':type' => $this->type,
                    ':target_type' => $this->target_type,
                    ':target_id' => $this->target_id
                ));
            }

  			try {
                $values = array(
                    ':title' => $this->title,
                    ':url' => $this->url,
                    ':image' => $this->image,
                    ':scope' => $this->scope,
                    ':type' => $this->type,
                    ':html' => $this->html,
                    ':text' => $this->text,
                    ':params' => $this->params,
                    ':target_type' => $this->target_type,
                    ':target_id' => $this->target_id
                );

				$sql = "INSERT INTO feed
                            (id, title, url, scope, type, html, text, params, image, target_type, target_id)
                        VALUES
                            ('', :title, :url, :scope, :type, :html, :text, :params, :image, :target_type, :target_id)
                        ";
				if (Model::query($sql, $values)) {
                    return true;
                } else {
                    return false;
                }
                
			} catch(\PDOException $e) {
                return false;
			}

		}
        
        /**
         * Metodo para transformar un TIMESTAMP en un "hace tanto"
         * 
         * Los periodos vienen de un texto tipo singular-plural_sg-pl_id-sg-pl_...
         * en mismo orden y cantidad que los per_id
         */
        public static function time_ago($date,$granularity=1) {

            $per_id = array('sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'dec');

            $per_txt = array();
            foreach (\explode('_', Text::get('feed-timeago-periods')) as $key=>$grptxt) {
                $per_txt[$per_id[$key]] = \explode('-', $grptxt);
            }

            $justnow = Text::get('feed-timeago-justnow');

            $retval = '';
            $date = strtotime($date);
            $ahora = time();
            $difference = $ahora - $date;
            $periods = array('dec' => 315360000,
                'year' => 31536000,
                'month' => 2628000,
                'week' => 604800,
                'day' => 86400,
                'hour' => 3600,
                'min' => 60,
                'sec' => 1);

            foreach ($periods as $key => $value) {
                if ($difference >= $value) {
                    $time = floor($difference/$value);
                    $difference %= $value;
                    $retval .= ($retval ? ' ' : '').$time.' ';
                    $retval .= (($time > 1) ? $per_txt[$key][1] : $per_txt[$key][0]);
                    $granularity--;
                }
                if ($granularity == '0') { break; }
            }

            return empty($retval) ? $justnow : $retval;
        }

        /**
         *  Genera codigo html para enlace o texto dentro de feed
         *
         */
        public static function item ($type = 'system', $label = 'label', $id = null) {

            // si llega id es un enlace
            if (isset($id)) {
                return '<a href="'.self::$page[$type].$id.'" class="ct1" target="_blank">'.$label.'</a>';
            } else {
                return '<span class="ct3">'.$label.'</span>';
            }
            
            /*
            if (isset($id)) {
                return '<a href="'.self::$page[$type].$id.'" class="'.self::$color[$type].'" target="_blank">'.$label.'</a>';
            } else {
                return '<span class="'.self::$color[$type].'">'.$label.'</span>';
            }
             */


        }

        /**
         *  Genera codigo html para feed público
         *
         *  segun tenga imagen, ebnlace, titulo, tipo de enlace
         *
         */
        public static function subItem ($item) {

            $pub_timeago = Text::get('feed-timeago-published', $item->timeago);

            // si enlace -> título como texto del enlace
            if (substr($item->url, 0, 5) == '/user') {
                $content = '<div class="subitem user">';
                
                $content .= '<div class="image">
                <a href="'.$item->url.'" class="avatar"><img src="'.SRC_URL.'/image/'.$item->image.'/60/60/1" /></a>';
                
                if ($item->target_type == 'booka') {
                    $content .= '<a href="/booka/'.$item->target_id.'" class="booka"><img src="'.SRC_URL.'/image/'.$item->booka->image.'/37/60/1" /></a>';
                }
                
                $content .= '</div>';
            } else {
                $content = '<div class="subitem">';
                
                $content .= '<div class="image">
                <a href="'.$item->url.'" class="image"><img src="'.SRC_URL.'/image/'.$item->image.'/75/60/1" /></a>
                </div>';
            }
            
            // y el contenido
                $content .= '<div class="content">
                    <a class="title ct1 fs-S bloque" href="'.$item->url.'">'.$item->title.'</a>
                    <span class="ct2 wshadow fs-XS bloque">'.$pub_timeago.'</span>
                ';
            
               // y lo que venga en el html
               $content .= '<div class="content-pub">'.$item->html.'</div>';

               $content .= '</div>';
           $content .= '<br clear="both" />
               </div>';

           return $content;
        }

    }
}