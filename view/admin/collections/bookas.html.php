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
<div class="widget board">
<?php if (!empty($this['list'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- preview -->
                <th>Booka</th>
                <th>Estado</th>
                <th>Despegue</th>
                <th>Coste</th> <!-- segun estado -->
                <th>Conseguido</th> <!-- segun estado -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $booka) : ?>
            <tr>
                <td><a href="/booka/<?php echo $booka->id; ?>" target="_blank" title="Preview">[Previsualizar]</a></td>
                <td><?php echo $booka->name; ?></td>
                <td><?php echo $this['status'][$booka->status]; ?></td>
                <td><?php if ($booka->status > 1 && !empty($booka->published)) echo $booka->published; ?></td>
                <td><?php echo $booka->cost; ?> &euro;</td>
                <td><?php echo $booka->invested; ?> &euro;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
