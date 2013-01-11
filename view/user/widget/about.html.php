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

$user = $this['user'];
$bookas = count($this['invest_on']);
shuffle($user->interests);
$intereses = Text::recorta(implode(', ', $user->interests), 60);
?>
<!-- banda presentacion usuario -->
    <div id="sub-header" class="sh-user">
        <!-- un solo div para la cabecera de usuario -->
        <div class="image">
            <img src="<?php echo $user->avatar->getLink(220, 220, true); ?>" alt="<?php echo $user->name; ?>" title="<?php echo $user->name; ?>" />
        </div>
        <div class="content">
            <h2 class="underlined"><?php echo $user->name; ?></h2>
            <?php if (!empty($user->location)) : ?>
            <span class="sh-user-location ct2 bloque"><?php echo implode(', ', $user->location); ?></span>
            <?php endif; ?>
            <?php if (!empty($user->interests)) : ?>
            <span class="sh-user-interests ft3 fs-XS ct2 bloque"><?php echo Text::get('profile-interests'); ?>: <?php echo $intereses; ?></span>
            <?php endif; ?>
            <?php if (!empty($user->about)) : ?>
            <div class="sh-user-about">
                <?php 
                /*
                 * Por si hay que poner el mas info
                 * 
                if (strlen($user->about) > 300) {
                    $text = Text::recorta($user->about, 300); 
                    $text .= '<a href="#" class="plus-info">+info</a>';
                } else {
                    $text = $user->about;
                }
                 */
                
                echo '<p>'. $user->about.'</p>';
                ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="status">
            <div class="sh-user-status">
                <span class="sh-user-bookas ct1 ft3 bloque"><?php echo $bookas; ?> <?php echo Text::get('user-invested-bookas'); ?></span>
                <span class="sh-user-level ct2 ft3 bloque"><?php echo Text::get('profile-user-level'); ?> <?php echo $user->level; ?></span>
            </div>

            <!-- Los botones sociales -->
        <?php if (!empty($user->facebook) || !empty($user->google) || !empty($user->twitter) || !empty($user->linkedin)): ?>
            <ul id="sh-user-social" class="line">
                <?php if (!empty($user->linkedin)): ?>
                <li><a class="social-button linkedin" href="<?php echo htmlspecialchars($user->linkedin) ?>" target="_blank"><?php echo Text::get('regular-linkedin'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->twitter)): ?>
                <li><a class="social-button twitter" href="<?php echo htmlspecialchars($user->twitter) ?>" target="_blank"><?php echo Text::get('regular-twitter'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li><a class="social-button google" href="<?php echo htmlspecialchars($user->google) ?>" target="_blank"><?php echo Text::get('regular-google'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->facebook)): ?>
                <li><a class="social-button facebook" href="<?php echo htmlspecialchars($user->facebook) ?>" target="_blank"><?php echo Text::get('regular-facebook'); ?></a></li>
                <?php endif ?>
            </ul>                
        <?php endif ?>
        </div>
        
        <?php if (!empty($user->web)) : ?>
        <div class="sh-user-web">
            <a class="ct3 ft3" href="<?php echo $user->fullweb; ?>" target="_blank"><?php echo $user->web; ?></a>
        </div>
        <?php endif; ?>
    </div>
