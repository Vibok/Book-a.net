<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci�n Fuentes Abiertas (see README for details)
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

namespace Base\Controller {

    use Base\Model,
        Base\Library;

    class Rss extends \Base\Core\Controller {
        
        public function index () {
            
            // sacamos su blog
            $posts = Model\Post::getAll();

            $tags = Model\Post\Tag::getAll();

            /*
            echo '<pre>'.print_r($tags, 1).'</pre>';
            echo '<pre>'.print_r($posts, 1).'</pre>';
            die;
            */

            // al ser xml no usaremos vista
            // usaremos FeedWriter

            // configuracion
            $config = array(
                'title' => 'Rss',
                'description' => 'Blog rss',
                'link' => SITE_URL,
                'indent' => 6
            );

            $data = array(
                'tags' => $tags,
                'posts' => $posts
            );

            \header("Content-Type: application/rss+xml");
            echo Library\Rss::get($config, $data, $_GET['format']);

            // le preparamos los datos y se los pasamos
        }
        
    }
    
}