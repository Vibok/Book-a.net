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

use Base\Library\Text,
    Base\Model,
    Base\Core\Redirection;

$invest = $this['invest'];
$status = $this['status'];

?>
<a href="/admin/accounts/details/<?php echo $invest->id ?>" class="button std-btn tight menu-btn">Volver al detalle</a>
<div class="widget" >
    <form method="post" action="/admin/accounts/update/<?php echo $invest->id ?>" >

    <p>
        <label for="status-filter">Pasarlo al estado:</label><br />
        <select id="status-filter" name="status" >
        <?php foreach ($status as $statusId=>$statusName) : ?>
            <option value="<?php echo $statusId; ?>"<?php if ($invest->status == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

    <?php if ($invest->issue) : ?>
    <p>
        <label><input type="checkbox" name="resolve" value="1" /> Dar la incidencia por resuelta</label>
    </p>
    <?php endif; ?>


        <input type="submit" name="update" value="Aplicar" onclick="return confirm('Segurisimo que le campibamos el estado al aporte???')"/>
    </form>
</div>
