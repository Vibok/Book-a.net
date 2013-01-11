<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci�n Fuentes Abiertas (see README for details)
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
    Base\Model,
    Base\Core\Redirection,
    Base\Library\Advice;

?>
<script type="text/javascript">
    function validate () {
        if (document.getElementById('booka-id') == '') {
            alert('tienes que poner el ID!');
            return false;
        }

        return true;
    }
</script>
<div class="widget">
    <p>Poner la id del booka para crearlo</p>

    <form method="post" action="/booka/create" onsubmit="return validate();">
        <input type="hidden" name="action" value="continue" />
        <input type="hidden" name="confirm" value="true" />

        /booka/<input type="text" id="booka-id" name="id" value="" />

        <input type="submit" name="create" value="Crear" />

    </form>
</div>
