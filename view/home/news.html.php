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

use Base\Library\Text;

    $posts = $this['posts'];
?>
<script type="text/javascript">
    $(function(){
        $('#sub-header').slides({
            container: 'posts-container',
            paginationClass: 'slide-ctrl',
            generatePagination: true,
            effect: 'fade',
            fadeSpeed: 200,
            play: 20000
        });
    });
</script>
<!-- banda presentacion noticias -->    
    <div id="sub-header" class="sh-post">
        
        <!-- contenedor de carruseles -->
        <div class="posts-container">
            <?php foreach ($posts as $post) : ?>
            <!-- un div para cada noticia -->
            <div class="sh-post-block">
                <div class="image brace">
                    <img src="<?php if ($post->image instanceof \Base\Model\Image) echo $post->image->getLink(436, 297, true); else echo '/data/images/fotonoticia.jpg';?>" alt="IMAGEN" title="imagen" />
                </div>
                <div class="content">
                    <h2><a href="/blog/<?php echo $post->id ?>"<?php if (!empty($post->collection)) echo ' style="color: #'.$post->color.';"'; ?>><?php echo $post->title ?></a></h2> <!-- color inline segun colección -->
                    <?php echo Text::recorta($post->text, 550); ?>
                </div>
                <a href="/blog/<?php echo $post->id ?>" class="sh-post-next ct3 wshadow"><?php echo Text::get('regular-read_more'); ?> &GT;</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        
        <!-- Los botones del carrusel DE NOTICIAS van por delante -->
        <ul id="sh-post-controller" class="slide-ctrl line"></ul>

    </div>



<?php // echo \trace($posts);


/*
 * 
            <li>o</li> <!-- inactivo, vacio  -->
            <li class="active">o</li> <!-- rollover, relleno -->
            <li class="current">o</li> <!-- actual, color -->
            <li>o</li>
            <li>o</li>
 * 
 * 
 */

?>