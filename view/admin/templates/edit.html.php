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
<p><strong><?php echo $this['template']->name; ?></strong>: <?php echo $this['template']->purpose; ?></p>

<div class="widget board">
    <form method="post" action="/admin/templates/edit/<?php echo $this['template']->id; ?>">
        <fieldset>
            <legend>Espa&ntilde;ol</legend>
            <p>
                <label for="tpltitle_es">Asunto:</label><br />
                <input id="tpltitle_es" type="text" name="title_es" size="120" value="<?php echo $this['template']->title_es; ?>" />
            </p>

            <p>
                <label for="tpltext_es">Contenido:</label><br />
                <textarea id="tpltext_es" name="text_es" cols="100" rows="20"><?php echo $this['template']->text_es; ?></textarea>
            </p>
        </fieldset>

        <fieldset>
            <legend>English</legend>
            <p>
                <label for="tpltitle_en">Asunto:</label><br />
                <input id="tpltitle_en" type="text" name="title_en" size="120" value="<?php echo $this['template']->title_en; ?>" />
            </p>

            <p>
                <label for="tpltext_en">Contenido:</label><br />
                <textarea id="tpltext_en" name="text_en" cols="100" rows="20"><?php echo $this['template']->text_en; ?></textarea>
            </p>
        </fieldset>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>