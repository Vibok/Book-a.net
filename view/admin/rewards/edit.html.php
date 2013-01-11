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

use Base\Library\Lang;

$item = $this['item'];
?>
<div class="widget board">
    <form action="/admin/rewards" method="post" >
        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $item->id; ?>" />
        <input type="hidden" name="order" value="<?php echo $item->order; ?>" />

        <ul id="lang-tabs">
            <?php foreach (Lang::$langs as $langId=>$langName) : ?>
                <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
            <?php endforeach; ?>
        </ul>
        <?php foreach (Lang::$langs as $langId=>$langName) : 
            $campo_nombre = 'name_'.$langId;
            $campo_descripcion = 'description_'.$langId;
            ?>
            <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
            <p>
                <label for="name_<?php echo $langId ?>">Nombre:</label><br />
                <input type="text" name="name_<?php echo $langId ?>" id="name_<?php echo $langId ?>" value="<?php echo $item->$campo_nombre; ?>" style="width:420px;" />
            </p>
            <p>
                <label for="description_<?php echo $langId ?>">Decripción:</label><br />
                <input type="text" name="description_<?php echo $langId ?>" id="description_<?php echo $langId ?>" value="<?php echo $item->$campo_descripcion; ?>" style="width:420px;" />
            </p>
            </div>
        <?php endforeach; ?>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>