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
    Base\Library\Text,
    Base\Model\Image,
    Base\Model\Booka\Category;

$booka = $this['booka'];
$categories = Category::getNames($booka->id, 3);

$fecha = date('d | m | Y', strtotime($booka->published));
$fecha_prod = date('d | m | Y', strtotime($booka->success));

$blank = ($this['global'] === true) ? ' target="_blank"' : '';

$color = '#'.$booka->collData->color;
$htitleStyle = "background-color:{$color};";
?>

<div class="widget-booka activable">
    <a href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>" class="expand"<?php echo $blank; ?>></a>

    <div class="widget-booka-header" style="<?php echo $htitleStyle; ?>">
        <h4 class="htitle ft2 fs-S" style="<?php echo $htitleStyle; ?>"><?php echo $booka->collData->name; ?></h4>
    </div>
    <div class="widget-booka-content">
        <h5><a href="<?php echo SITE_URL ?>/booka/<?php echo $booka->id ?>"><?php echo $booka->name ?></a></h5>
        <h6 class="upcase ct2 wshadow"><?php echo $booka->author ?></h6>

        <div class="image">
            <?php if (!empty($booka->gallery) && (current($booka->gallery) instanceof Image)): ?>
            <img alt="<?php echo $booka->name ?>" src="<?php echo current($booka->gallery)->getLink(250, 171, true) ?>" />
            <?php endif ?>
            <div class="categories ct0 fs-XS ft3"><?php echo implode(', ', $categories); ?></div>
        </div>

        <div class="subtitle ct2 ft2 fs-M wshadow"><?php echo $booka->info; ?></div>
        <div class="publisher upcase ft3 fs-XS ct1"><?php echo $fecha; ?></div>
        <?php /* if ($booka->status == 3) : ?>
        <div class="publisher upcase ft3 fs-XS ct1"><?php echo Text::get('booka-launch_date', $fecha); ?></div>
        <?php else : ?>
        <div class="publisher upcase ft3 fs-XS ct1"><?php echo Text::get('booka-produced_date', $fecha_prod); ?></div>
        <?php endif; */ ?>

        <div class="description ft3 fs-XS"><?php echo Text::recorta($booka->about, 300); ?></div>

        <div class="obtained ft3 fs-XS ct2 wshadow"><?php echo Text::get('booka-view-metter-got'); ?> <?php echo $booka->invested; ?>&euro; <?php echo Text::get('regular-of'); ?> <?php echo $booka->cost; ?>&euro;</div>

        <?php echo new View('view/booka/widget/meter.html.php', array('booka' => $booka) ) ?>
        <?php echo new View('view/booka/widget/stages.html.php', array('booka' => $booka) ) ?>
    </div>
</div>
