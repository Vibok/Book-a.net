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

$booka = $this['booka'];
?>
<?php if (count($booka->gallery2) > 1) : ?>
    <script type="text/javascript" >
        $(function(){
            $('#booka-gallery').slides({
                container: 'booka-gallery-container',
                paginationClass: 'slderpag',
                generatePagination: false,
                effect: 'fade',
                fadeSpeed: 200,
                play: 7000
            });
        });
    </script>
<?php endif; ?>
<?php if (!empty($booka->about)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-about'); ?></h4>
        <?php echo $booka->about; ?>
    </div>    
<?php endif; ?>

<?php if (!empty($booka->goal)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-goal'); ?></h4>
        <?php echo $booka->goal; ?>
    </div>    
<?php endif; ?>

<!-- carrusel imagenes de proceso -->
<?php if (count($booka->gallery2) > 1): ?>
<div id="booka-gallery">
    <div class="booka-gallery-container">
        <?php $i = 1; foreach ($booka->gallery2 as $image) : ?>
        <div class="gallery-image" id="gallery-image-<?php echo $i ?>">
            <img src="<?php echo $image->getLink('570', '390', true); ?>" alt="<?php echo $booka->name; ?>" />
        </div>
        <?php $i++; endforeach; ?>
    </div>
<!-- carrusel de imagenes si hay mas de una -->
    <ul class="slderpag slide-ctrl line">
        <?php $i = 1; foreach ($booka->gallery2 as $image) : ?>
        <li><a href="#" id="navi-gallery-image-<?php echo $i ?>" rel="gallery-image-<?php echo $i ?>" class="navi-gallery-image<?php echo $booka->id ?>">
    <?php echo htmlspecialchars($image->name) ?></a>
        </li>
        <?php $i++; endforeach ?>
    </ul>
<!-- carrusel de imagenes -->
</div>
<div class="legend concarrusel ft3 ct2 fs-XS wshadow">
    <?php echo $booka->caption; ?>
</div>
<?php elseif (!empty($booka->gallery2)) : ?>
    <div class="gallery-image" id="gallery-image-1"style="display:block;">
        <img src="<?php echo $booka->gallery2[0]->getLink('570', '390', true); ?>" alt="<?php echo $booka->name; ?>" />
    </div>
    <div class="legend ft3 ct2 fs-XS wshadow">
        <?php echo $booka->caption; ?>
    </div>
<?php endif; ?>

<?php if (!empty($booka->motivation)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-motivation'); ?></h4>
        <?php echo $booka->motivation; ?>
    </div>    
<?php endif; ?>

<?php if (!empty($booka->related)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-related'); ?></h4>
        <?php echo $booka->related; ?>
    </div>    
<?php endif; ?>

<div class="hr"><hr /></div>