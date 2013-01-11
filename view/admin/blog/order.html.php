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

$type = $this['type'];

?>
<a href="/admin/blog" class="button std-btn tight menu-btn">Volver a la lista</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/add" class="button std-btn tight menu-btn">Nueva entrada</a>
<?php if ($type != 'home') : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/home" class="button std-btn tight menu-btn">Ordenar Home</a>
<?php endif; ?>
<?php if ($type != 'footer') : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/footer" class="button std-btn tight menu-btn">Ordenar Footer</a>
<?php endif; ?>
<?php /* if ($type != 'top') : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/top" class="button std-btn tight menu-btn">Ordenar Top</a>
<?php endif; */ ?>


<div class="widget board">
    <?php if (!empty($this['posts'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Título</th> <!-- title -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['posts'] as $post) : ?>
            <tr>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->order; ?></td>
                <td><a href="/admin/blog/up_<?php echo $type ?>/<?php echo $post->id ?>">[&uarr;]</a></td>
                <td><a href="/admin/blog/down_<?php echo $type ?>/<?php echo $post->id ?>">[&darr;]</a></td>
                <td><a href="/admin/blog/remove_<?php echo $type ?>/<?php echo $post->id ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>