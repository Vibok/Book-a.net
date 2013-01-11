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
    Base\Core\View,
    Base\Model\Invest;

$filters = $this['filters'];

$emails = Invest::emails(true);
?>
<!-- filtros -->
<?php $the_filters = array(
    'bookas' => array (
        'label' => 'Booka',
        'first' => 'Todos los bookas'),
    'methods' => array (
        'label' => 'Método de pago',
        'first' => 'Todos los métodos'),
    'investStatus' => array (
        'label' => 'Estado del aporte',
        'first' => 'Todos los estados'),
    'investStatus' => array (
        'label' => 'Estado de aporte',
        'first' => 'Todos los estados'),
    'collections' => array (
        'label' => 'De la colección',
        'first' => 'Cualquiera'),
    /*
    'review' => array (
        'label' => 'Para revisión',
        'first' => 'Todos'),
     * 
     */
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos'),
    'issue' => array (
        'label' => 'Mostrar',
        'first' => 'Todos los aportes')
); ?>
<a href="/admin/invests/add" class="button std-btn tight menu-btn">Generar aporte manual</a>
<?php /*    <a href="/cron/execute" target="_blank"  class="button std-btn tight menu-btn" onclick="return confirm('Se va lanzar el proceso automático ahora mismo, ok?');">Ejecutar cargos</a>&nbsp;&nbsp;&nbsp; */ ?>
<a href="/admin/accounts/viewer" class="button std-btn tight menu-btn">Visor de logs</a>&nbsp;&nbsp;&nbsp;
<?php if (!empty($filters['bookas'])) : ?>
    <a href="/admin/accounts/report/<?php echo $filters['bookas'] ?>#detail" class="button std-btn tight menu-btn" target="_blank">Informe financiero completo de <strong><?php echo $this['bookas'][$filters['bookas']] ?></strong></a>&nbsp;&nbsp;&nbsp;
<?php /*    <a href="/cron/dopay/<?php echo $filters['bookas'] ?>" target="_blank" class="button std-btn tight menu-btn" onclick="return confirm('No hay vuelta atrás, ok?');">Realizar pagos secundarios a <strong><?php echo $this['bookas'][$filters['bookas']] ?></strong></a> */ ?>
<?php endif ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/accounts" method="get">
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>">
                <option value="<?php if ($filter == 'investStatus' || $filter == 'status' || $filter == 'issue') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
        <br clear="both" />
        
        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
            <?php echo new View('library/superform/view/element/datebox.html.php', array('value'=>$filters['date_from'], 'id'=>'date-filter-from', 'name'=>'date_from')); ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
            <?php echo new View('library/superform/view/element/datebox.html.php', array('value'=>$filters['date_until'], 'id'=>'date-filter-until', 'name'=>'date_until')); ?>
        </div>

        <div style="float:left;margin:5px;">
            <label for="id-filter">Id:</label><br />
            <input type="text" id ="id-filter" name="id" value ="<?php echo $filters['id']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/accounts/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['list'])) : ?>
<?php $Total = 0; foreach ($this['list'] as $invest) { $Total += $invest->amount; } ?>
    <p><strong>TOTAL:</strong>  <?php echo number_format($Total, 0, '', '.') ?> &euro;</p>
    
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th>Booka</th>
                <th>Metodo</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/accounts/details/<?php echo $invest->id ?>" title="<?php
                    if ($invest->issue)  echo 'Incidencia! ';
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual';
                    ?>" <?php if ($invest->issue) echo ' style="color:red !important;"'; ?>>[Detalles]</a></td>
                <td><a href="/admin/invests/edit/<?php echo $invest->id ?>" title="Modificar el aporte">[Modificar]</a></td>
                <td><?php echo $invest->amount ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><a href="/admin/users/manage/<?php echo $invest->user->id ?>" target="_blank" title="<?php echo $invest->user->name; ?>"><?php echo $invest->user->email; ?></a></td>
                <td><a href="/admin/bookas/?name=<?php echo substr($invest->booka->name, 0, 10) ?>" target="_blank"><?php echo Text::recorta($invest->booka->name, 20); ?></a></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay transacciones que cumplan con los filtros.</p>
<?php endif;?>
</div>