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

use Base\Core\View,
    Base\Model\Image,
    Base\Library\Text;

$user = $this['user'];

if (!$user->avatar instanceof Image) {
    $user->avatar = Image::get(1);
}

$url_profile = ($user->user != 'anonymous') ? '/user/profile/'.$user->user : '';
?>
<div class="user-widget-investor">
    <div class="avatar">
        <?php if (!empty($url_profile)) : ?>
            <a href="<?php echo $url_profile; ?>"><img src="<?php echo $user->avatar->getLink(60, 60, true); ?>" /></a>
        <?php else : ?>
            <img src="<?php echo $user->avatar->getLink(60, 60, true); ?>" />
        <?php endif; ?>
    </div>
    <div class="content">
        <span class="user ft2 ct1 bloque">
            <?php if (!empty($url_profile)) : 
                echo '<a class="ft2 ct1" href="'.$url_profile.'">'.$user->name.'</a>';
            else : 
                echo htmlspecialchars($user->name);
            endif; ?>
        </span>
        <span class="date fs-XS ft3 ct2 wshadow bloque" ><?php echo $user->date ?></span>
        <span class="fs-XS ft3 bloque"><?php echo Text::get('user-invested-amount'); ?></span>
        <div class="invest ft3 ct2 wshadow fs-L">
            <?php echo $user->amount; ?><span class="euro ct2 wshadow fs-M">&euro;</span>
        </div>
        
        <?php if ($user->bookas) : ?>
        <div class="invests-num ct1"><?php echo $user->bookas ?> <?php echo Text::get('user-invested-bookas'); ?></div>
        <?php endif; ?>

    </div>
    
</div>  
