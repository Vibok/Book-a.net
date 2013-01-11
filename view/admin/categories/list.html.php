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
<a href="/admin/categories/add" class="button std-btn tight menu-btn">Añadir categoria</a>

<div class="widget board">
    <?php if (!empty($this['categories'])) : ?>
    <table>
        <thead>
            <tr>
                <td><!-- Edit --></td>
                <th>Nombre</th>
                <th>Bookas</th> <!-- usos -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['categories'] as $item) : ?>
            <tr>
                <td><?php echo Text::adminBtn($item->id, 'edit'); ?></td>
                <td><?php echo $item->name_es; ?></td>
                <td><?php echo $item->used; ?></td>
                <td><?php echo $item->order; ?></td>
                <td><?php echo Text::adminBtn($item->id, 'up'); ?></td>
                <td><?php echo Text::adminBtn($item->id, 'down'); ?></td>
                <td><?php echo ($item->used > 0) 
                            ? Text::adminBtn($item->id, 'remove', '', '', 'Tiene bookas! No se puede eliminar...')
                            : Text::adminBtn($item->id, 'remove', '', 'Seguro que deseas eliminar este registro?'); 
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>