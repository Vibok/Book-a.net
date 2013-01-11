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

use Base\Model\Image,
    Base\Library\Text;

$user = $this['user'];

shuffle($user->interests);

if (!$user->avatar instanceof Image) {
    $user->avatar = Image::get(1);
}

$url_profile = '/user/profile/'.$user->user;
?>
<div class="user-widget-investor">
    <div class="avatar">
        <a href="<?php echo $url_profile; ?>"><img src="<?php echo $user->avatar->getLink(60, 60, true); ?>" /></a>
    </div>
    <div class="content sharemate">
        <span class="user bloque">
            <a class="ft2 ct1" href="<?php echo $url_profile; ?>"><?php echo $user->name; ?></a>
        </span>
        <div class="interests"><?php echo implode(', ', $user->interests); ?></div>
    </div>
</div>  
