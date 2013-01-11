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
    Base\Model,
    Base\Library\Text;

if (empty($this['investors'])) return '';
?>
<div id="user-sharemates" class="side-widget">
    
    <h3 class="htitle"><?php echo Text::get('collection-side-topten'); ?></h3>
       
    <ul class="user-side-list">
    <?php foreach ($this['investors'] as $item) : ?>
    <li>
        <div class="user-side-list-item">
            <?php echo new View('view/user/widget/investor.html.php', array('user' => $item)); ?>
        </div> 
    </li>
    <?php endforeach ?>
    </ul>
    
</div>