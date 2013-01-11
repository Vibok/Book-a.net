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

namespace Base\Controller\Admin {

    use Base\Core\View,
        Base\Core\Redirection,
        Base\Core\Error,
		Base\Library\Text,
		Base\Library\Feed,
		Base\Library\Advice,
        Base\Model;

    class Blog {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            $url = '/admin/blog';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $editing = false;

                    if (!empty($_POST['id'])) {
                        $post = Model\Post::get($_POST['id']);
                    } else {
                        $post = new Model\Post();
                    }
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'title_es',
                        'title_en',
                        'text_es',
                        'text_en',
                        'image',
                        /*
                        'media_es',
                        'media_en',
                         */
                        'legend_es',
                        'legend_en',
                        'url',
                        'date',
                        'allow',
                        'booka',
                        'author',
                        'publish'
                    );

                    foreach ($fields as $field) {
                        $post->$field = $_POST[$field];
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if (isset($_POST['upload'])) {
                        $editing = true;
                    }
                    if(!empty($_FILES['image_upload']['name'])) {
                        $post->image = $_FILES['image_upload'];
                    }

                    // tratar las imagenes que quitan
                    foreach ($post->gallery as $key=>$image) {
                        if (!empty($_POST["gallery-{$image->id}-remove"])) {
                            $image->remove('post');
                            unset($post->gallery[$key]);
                            if ($post->image == $image->id) {
                                $post->image = '';
                            }
                            $editing = true;
                        }
                    }

                    /*
                    if (!empty($post->media_es)) {
                        $post->media_es = new Model\Booka\Media($post->media_es);
                    }

                    if (!empty($post->media_en)) {
                        $post->media_en = new Model\Booka\Media($post->media_en);
                    }
                     * 
                     */

                    $post->tags = $_POST['tags'];
                    
                    // si tenemos un nuevio tag hay que añadirlo
                    if(!empty($_POST['new-tag'])) {

                        // grabar el tag en la tabla de tag,
                        $new_tag = new Model\Post\Tag(array(
                            'id' => '',
                            'name_es' => $_POST['new-tag']
                        ));

                        if ($new_tag->save($errors)) {
                            $post->tags[] = $new_tag->id; // asignar al post
                        } else {
                            Advice::Error(implode('<br />', $errors));
                        }

                        $editing = true; // seguir editando
                    }

                    $post->user = (!empty($post->author)) ? Model\User::getMini($post->author) : Model\User::getMini('booka');
                    
                    /// este es el único save que se lanza desde un metodo process_
                    if ($post->save($errors)) {
                        if ($action == 'edit') {
//                            Advice::Info('La entrada se ha actualizado correctamente');
                        } else {
//                            Advice::Info('Se ha añadido una nueva entrada');
                            $id = $post->id;
                        }
                        if ($editing) {
                            $action = 'edit';
                        } else {
                            if ((bool) $post->publish) {
                                // Evento Feed
                                $log = new Feed();
                                $log_html = Text::get('feed-new_post', array(
                                    Feed::item('blog', 'el blog', '/'),
                                    Feed::item('user', $post->user->name, $post->user->id)
                                ));
                                $log->unique = true;
                                $log->populate($post->title, '/blog/'.$post->id, $log_html, $post->gallery[0]->id);
                                $log->setTarget($post->id, 'post');
                                $log->doPublic('community');
                                unset($log);
                            } else {
                                //sino lo quitamos
                                \Base\Core\Model::query("DELETE FROM feed WHERE url = '/blog/{$post->id}' AND scope = 'public' AND type = 'community'");
                            }

                            throw new Redirection('/admin/blog');
                        }

                    } else {
                        Advice::Error('Ha habido algun problema al guardar los datos:<br />' . \implode('<br />', $errors));
                    }
            }

            switch ($action)  {
                case 'list':
                    // lista de entradas
                    // filtro por defecto para directores
                    if (empty($filters['show']) && isset($_SESSION['user']->roles['director']))  {
                        $filters['show'] = 'mine';
                    }

                    // obtenemos los datos
                    $show = array(
                        'all' => 'Todas las entradas existentes',
                        'mine' => 'Solamente mis entradas',
                        'published' => 'Solamente las publicadas',
//                        'allow' => 'Solamente las asociadas a bookallow',
//                        'ateca' => 'Solamente las asociadas a bookateca',
//                        'alacarte' => 'Solamente las asociadas a bookalacarte',
                        'home' => 'Solamente las de Home',
                        'footer' => 'Solamente las de Footer',
//                        'top' => 'Solamente las de Top'
                    );
                    
                    if ($filters['show'] == 'mine') {
                        $filters['author'] = $_SESSION['user']->id;
                    }
                    $posts   = Model\Post::getAll($filters, false);
//                    $tops    = Model\Post::getList('top');
                    $homes   = Model\Post::getList('home');
                    $footers = Model\Post::getList('footer');

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'list',
                            'posts' => $posts,
                            'status' => Model\Booka::status(),
                            'filters' => $filters,
                            'show' => $show,
                            'tops' => $tops,
                            'homes' => $homes,
                            'footers' => $footers
                        )
                    );
                    break;
                case 'add':
                    // nueva entrada con wisiwig
                    // obtenemos datos basicos
                    $post = new Model\Post(
                            array(
                                'date' => date('Y-m-d'),
                                'publish' => false,
                                'allow' => true,
                                'tags' => array(),
                                'author' => $_SESSION['user']->id
                            )
                        );

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'add',
                            'post' => $post,
                            'tags' => Model\Post\Tag::getAll(),
                            'bookas' => Model\Booka::getList(),
                            'status' => Model\Booka::status()
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        Advice::Error('No se ha encontrado la entrada');
                        throw new Redirection('/admin/blog');
                        break;
                    } else {
                        $post = Model\Post::get($id);

                        if (!$post instanceof Model\Post) {
                            Advice::Error('La entrada esta corrupta, contacte con nosotros.');
                            throw new Redirection('/admin/blog');
                        }
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'edit',
                            'post' => $post,
                            'tags' => Model\Post\Tag::getAll(),
                            'bookas' => Model\Booka::getList(),
                            'status' => Model\Booka::status()
                        )
                    );
                    break;
                case 'remove':
                    // eliminar una entrada
