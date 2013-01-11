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

$cost = $this['data']['cost'];
$types = $this['data']['types'];
?>

<div class="cost stage<?php echo $cost->stage; ?>">
    
    <div class="title upcase ct1 fs-L"><?php echo htmlspecialchars($cost->cost_es); ?><span class="ct1"><?php echo \amount_format($cost->amount) ?> &euro;</span></div>
    
    <div class="description">
        <?php echo htmlspecialchars($cost->description) ?>
    </div>
    
    <div class="cost-buttons">
        <input type="submit" class="std-btn edit" name="cost-<?php echo $cost->id ?>-edit" value="<?php echo Text::get('regular-edit') ?>" />
        <input type="submit" class="std-btn remove" name="cost-<?php echo $cost->id ?>-remove" value="<?php echo Text::get('form-remove-button') ?>" />
    </div>
    
</div>

    

    