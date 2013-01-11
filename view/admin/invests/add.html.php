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

?>
<div class="widget">
    <form id="filter-form" action="/admin/invests/add" method="post">
        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="" />
        </p>
        <p>
            <label for="invest-user">Usuario:</label><br />
            <select id="invest-user" name="user">
                <option value="">Seleccionar usuario que hace el aporte</option>
            <?php foreach ($this['users'] as $userId=>$userName) : ?>
                <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="invest-booka">Booka:</label><br />
            <select id="invest-booka" name="booka">
                <option value="">Seleccionar el booka al que se aporta</option>
            <?php foreach ($this['bookas'] as $bookId=>$bookName) : ?>
                <option value="<?php echo $bookId; ?>"><?php echo $bookName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="invest-anonymous">Aporte anónimo:</label><br />
            <input id="invest-anonymous" type="checkbox" name="anonymous" value="1">
        </p>

        <input type="submit" name="add" value="Generar aporte" />

    </form>
</div>