//                    $tempData = Model\Post::get($id);
                    if (Model\Post::delete($id)) {
                        /*
                         * Evento Feed
                        $log = new Feed();
                        $log->populate('Quita entrada de blog (admin)', '/admin/blog',
                            \vsprintf('El admin %s ha %s la entrada "%s" las Noticias', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Quitado'),
                                Feed::item('blog', $tempData->title)
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                         * 
                         */

//                        Advice::Info('Entrada eliminada');
                    } else {
                        Advice::Error('No se ha podido eliminar la entrada');
                    }
                    throw new Redirection('/admin/blog');
                    break;

                // tratamiento home
                case 'home':
                    // lista de entradas en portada
                    // obtenemos los datos
                    $posts = Model\Post::getAll(array('show'=>'home'));

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'order',
                            'type' => 'home',
                            'posts' => $posts
                        )
                    );
                    break;
                case 'up_home':
                    Model\Post::up($id, 'home');
                    throw new Redirection('/admin/blog/home');
                    break;
                case 'down_home':
                    Model\Post::down($id, 'home');
                    throw new Redirection('/admin/blog/home');
                    break;
                case 'add_home':
                    // siguiente orden
                    $next = Model\Post::next('home');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => $next,
                        'home' => 1
                    ));

                    if ($post->update($errors)) {
//                        Advice::Info('Entrada puesta en la Home correctamente');
                    } else {
                        Advice::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove_home':
                    // se quita de la portada solamente
                    $ok = false;
                    $ok = Model\Post::remove($id, 'home');
                    if ($ok) {
//                        Advice::Info('Entrada quitada de la Home correctamente');
                    } else {
                        Advice::Error('No se ha podido quitar esta entrada de la Home');
                    }
                    throw new Redirection('/admin/blog');
                    break;

                // tratamiento footer
                case 'footer':
                    // lista de entradas en el footer
                    // obtenemos los datos
                    $posts = Model\Post::getAll(array('show'=>'footer'));

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'order',
                            'type' => 'footer',
                            'posts' => $posts
                        )
                    );
                    break;
                case 'up_footer':
                    Model\Post::up($id, 'footer');
                    throw new Redirection('/admin/blog/footer');
                    break;
                case 'down_footer':
                    Model\Post::down($id, 'footer');
                    throw new Redirection('/admin/blog/footer');
                    break;
                case 'add_footer':
                    // siguiente orden
                    $next = Model\Post::next('footer');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => $next,
                        'footer' => 1
                    ));

                    if ($post->update($errors)) {
//                        Advice::Info('Entrada puesta en Footer correctamente');
                    } else {
                        Advice::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove_footer':
                    // se quita del footer solamente
                    if (Model\Post::remove($id, 'footer')) {
//                        Advice::Info('Entrada quitada del Footer correctamente');
                    } else {
                        Advice::Error('No se ha podido quitar esta entrada del Footer');
                    }
                    throw new Redirection('/admin/blog');
                    break;


                // tratamiento top
                case 'top':
                    // lista de entradas en el footer
                    // obtenemos los datos
                    $posts = Model\Post::getAll(array('show'=>'top'));

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'order',
                            'type' => 'top',
                            'posts' => $posts
                        )
                    );
                    break;
                case 'up_top':
                    Model\Post::up($id, 'top');
                    throw new Redirection('/admin/blog/top');
                    break;
                case 'down_top':
                    Model\Post::down($id, 'top');
                    throw new Redirection('/admin/blog/top');
                    break;
                case 'add_top':
                    // siguiente orden
                    $next = Model\Post::next('top');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => $next,
                        'top' => 1
                    ));

                    if ($post->update($errors)) {
//                        Advice::Info('Entrada puesta en el Top correctamente');
                    } else {
                        Advice::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove_top':
                    // se quita del footer solamente
                    if (Model\Post::remove($id, 'top')) {
//                        Advice::Info('Entrada quitada del Top correctamente');
                    } else {
                        Advice::Error('No se ha podido quitar esta entrada del Top');
                    }
                    throw new Redirection('/admin/blog');
                    break;
            }

        }

    }

}
