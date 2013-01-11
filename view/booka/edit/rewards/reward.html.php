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

$reward = $this['data']['reward'];
$types = $this['data']['types'];

if ($reward->type == 9999) {
    $reward->name = $reward->other_text;
} else {
    $reward->name = $types[$reward->type]->name;
}
?>

<div class="reward">
    
    <div class="title upcase ct1 fs-L"><?php echo htmlspecialchars($reward->name); ?><span class="ct1"><?php echo \amount_format($reward->amount) ?> &euro;</span></div>
    
    <div class="description">
        <?php echo htmlspecialchars($reward->description) ?>
    </div>
    
    <?php if (!empty($reward->units)) : ?>
        <div class="units ct1 fs-S"><?php echo Text::get('booka-rewards-reward-units', $reward->units); ?></div>
    <?php endif; ?>

    <div class="reward-buttons">
        <input type="submit" class="std-btn edit" name="reward-<?php echo $reward->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
        <input type="submit" class="std-btn remove" name="reward-<?php echo $reward->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
    </div>
    
</div>

    

    