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
    Base\Model\Invest;

$filters = $this['filters'];

?>
<!-- filtros -->
<?php $the_filters = array(
    'bookas' => array (
        'label' => 'Booka',
        'first' => 'Todos los Bookas'),
    'methods' => array (
        'label' => 'Método de pago',
        'first' => 'Todos los métodos'),
    'investStatus' => array (
        'label' => 'Estado de aporte',
        'first' => 'Todos los estados'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos')
); ?>
<a href="/admin/invests/add" class="button std-btn tight menu-btn">Generar aporte manual</a>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/invests" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>" onchange="document.getElementById('filter-form').submit();">
                <option value="<?php if ($filter == 'investStatus' || $filter == 'status') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo substr($itemName, 0, 50); ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/invests/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro.</p>
<?php elseif (!empty($this['list'])) : ?>
<?php $Total = 0; foreach ($this['list'] as $invest) { $Total += $invest->amount; } ?>
    <p><strong>TOTAL:</strong>  <?php echo number_format($Total, 0, '', '.') ?> &euro;</p>
    
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Aporte ID</th>
                <th>Fecha</th>
                <th>usuario</th>
                <th>Booka</th>
                <th>Metodo</th>
                <th>Estado</th>
                <th>Importe</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/invests/details/<?php echo $invest->id ?>" title="<?php
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual ';
                   ?>">[Detalles]</a></td>
                <td><a href="/admin/invests/edit/<?php echo $invest->id ?>" title="Modificar cantidad, recompensas y datos">[Modificar]</a></td>
                <td><?php echo $invest->id ?></td>
                    <td><?php echo $invest->invested ?></td>
                <td><a href="/admin/users?id=<?php echo $invest->user->id ?>" target="_blank"><?php echo $invest->user->name; ?></a></td>
                <td><a href="/admin/bookas/?name=<?php echo substr($invest->booka->name, 0, 10) ?>" target="_blank"><?php echo Text::recorta($invest->booka->name, 20); ?></a></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
                <td><?php echo $invest->amount ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay aportes que cumplan con los filtros.</p>
<?php endif;?>
</div>