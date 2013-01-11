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
<div class="widget board">
    <?php if (!empty($this['bookas'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Booka</th>
                <th>Conseguido</th>
                <th>De</th>
                <th></th>
                <th>Ahora esta en</th>
                <th>Faltan</th>
                <th>Para ...</th>
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['bookas'] as $booka) : ?>
            <tr>
                <td><a href="/booka/<?php echo $booka->id; ?>" target="_blank" title="Preview"><?php echo $booka->name; ?></a></td>
                <td><?php echo $booka->invested; ?>&euro;</td>
                <td><?php echo $booka->cost; ?>&euro;</td>
                <td><?php echo $booka->percent; ?>&#37;</td>
                <td><?php echo $booka->stageData->currentName; ?></td>
                <td><?php echo (int) $booka->stageData->rest; ?>&euro;</td>
                <td><?php echo $booka->stageData->nextName; ?></td>
                <td><a href="/admin/invests/?bookas=<?php echo $booka->id; ?>" title="Ver aportes" target="_blank">[Aportes]</a></td>
                <td><a href="/admin/accounts/report/<?php echo $booka->id; ?>" title="Ver informe detallado de financiación" target="_blank">[Informe]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No hay ningún booka en campaña actualmente</p>
    <?php endif; ?>
</div>