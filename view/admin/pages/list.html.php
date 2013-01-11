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

?>
<a href="/admin/pages/add" class="button std-btn tight menu-btn">Nueva P&aacute;gina</a>

<div class="widget board">
    <?php if (!empty($this['pages'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <th><!-- Abrir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['pages'] as $page) : ?>
            <tr>
                <td><?php echo Text::adminBtn($page->id, 'edit'); ?></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->order; ?></td>
                <td><?php echo Text::adminBtn($page->id, 'up'); ?></td>
                <td><?php echo Text::adminBtn($page->id, 'down'); ?></td>
                <td><?php echo Text::adminBtn('', 'default', $page->url, '', '', 'Ver', true); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>