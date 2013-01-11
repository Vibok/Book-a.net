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
    Base\Model\Image;

// si es director, solo puede editar la suya
$solo = (isset($_SESSION['user']->roles['director'])) ? $_SESSION['user']->collection : null;
?>
<?php if (!isset($_SESSION['user']->roles['director'])) : ?>
<a href="/admin/collections/add" class="button std-btn tight menu-btn">Añadir colección</a>
<?php endif; ?>

<div class="widget board">
    <?php if (!empty($this['collections'])) : ?>
    <table>
        <thead>
            <tr>
                <td><!-- Ver --></td>
                <td><!-- Edit --></td>
                <th>Nombre</th>
                <th>Director</th>
                <th>Color</th>
                <th>Imagen</th>
                <th>Bookas</th> <!-- usos -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['collections'] as $item) : ?>
            <tr>
                <td><a class="tipsy" href="/admin/collections/bookas/<?php echo $item->id; ?>" title="lista de títulos">[Títulos]</a></td>
                <td><?php if (!isset($solo) || (isset($solo) && $item->id == $solo)) echo Text::adminBtn($item->id, 'edit'); ?></td>
                <td><a class="tipsy" href="/collection/<?php echo $item->id; ?>" title="Ver la página pública"><?php echo $item->name_es; ?></td>
                <td><?php echo $item->director; 
                if (!isset($solo) && is_object($item->user)) 
                    echo '&nbsp;<a href="/admin/users/manage/'.$item->user->id.'">[Gestionar]</a>'; 
                ?></td>
                <td style="background-color:white;color:<?php echo '#'.$item->color ?>;">[#######]</td>
                <td><?php if (!empty($item->image) && $item->image instanceof Image) : ?><img src="<?php echo $item->image->getLink(96, 64); ?>" alt="IMAGEN" title="<?php echo $item->name_es ?>"><?php endif; ?></td>
                <td><?php echo $item->used; ?></td>
                <td><?php echo $item->order; ?></td>
                <td><?php if (!isset($solo)) echo Text::adminBtn($item->id, 'up'); ?></td>
                <td><?php if (!isset($solo)) echo Text::adminBtn($item->id, 'down'); ?></td>
                <td><?php if (!isset($solo)) 
                            echo ($item->used > 0) 
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