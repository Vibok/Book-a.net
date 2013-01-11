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
// Vista para pintar una entrada de blog
// puede ser resumen en la lista o completa
use Base\Library\Text;

$post = $this['post'];

if ($this['show'] == 'list') {
    // substituimos el último '</p>' por el ver más
    $post->text = str_replace('...</p>', 
        '... <a href="/blog/'.$post->id.'" class="ct3">'.Text::get('regular-read_more').'</a></p>', 
        Text::recorta($post->text, 500)
        );
}

$share_title = str_replace('<br />', ' ', $post->title);

$share_url = SITE_URL . '/blog/' . $post->id;
if (LANG != 'es')
    $share_url .= '?lang=' . LANG;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url . ' #bookallow');
$google_url = 'https://m.google.com/app/plus/x/?v=compose&content=' . urlencode($share_title . ': ' . $share_url);

?>
    <?php if (count($post->gallery) > 1) : ?>
		<script type="text/javascript" >
			$(function(){
				$('#post-gallery<?php echo $post->id ?>').slides({
					container: 'post-gallery-container',
					paginationClass: 'slderpag',
					generatePagination: false,
					play: 0
				});
			});
		</script>
    <?php endif; ?>
<div class="center-widget">
	<h3 class="underlined"><a href="/blog/<?php echo $post->id; ?>" style="color:<?php echo '#'.$post->bookaData->collection->color; ?>"><?php echo $post->title; ?></a></h3>
	<span class="ct2 bloque fs-M ft2 author"><?php echo Text::get('regular-by') ?> <a href="/user/profile/<?php echo $post->user->id; ?>" class="ct2 fs-M ft2"><?php echo $post->user->name; ?></a></span>
	<span class="ct2 bloque fs-XS ft3 fecha"><?php echo $post->fecha; ?></span>
	<?php if (count($post->gallery) > 1) : 
        $sincarrusel = '';
        ?>
        <div id="post-gallery<?php echo $post->id ?>" class="post-gallery">
			<div class="post-gallery-container">
				<?php $i = 1; foreach ($post->gallery as $image) : ?>
				<div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
					<img src="<?php echo $image->getLink(570, 344); ?>" alt="<?php echo $post->title; ?>" />
				</div>
				<?php $i++; endforeach; ?>
			</div>
			<!-- carrusel de imagenes si hay mas de una -->
                <ul class="slderpag slide-ctrl line">
                    <?php $i = 1; foreach ($post->gallery as $image) : ?>
                    <li><a href="#" id="navi-gallery-post<?php echo $post->id ?>-<?php echo $i ?>" rel="gallery-post<?php echo $post->id ?>-<?php echo $i ?>" class="navi-gallery-post<?php echo $post->id ?>">
                <?php echo htmlspecialchars($image->name) ?></a>
                    </li>
                    <?php $i++; endforeach ?>
                </ul>
			<!-- carrusel de imagenes -->
		</div>
		<div class="caption concarrusel ft3 ct2 fs-XS wshadow">
			<?php echo $post->legend; ?>
		</div>
	<?php elseif (!empty($post->image)) : 
        $sincarrusel = ' sincarrusel';
        ?>
        <div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
            <img src="<?php echo $post->image->getLink(570, 344); ?>" alt="<?php echo $post->title; ?>" />
        </div>
		<div class="caption ft3 ct2 fs-XS wshadow">
			<?php echo $post->legend; ?>
		</div>
	<?php endif; ?>
	<div class="post-content<?php echo $sincarrusel ?>">
        <?php echo $post->text; ?>
    </div>
    <div class="ft3 fs-XS ct2 wshadow tags"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments') ; ?><span class="vr ct2">|</span><?php echo implode(', ', $post->tags) ?></div>
    <div class="hr"><hr /></div>
    <ul class="share line">
        <li class="link"><a class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo SITE_URL.'/blog/'.$post->id ?>'); return false;"><?php echo Text::get('regular-link') ?></a></li>
        <li class="socials">
            <span class="vr ct1 fs-XS">|</span><span class="ct1 fs-XS ft3" style="margin-right: 13px;"><?php echo Text::get('regular-share') ?>:</span>
            <a class="social-button facebook" href="<?php echo $facebook_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button twitter" href="<?php echo $twitter_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button google" href="<?php echo $google_url; ?>" target="_blank">&nbsp;</a>
        </li>
    </ul>
</div>