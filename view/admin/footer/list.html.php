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
    Base\Core\ACL;

$filters = $this['filters'];
?>
<a href="/admin/footer/add" class="button std-btn tight menu-btn">Añadir elemento</a>

<div class="widget board">
    <form id="columnfilter-form" action="/admin/footer" method="get">
        <label for="column-filter">Mostrar las elementos de:</label>
        <select id="column-filter" name="column" onchange="document.getElementById('columnfilter-form').submit();">
            <option value="">Todas</option>
        <?php foreach ($this['columns'] as $columnId=>$columnName) : ?>
            <option value="<?php echo $columnId; ?>"<?php if ($filters['column'] == $columnId) echo ' selected="selected"';?>><?php echo $columnName; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['footers'])) : ?>
    <table>
        <thead>
            <tr>
                <td><!-- Edit --></td>
                <th>Título</th> <!-- title -->
                <th>Columna</th> <!-- column -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['footers'] as $item) : ?>
            <tr>
                <td><?php echo Text::adminBtn($item->id, 'edit'); ?></td>
                <td><?php echo $item->title; ?></td>
                <td><?php echo $this['columns'][$item->column]; ?></td>
                <td><?php echo $item->order; ?></td>
                <td><?php echo Text::adminBtn($item->id, 'up'); ?></td>
                <td><?php echo Text::adminBtn($item->id, 'down'); ?></td>
                <td><?php echo Text::adminBtn($item->id, 'remove', '', 'Seguro que deseas eliminar este registro?'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
