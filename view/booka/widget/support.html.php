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
    Base\Library\Text;

$level = (int) $this['level'] ?: 3;

$booka = $this['booka'];

?>
<div id="booka-support" class="side-widget">

    <h3 class="htitle"><?php echo Text::get('booka-meter-header'); ?></h3>
    
    <?php echo new View('view/booka/widget/meter.html.php', array('booka' => $booka) ) ?>
    <?php echo new View('view/booka/widget/stages.html.php', array('booka' => $booka) ) ?>
    
    <div class="booka-stats upcase ft3 fs-XS ct1"><?php echo Text::get('booka-view-metter-got'); ?>: <span class="ct3"><?php echo \amount_format($booka->invested); ?>&euro;</span></div>
    <div class="booka-stats upcase ft3 fs-XS ct1"><?php echo Text::get('booka-menu-investors'); ?>: <span class="ct3"><?php echo count($booka->investors); ?></span></div>
    <div class="hr"><hr /></div>
    
    <div class="button">
        <?php if ($booka->status == 3) : // boton apoyar solo si esta en campaña ?>
        <a class="button std-btn wide" href="/booka/<?php echo $booka->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
        <?php elseif ($booka->status > 3) : ?>
        <a class="button std-btn wide" href="/buy/<?php echo $booka->id ?>"><?php echo Text::get('regular-buy'); ?></a>
        <?php endif; ?>
    </div>
    
</div>