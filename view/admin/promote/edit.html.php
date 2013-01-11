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

$promo = $this['promo'];
$bookas = $this['bookas'];
$status = $this['status'];

?>
<form method="post" action="/admin/promote">
    <input type="hidden" name="action" value="<?php echo $this['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $promo->order ?>" />
    <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />

<p>
    <label for="promo-booka">Booka:</label><br />
    <select id="promo-booka" name="booka">
        <option value="" >Seleccionar el Booka a destacar</option>
    <?php foreach ($bookas as $booka) : ?>
        <option value="<?php echo $booka->id; ?>"<?php if ($promo->booka == $booka->id) echo' selected="selected"';?>><?php echo $booka->name . ' ('. $status[$booka->status] . ')'; ?></option>
    <?php endforeach; ?>
    </select>
</p>

<p>
    <label>Publicado:</label><br />
    <label><input type="radio" name="active" id="promo-active" value="1"<?php if ($promo->active) echo ' checked="checked"'; ?>/> SÍ</label>
    &nbsp;&nbsp;&nbsp;
    <label><input type="radio" name="active" id="promo-inactive" value="0"<?php if (!$promo->active) echo ' checked="checked"'; ?>/> NO</label>
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
