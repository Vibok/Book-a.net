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
    Base\Model\Image;

$collection = $this['collection'];

/* 
 * La barra de color sobra
    <div class="lcol" style="background-color:<?php echo Text::rgb($collection->color, 0.5); ?>;">&nbsp;</div> 
 */
?>
<!-- banda presentacion collection -->
    <div id="sub-header" class="sh-booka">
        <!-- un solo div para la cabecera del collection -->
        <div class="sh-booka-block">
            <div class="image">
                <?php if (!empty($collection->image) && $collection->image instanceof Image) : ?>
                <div class="sh-booka-images">
                    <div class="brace">
                        <img src="<?php echo $collection->image->getLink('436', '297', true); ?>" alt="IMAGE" title="<?php echo $image->name; ?>" />
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="content">
                <h2 class="underlined"><a href="/collection/<?php echo $collection->id; ?>" style="color:<?php echo '#'.$collection->color; ?>;"><?php echo $collection->name; ?></a></h2>
                <span class="sh-booka-subtitle ct2 bloque"><?php echo $collection->director; ?></span>
                <span class="sh-booka-keywords ft2 ct2 bloque"><?php echo $collection->keywords; ?></span>
                <?php echo $collection->description; ?>
                <?php if ($this['show'] == 'list') : ?>
                <div class="sh-booka-collection-menu"><a href="/collection/<?php echo $collection->id; ?>" class="ct3 ft3 fs-XS rollover"><?php echo Text::get('collection-menu-contents'); ?></a> | <a href="/collection/<?php echo $collection->id; ?>/bookas" class="ct3 ft3 fs-XS rollover"><?php echo Text::get('collection-menu-bookas'); ?></a></div>
                <?php endif; ?>
            </div>
        </div>
        
        
    </div>
