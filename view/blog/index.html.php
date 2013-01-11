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
use Base\Library\Text,
    Base\Library\Advice,
    Base\Library\Navi,
    Base\Core\View;

$posts = $this['posts'];
$post = $this['post'];
$filters = $this['filters'];
$side = $this['side'];
$navi = $this['navi'];

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters = "&{$key}={$value}";
}

$bodyClass = 'blog';
$home = $this['home'];

$lang = (LANG != 'es') ? '?lang=' . LANG : '';

// metas og: para que al compartir en facebook coja bien el nombre y la imagen (todas las de proyecto y las novedades
// si es portada, todas las imagenes del blog
if ($this['show'] == 'list') {
    $ogmeta = array(
        'title' => Text::get('logo-booka'),
        'description' => Text::get('regular-by').' Book-a',
        'url' => SITE_URL . '/blog'
    );

    foreach ($posts as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $ogmeta['image'][] = $pbimg->getLink(570, 344);
                }
            }
        } elseif (!empty($post->image)) {
            $ogmeta['image'][] = $post->image->getLink(570, 344);
        }
    }
} elseif ($this['show'] == 'post') {
    $ogmeta = array(
        'title' => trim(str_replace('<br />', ' ', $post->title)),
        'description' => Text::get('regular-by').' '.$post->user->name,
        'url' => SITE_URL . '/blog/'.$post->id
    );

    if (count($post->gallery) > 1) {
        foreach ($post->gallery as $pbimg) {
            if ($pbimg instanceof Image) {
                $ogmeta['image'][] = $pbimg->getLink(570, 344);
            }
        }
    } elseif (!empty($post->image)) {
        $ogmeta['image'] = $post->image->getLink(570, 344);
    }
}


include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
	<div id="main" class="sided">
        
        <?php // echo new View('view/home/navi.html.php', array('categories' => $this['categories'])); ?>
        
		<div class="center">
			<?php
            switch ($this['show']) {
                case 'list':
                    
                    // paginacion
                    $pg = Navi::calcPages(count($posts), $_GET['page'], 20);
                    
                    if (!empty($posts)) {
                        $p = 0;
                        foreach ($posts as $post) {
                            $p++;
                            if ($p < $pg['from'] || $p > $pg['to']) continue;
                            echo new View('view/blog/post.html.php', array('post'=>$post, 'show' => 'list')); 
                        }
                        echo Navi::pageHtml(array('back' => $pg['prev'], 'go'=>$pg['next'], 'white'=>true));
                    }
                    break;
                case 'post': 
                    echo new View('view/blog/post.html.php', array('post'=>$post, 'show' => 'post')); 
                    if ($post->allow) : ?>
                        <div class="center-widget">
                            <h3 class="fs-L ct2 upcase wshadow underlined"><?php echo Text::get('blog-coments-header'); ?></h3>
                        <?php
                            echo new View('view/blog/comments.html.php', $this);
                            echo new View('view/blog/sendComment.html.php', $this);
                        ?>
                        </div>

                <?php if (empty($_SESSION['user'])) include 'view/blog/advice.html.php'; ?>
                <?php endif; ?>
                    <!-- Paginación -->
                    <div class="results-bar">
                        <ul class="page-control line">
                            <?php if (!empty($navi['prev'])) : ?>
                            <li><a class="prev-page fs-XS ft3 ct3" href="/blog/<?php echo $navi['prev']; ?>">< <?php echo Text::get('regular-prev'); ?></a></li>
                            <?php else : ?>
                            <li></li>
                            <?php endif; ?>
                            <?php if (!empty($navi['next'])) : ?>
                            <li><a class="next-page fs-XS ft3 ct3" href="/blog/<?php echo $navi['next']; ?>"><?php echo Text::get('regular-next'); ?> ></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php 
                    break;
            } ?>
		</div>
		<div class="side">
        <?php foreach ($side as $type=>$data) {
            echo new View('view/blog/side.html.php', array('type'=>$type, 'title'=>$data['title'], 'items'=>$data['items'])) ; 
        }
        ?>
                    <div class="side-widget">
                        <ul class="blog-side-list">
                            <li><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?php echo $lang ?>" target="_blank"><?php echo Text::get('regular-share-rss'); ?></a></li>
                        </ul>
                    </div>
		</div>

	</div>
<?php
    include 'view/footer.html.php';
	include 'view/epilogue.html.php';
