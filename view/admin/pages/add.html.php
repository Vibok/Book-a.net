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
<div class="widget board">
    <form method="post" action="/admin/pages/add">

        <p>
            <label for="page-id">Identificador:</label><br />
            /about/<input type="text" name="id" id="page-id" value="" />
        </p>

        <p>
            <label for="page-name">Nombre:</label><br />
            <input type="text" name="name" id="page-name" value="" />
        </p>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>