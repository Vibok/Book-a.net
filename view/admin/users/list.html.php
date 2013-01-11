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

$pagedResults = new \Paginated($this['users'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/users/add" class="button std-btn tight menu-btn">Crear usuario</a>

<div class="widget board">
    <form id="filter-form" action="/admin/users" method="get">
        <table>
            <tr>
                <td>
                    <label for="role-filter">Con rol:</label><br />
                    <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier rol</option>
                    <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
                        <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="booka-filter">Que aportaron al booka:</label><br />
                    <select id="booka-filter" name="booka" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                        <option value="any"<?php if ($filters['booka'] == 'any') echo ' selected="selected"';?>>Algún booka</option>
                    <?php foreach ($this['bookas'] as $projId=>$projName) : ?>
                        <option value="<?php echo $projId; ?>"<?php if ($filters['booka'] == $projId) echo ' selected="selected"';?>><?php echo substr($projName, 0, 35); ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="status-filter">En estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier estado</option>
                        <option value="active"<?php if ($filters['status'] == 'active') echo ' selected="selected"';?>>Activo</option>
                        <option value="inactive"<?php if ($filters['status'] == 'inactive') echo ' selected="selected"';?>>Inactivo</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="id-filter">Id (exacto):</label><br />
                    <input id="id-filter" name="id" value="<?php echo $filters['id']; ?>" />
                </td>
                <td>
                    <label for="name-filter">Alias/Email:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
                </td>
                <td>
                    <label for="type-filter">Del  tipo:</label><br />
                    <select id="type-filter" name="type" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                    <?php foreach ($this['types'] as $type=>$desc) : ?>
                        <option value="<?php echo $type; ?>"<?php if ($filters['type'] == $type) echo ' selected="selected"';?>><?php echo $desc; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" name="filter" class="std-btn tight menu-btn">Buscar</button>
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
    <a href="/admin/users/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro.</p>
<?php elseif (!empty($this['users'])) : ?>
    <p><strong><?php echo count($this['users']) ?></strong> usuarios cumplen este filtro </p>
    <table>
        <thead>
            <tr>
                <th>Alias</th> <!-- view profile -->
                <th>User</th>
                <th>Email</th>
                <th>Bookas</th>
                <th>Cantidad</th>
                <th>Alta</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($user = $pagedResults->fetchPagedRow()) :
                    $supports = $user->support;
                    $user->num_bookas = (int) $supports['bookas'];
                ?>
            <tr>
                <td><a href="/user/profile/<?php echo $user->id; ?>" target="_blank" title="Ver perfil público"><?php echo substr($user->name, 0, 20); ?></a></td>
                <td><strong><?php echo substr($user->id, 0, 20); ?></strong></td>
                <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a></td>
                <td><?php echo (int) $user->num_bookas; ?></td>
                <td><?php echo \amount_format($supports['amount']); ?> &euro;</td>
                <td><?php echo $user->register_date; ?></td>
            </tr>
            <tr>
                <td><a href="/admin/users/manage/<?php echo $user->id; ?>" title="Gestionar">[Gestionar]</a></td>
                <td><?php if ($user->bookas > 0) : ?>
                <a href="/admin/invests/?users=<?php echo $user->id; ?>" title="Ver sus aportes">[Aportes]</a>
                <?php endif; ?></td>
                <td colspan="5" style="color:blue;">
                    <?php echo (!$user->active && $user->hide) ? ' Baja ' : ''; ?>
                    <?php echo $user->active ? '' : ' Inactivo '; ?>
                    <?php echo $user->hide ? ' Oculto ' : ''; ?>
                    <?php echo $user->checker ? ' Revisor ' : ''; ?>
                    <?php echo $user->superadmin ? ' Superadmin ' : ''; ?>
                    <?php echo $user->admin ? ' Admin ' : ''; ?>
                    <?php echo $user->director ? ' Director ' : ''; ?>
                    <?php echo $user->colBlog ? ' Colabora Blog ' : ''; ?>
                    <?php echo $user->colBooka ? ' Colabora Blog ' : ''; ?>
                </td>
            </tr>
            <tr>
                <td colspan="6"><hr /></td>
            </tr>
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
