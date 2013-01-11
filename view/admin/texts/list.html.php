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
<div class="widget">
    <form id="filter-form" action="/admin/texts" method="get">

        <table>
            <tr>
                <td>
                    <label for="group-filter">Agrupacion:</label><br />
                    <select id="group-filter" name="group" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                        <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="filter-text">El texto:</label><br />
                    <input name="text" value="<?php echo (string) $filters['text']; ?>" />
                </td>
            </tr>
        </table>


        <button type="submit" name="filter" class="std-btn tight menu-btn">Buscar</button>
    </form>
</div>

<!-- lista -->
<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro.</p>
<?php elseif (!empty($this['list'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Espa&ntilde;ol</th>
                <th>Agrupación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['list'] as $item) : ?>
            <tr>
                <td><a href="/admin/texts/edit/<?php echo $item->id; ?>">[Editar]</a></td>
                <td><?php echo $item->text_es; ?></td>
                <td><?php echo $item->group; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>