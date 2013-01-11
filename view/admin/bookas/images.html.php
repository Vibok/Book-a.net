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

$booka = $this['booka'];
$images = $this['images'];
$images2 = $this['images2'];
?>
<script type="text/javascript">
function move (img, direction) {
    document.getElementById('the_action').value = direction;
    document.getElementById('move_pos').value = img;
    document.getElementById('images_form').submit();
}
</script>

<a href="/admin/bookas" class="button std-btn tight menu-btn">Volver a la gesti&oacute;n de bookas</a>
&nbsp;&nbsp;&nbsp;
<a href="/booka/<?php echo $booka->id; ?>" class="button std-btn tight menu-btn" target="_blank">Ver publicado</a>
&nbsp;&nbsp;&nbsp;
<a href="/booka/edit/<?php echo $booka->id; ?>" class="button std-btn tight menu-btn" target="_blank">Editar este booka</a>
<div class="widget board">
    <h3>Im&aacute;genes de banda superior</h3>
    <?php if (!empty($images)) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th colspan="2"></th> <!-- posicion -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($images as $image) : ?>
            <tr>
                <td style="width:105px;text-align: left;"><img src="<?php echo $image->getLink('145', '99', true); ?>" alt="image" /></td>
                <td>&nbsp;</td>
                <td><a href="#" onclick="move('<?php echo $image->id; ?>', 'up'); return false;">[&uarr;]</a></td>
                <td><a href="#" onclick="move('<?php echo $image->id; ?>', 'down'); return false;">[&darr;]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado im&aacute;genes de banda superior</p>
    <?php endif; ?>
</div>
<div class="widget board">
    <a name="content"></a>
    <h3>Im&aacute;genes de contenido</h3>
    <?php if (!empty($images2)) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th colspan="2"></th> <!-- posicion -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($images2 as $image2) : ?>
            <tr>
                <td style="width:105px;text-align: left;"><img src="<?php echo $image2->getLink('190', '130', true); ?>" alt="image" /></td>
                <td>&nbsp;</td>
                <td><a href="#" onclick="move('<?php echo $image2->id; ?>', 'up2'); return false;">[&uarr;]</a></td>
                <td><a href="#" onclick="move('<?php echo $image2->id; ?>', 'down2'); return false;">[&darr;]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado im&aacute;genes de contenido</p>
    <?php endif; ?>
</div>
<form id="images_form" action="/admin/bookas/images/<?php echo $booka->id; ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $booka->id; ?>" />
    <input type="hidden" id="the_action" name="action" value="apply" />
    <input type="hidden" id="move_pos" name="move" value="" />
</form>