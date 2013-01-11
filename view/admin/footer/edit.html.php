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
    Base\Library\Lang;

$item = $this['footer'];
?>
<script type="text/javascript">

jQuery(document).ready(function ($) {

    $('#footer-column').change(function () {
        order = $.ajax({async: false, url: '<?php echo SITE_URL; ?>/ws/get_footer_order/'+$('#footer-column').val()}).responseText;
        $('#footer-order').val(order);
        $('#footer-num').html(order);
    });

});
</script>
<div class="widget board">
    <form method="post" action="/admin/footer">

        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $item->id; ?>" />

        <p>
            <label for="footer-column">Columna:</label><br />
            <select id="footer-column" name="column">
                <option value="" disabled>Elige la columna</option>
                <?php foreach ($this['columns'] as $id=>$name) : ?>
                <option value="<?php echo $id; ?>"<?php if ($id == $item->column) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <ul id="lang-tabs">
            <?php foreach (Lang::$langs as $langId=>$langName) : ?>
                <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
            <?php endforeach; ?>
        </ul>
        <?php foreach (Lang::$langs as $langId=>$langName) : 
            $campo_titulo = 'title_'.$langId;
            ?>
            <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
            <p>
                <label for="footer-title_<?php echo $langId ?>">Título:</label><br />
                <input type="text" name="title_<?php echo $langId ?>" id="footer-title_<?php echo $langId ?>" value="<?php echo $item->$campo_titulo ?>" style="width:420px;" />
            </p>
            </div>
        <?php endforeach; ?>

        <p>
            <label for="footer-url">Url:</label><br />
            <input id="footer-url" type="text" name="url" value="<?php echo $item->url; ?>" style="width:420px;" />
        </p>


        <p>
            <label for="footer-order">Posición:</label><br />
            <select name="move">
                <option value="same" selected="selected" disabled>Tal cual</option>
                <option value="up">Antes de </option>
                <option value="down">Después de </option>
            </select>&nbsp;
            <input type="text" name="order" id="footer-order" value="<?php echo $item->order; ?>" size="4" />
            &nbsp;de&nbsp;<span id="footer-num"><?php echo $item->cuantos; ?></span>
        </p>


        <input type="submit" name="save" value="Guardar" />
    </form>
</div>