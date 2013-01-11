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

// paginacion
require_once 'library/pagination/pagination.php';

$filters = $this['filters'];

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new \Paginated($this['bookas'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/bookas/add" class="button std-btn tight menu-btn">Crear nuevo booka</a>

<div class="widget board">
    <form id="filter-form" action="/admin/bookas" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <table>
            <tr>
                <td>
                    <label for="proj_name-filter">Nombre del booka:</label><br />
                    <input id="proj_name-filter" name="name" value="<?php echo $filters['name']; ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="collection-filter">De la colección:</label><br />
                    <select id="collection-filter" name="collection" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier colección</option>
                    <?php foreach ($this['collections'] as $collectionId=>$collectionName) : ?>
                        <option value="<?php echo $collectionId; ?>"<?php if ($filters['collection'] == $collectionId) echo ' selected="selected"';?>><?php echo $collectionName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="-1"<?php if ($filters['status'] == -1) echo ' selected="selected"';?>>Todos los estados</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="category-filter">De la categoría:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($this['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" name="filter" class="std-btn">Buscar</button>
                </td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
    <br clear="both" />
    <a href="/admin/bookas/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro.</p>
<?php elseif (!empty($this['bookas'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- edit -->
                <th>Booka</th> <!-- ptreview -->
                <th>Colección</th>
                <th>Estado</th>
                <th>Despegue</th>
                <th>Coste</th> <!-- segun estado -->
                <th>Conseguido</th> <!-- segun estado -->
            </tr>
        </thead>

        <tbody>
            <?php while ($booka = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td><a href="/booka/edit/<?php echo $booka->id; ?>">[Editar]</a></td>
                <td><a href="/booka/<?php echo $booka->id; ?>" target="_blank" title="Preview"><?php echo $booka->name; ?></a></td>
                <td style="color:<?php echo '#'.$booka->collData->color; ?>;"><?php echo strtoupper($booka->collData->name); ?></td>
                <td><?php echo $this['status'][$booka->status]; ?></td>
                <td><?php if ($booka->status > 1 && !empty($booka->published)) echo $booka->published; ?></td>
                <td><?php echo $booka->cost; ?></td>
                <td><?php echo $booka->invested; ?></td>
            </tr>
        <?php if (!isset($_SESSION['user']->roles['vip-booka'])) : ?>                
            <tr>
                <td colspan="8">
                    REVISIÓN:&nbsp;&nbsp;
                    <a href="/admin/users/?id=<?php echo $booka->owner; ?>" target="_blank">[Gestionar Creador]</a>&nbsp;
                    <a href="/admin/invests/?bookas=<?php echo $booka->id; ?>" title="Ver aportes">[Aportes]</a>&nbsp;
                    <a href="/admin/users/?booka=<?php echo $booka->id; ?>" title="Ver cofinanciadores">[Cofinanciadores]</a>&nbsp;
                    <a href="/admin/bookas/report/<?php echo $booka->id; ?>" target="_blank">[Informe Financiacion]</a>&nbsp;
                    <a href="<?php echo "/admin/bookas/dates/{$booka->id}"; ?>">[Cambiar fechas]</a>
                    <a href="<?php echo "/admin/bookas/images/{$booka->id}"; ?>">[Ordenar imagenes]</a>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    PROCESO:&nbsp;&nbsp;
                    <?php if ($booka->status < 2) : ?><a href="<?php echo "/admin/bookas/review/{$booka->id}"; ?>" onclick="return confirm('El creador no podrá editar más este Booka, ok?');">[A revisión]</a><?php endif; ?>&nbsp;
                    <?php if ($booka->status < 3 && $booka->status > 0) : ?><a href="<?php echo "/admin/bookas/publish/{$booka->id}"; ?>" onclick="return confirm('Este Booka inicia su campaña de crowdfunding, ¿comenzamos?');">[Publicar]</a><?php endif; ?>&nbsp;
                    <?php if ($booka->status != 1) : ?><a href="<?php echo "/admin/bookas/enable/{$booka->id}"; ?>" onclick="return confirm('Mucho Ojo! Este Booka dejará de estar en campaña (si lo estuviera), ¿Reabrimos la edicion?');">[Reabrir]</a><?php endif; ?>&nbsp;
                    <?php if ($booka->status == 4) : ?><a href="<?php echo "/admin/bookas/fulfill/{$booka->id}"; ?>" onclick="return confirm('Se dará por completado exitosamente el proceso de crowdfunding de este Booka');">[Producido]</a><?php endif; ?>&nbsp;
                    <?php if ($booka->status < 3 && $booka->status > 0) : ?><a href="<?php echo "/admin/bookas/cancel/{$booka->id}"; ?>" onclick="return confirm('El booka va a desaparecer del admin, solo se podra recuperar desde la base de datos, Ok?');">[Descartar]</a><?php endif; ?>&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="8"><hr /></td>
            </tr>
        <?php endif; ?>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
<ul id="pagination">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation($the_filters); ?>
</ul>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
