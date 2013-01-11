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
<a href="/admin/promote/add" class="button std-btn tight menu-btn">Nuevo destacado</a>

<div class="widget board">
    <?php if (!empty($this['promoted'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- preview -->
                <th>Booka</th> <!-- title -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- On/Off --></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['promoted'] as $promo) : ?>
            <tr>
                <td><?php echo Text::adminBtn(null, 'preview', '/booka/'.$promo->booka->id); ?></td>
                <td><?php echo ($promo->active) ? '<strong style="font-style: italic;">'.$promo->booka->name.'</strong>' : $promo->booka->name; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><?php echo Text::adminBtn($promo->id, 'up'); ?></td>
                <td><?php echo Text::adminBtn($promo->id, 'down'); ?></td>
                <td><?php echo Text::adminBtn($promo->id, 'edit'); ?></td>
                <td><?php echo ($promo->active) 
                            ? Text::adminBtn($promo->id, 'active', 'off', '', '', 'Ocultar')
                            : Text::adminBtn($promo->id, 'active', 'on', '', '', 'Mostrar');
                ?></td>
                <td><a href="/admin/promote/remove/<?php echo $promo->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>