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

$level = (int) $this['level'] ?: 3;

$booka = $this['booka'];

if (empty($booka->rewards))
    return '';

uasort($booka->rewards,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );
?>
<div id="booka-rewards" class="side-widget">
    
    <h3 class="htitle"><?php echo Text::get('booka-rewards-supertitle'); ?></h3>
       
    <ul>
    <?php foreach ($booka->rewards as $reward) : ?>
    <li class="underlined">

        <span class="upcase ct1"><?php echo htmlspecialchars($reward->name) ?></span>
        
        <p class="ft2"><?php echo htmlspecialchars($reward->description)?></p>

        <?php if (!empty($reward->units)) : 
            $units = ($reward->units - $reward->taken); ?>
            <span class="upcase ft3 fs-XS ct5 bloque"><?php echo Text::html('booka-rewards-units_left', $units); ?></span>
            <br />
        <?php endif; ?>

        
        <div class="booka-stats ft3 fs-L ct2 wshadow">
            <?php echo \amount_format($reward->amount); ?><span class="euro fs-M ct2 wshadow">&euro;</span>
        </div>
        
        <?php if (!$reward->none) : ?>
        <div class="booka-stats">
            <?php if ($booka->status == 3) : // boton apoyar solo si esta en campaña ?>
            <a href="/booka/<?php echo $booka->id ?>/invest?amount=<?php echo $reward->amount ?>" class="button std-btn" ><?php echo Text::get('regular-getit'); ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
            
        <br clear="both" />
        
    </li>
    <?php endforeach ?>
    </ul>
    
</div>