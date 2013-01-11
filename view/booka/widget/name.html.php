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

$booka = $this['booka'];

$fecha = date('d | m | Y', strtotime($booka->published));
$fecha_prod = date('d | m | Y', strtotime($booka->success));
?>
<!-- banda presentacion booka -->
    <div id="sub-header" class="sh-booka">
        <!-- un solo div para la cabecera del booka -->
        <div class="sh-booka-block">
            <div class="image">
                <div class="sh-booka-images">
                    <?php foreach ($booka->gallery as $image) : 
                        if (!$image instanceof Image) continue; 
                        ?>
                    <div class="brace">
                        <img src="<?php echo $image->getLink('436', '297', true); ?>" alt="IMAGE" title="<?php echo $image->name; ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="content">
                <h2 class="underlined"><?php echo $booka->name; ?></h2> <!-- color inline segun colección -->
                <span class="sh-booka-subtitle ct2 bloque"><?php echo $booka->author; ?></span>
                <span class="sh-booka-author ft2 ct2 bloque"><?php echo $booka->info; ?></span>
                <p><?php echo $booka->description; //@TODO mirar recorte de palabras ?></p>
            </div>
        </div>
        
        <!-- Los botones del carrusel DE FOTOS van por delante -->
        <ul id="sh-booka-controller" class="slide-ctrl line"></ul>
        
        <?php if ($booka->status == 3) : ?>
        <div class="sh-booka-date fs-XS ft3 ct2 wshadow"><?php echo Text::get('booka-launch_date', $fecha); ?></div>
        <?php else : ?>
        <div class="sh-booka-date fs-XS ft3 ct2 wshadow"><?php echo Text::get('booka-produced_date', $fecha_prod); ?></div>
        <?php endif; ?>
        
    </div>
