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
<?php if (!empty($booka->milestone1)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-milestone1'); ?></h4>
        <?php echo $booka->milestone1; ?>
    </div>    
<?php endif; ?>

<?php if (!empty($booka->milestone2)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-milestone2'); ?></h4>
        <?php echo $booka->milestone2; ?>
    </div>    
<?php endif; ?>

<?php if (!empty($booka->milestone3)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-milestone3'); ?></h4>
        <?php echo $booka->milestone3; ?>
    </div>    
<?php endif; ?>

<?php if (!empty($booka->milestone4)) : ?>
    <div class="booka-content">
        <h4 class="ct2 fs-L upcase wshadow"><?php echo Text::get('booka-text_title-milestone4'); ?></h4>
        <?php echo $booka->milestone4; ?>
    </div>    
<?php endif; ?>

