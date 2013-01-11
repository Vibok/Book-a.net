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

namespace Base\Controller {

    use Base\Core\View,
        Base\Library\Text,
        Base\Library\Advice,
        Base\Model\Post,
        Base\Model\Collection,
        Base\Model\Category,
        Base\Core\Redirection;

    class Blog extends \Base\Core\Controller {
        
        public function index ($id = null) {

            // categorias
            $categories = Category::getAll(6);
            
            
            $side = array(
                'posts' => array(
                    'title' => Text::get('blog-side-last_posts'),
                    'items' => Post::getAll(null, true, 10)
                ),
                'comments' => array(
                    'title' => Text::get('blog-side-last_comments'),
                    'items' => Post\Comment::getLast(10)
                ),
                'collections' => array(
                    'title' => Text::get('blog-side-collections'),
                    'items' => Collection::getList(true, true)
                ),
                'tags' => array(
                    'title' => Text::get('blog-side-tags'),
                    'items' => Post\Tag::getList(10, true)
                )
            );
            
            if (!empty($id)) {
                $post = Post::get($id);
                $navi = Post::navi($id);

                if (!$post instanceof Post || (!$post->publish && !isset($_GET['preview'])) || (isset($_GET['preview']) && $_GET['preview'] != $_SESSION['user']->id)) {
                    Advice::Error('La entrada que buscas no está publicada');
                    throw new Redirection('/blog');
                }

                // segun eso montamos la vista
                return new View(
                    'view/blog/index.html.php',
                    array(
                        'show' => 'post',
                        'post' => $post,
                        'navi' => $navi,
                        'side' => $side,
                        'home' => '/blog',
                        'categories' => $categories
                    )
                 );
            }

            $filters = array();
            if (isset($_GET['tag'])) {
                $filters['tag'] = $_GET['tag'];
            }

            if (isset($_GET['collection'])) {
                $filters['collection'] = $_GET['collection'];
            }

            $posts = Post::getAll($filters);

            if (empty($posts)) {
                if (!empty($filters)) {
                    Advice::Error('No hay entradas de este tipo');
                    throw new Redirection('/blog');
                } else {
                    throw new Redirection('/');
                }
            }
            
            // segun eso montamos la vista
            return new View(
                'view/blog/index.html.php',
                array(
                    'show' => 'list',
                    'filters'  => $filters,
                    'posts' => $posts,
                    'side' => $side,
                    'home' => '/',
                    'categories' => $categories
                )
             );

        }

    }
    
}