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
$stage = $booka->stageData;

// el procentaje lo calculamos sobre el total de la fse
$percentWidth = $booka->percent > 100 ? '100' : $booka->percent;
$percentWidth = $percentWidth < 10 ? 10 : $percentWidth;
$mercuryWidth = $booka->percent > 100 ? '100' : $booka->percent;

?>        
<div id="booka-meter">
    <div class="percent ft3" style="width:<?php echo $percentWidth; echo '&#37;';?>">
        <?php echo $booka->percent; ?><span class="ft3">&#37;</span>
    </div>
    <div class="graph">
        <div class="mercury" style="width:<?php echo $mercuryWidth; echo '&#37;';?>"></div>
    </div>
    <?php if ($this['type'] == 'small') : ?>
        <div class="rest ft3 fs-XS ct2 wshadow"><?php echo Text::get('booka-view-metter-got'); ?> <?php echo $booka->invested; ?>&euro; <?php echo Text::get('regular-of'); ?> <?php echo $booka->cost; ?>&euro;</div>
    <?php else : ?>
        <div class="rest ft3 fs-XS ct2 wshadow"><?php echo Text::get('booka-milestones-rest_next', $stage->rest . '&euro;', $stage->nextName); ?></div>
    <?php endif; ?>
    
</div>
