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
    Base\Library\Navi,
    Base\Library\Text;

$booka = $this['booka'];

// paginación
$pg = Navi::calcPages(count($booka->investors), $_GET['page'], 60);
// este rollo para que los últimos dos items (o uno si es impar) no lleven borde
$cI = ($pg['to'] < 60) ? $pg['to'] : 60;
//echo \trace($pg);
?>
<div class="center-widget">

    <div style="clear:both; margin-bottom: 34px;">
        <div class="booka-stats upcase ct1 fs-M"><span class="ct3 fs-M"><?php echo count($booka->investors); ?></span> <?php echo Text::get('booka-menu-investors'); ?></div>
        <div class="booka-stats upcase ct1 fs-M"><span class="ct3 fs-M"><?php echo \amount_format($booka->invested); ?>&euro;</span> <?php echo Text::get('booka-view-metter-got'); ?></div>
    </div>

<?php if (!empty($booka->investors)) : ?>
    <?php echo Navi::pageHtml(array('span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], strtolower(Text::get('booka-menu-investors'))), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
    
    <div id="booka-investors" >
        <ul>
        <?php $i = 0;
        foreach ($booka->investors as $investor) : 
            $i++;
            if ($i < $pg['from'] || $i > $pg['to']) continue;
                if ($cI %2 == 0) { 
                    // par, los dos últimos
                    $btmdsh = ($i == $pg['to'] || $i == $pg['to']-1) ? '' : 'bottom dashed';
                } else {
                    // impar, solo el último
                    $btmdsh = ($i == $pg['to']) ? '' : 'bottom dashed';
                }
            ?>
            <li class="<?php echo $btmdsh; ?>"><?php echo new View('view/user/widget/investor.html.php', array('user' => $investor)) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>        
    
    <?php echo Navi::pageHtml(array('footer' => true, 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
<?php endif; ?>

</div>