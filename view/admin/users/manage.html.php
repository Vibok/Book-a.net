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
    Base\Model\User;

$user = $this['user'];
$roles = User::getRolesList();
?>
<div class="widget">
    <table>
        <tr>
            <td width="140px">Nombre de usuario</td>
            <td><a href="/user/profile/<?php echo $user->id ?>" target="_blank"><?php echo $user->name ?></a></td>
        </tr>
        <tr>
            <td>Login de acceso</td>
            <td><strong><?php echo $user->id ?></strong></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $user->email ?></td>
        </tr>
        <tr>
            <td>Roles actuales</td>
            <td>
                <?php
                foreach ($user->roles as $role=>$roleData) {
                    if (in_array($role, array('user', 'superadmin', 'root'))) {
                        echo '['.$roleData->name . ']&nbsp;&nbsp;';
                    } else {
                        // onclick="return confirm('Se le va a quitar el rol de <?php echo $roleData->name ? > a este usuario')"
                        ?>
                        [<a href="/admin/users/manage/<?php echo $user->id ?>/no<?php echo $role ?>" style="color:red;text-decoration:none;"><?php echo $roleData->name ?></a>]&nbsp;&nbsp;
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Roles disponibles</td>
            <td>
                <?php
                foreach ($roles as $roleId=>$roleName) {
                    if (!in_array($roleId, array_keys($user->roles)) && !in_array($roleId, array('root', 'superadmin'))) {
                        // onclick="return confirm('Se le va a dar el rol de <?php echo $roleName ? > a este usuario')"
                        ?>
                        <a href="/admin/users/manage/<?php echo $user->id ?>/<?php echo $roleId ?>" style="color:green;text-decoration:none;">[<?php echo $roleName ?>]</a>&nbsp;&nbsp;
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Estado de la cuenta</td>
            <td>
                <?php if ($user->active) : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/ban"; ?>" style="color:green;text-decoration:none;font-weight:bold;">Activa</a>
                <?php else : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/unban"; ?>" style="color:red;text-decoration:none;font-weight:bold;">Inactiva</a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Visibilidad</td>
            <td>
                <?php if (!$user->hide) : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/hide"; ?>" style="color:green;text-decoration:none;font-weight:bold;">Visible</a>
                <?php else : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/show"; ?>" style="color:red;text-decoration:none;font-weight:bold;">Oculto</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<div class="widget board">
    <ul>
        <li><a href="/admin/users/edit/<?php echo $user->id; ?>">[Cambiar email/contraseña]</a></li>
        <li><a href="/admin/users/impersonate/<?php echo $user->id; ?>">[Suplantar]</a></li>
        <li><a href="/admin/invests/?name=<?php echo $user->email; ?>">[Historial aportes]</a></li>
        <li><a href="/admin/sended/?user=<?php echo $user->email; ?>">[Historial envíos]</a></li>
    </ul>
</div>

<div class="widget board">
    <form action="/admin/users/level/<?php echo $user->id; ?>" method="post">
        <p> <label>Nivel:
            <select name="level">
                <?php for ($i = 0; $i<10; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php if ($i == $user->level) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select></label>
        </p>

        <p>
            <input type="submit" value="Aplicar" />
        </p>
    </form>
</div>

<?php if (isset($user->roles['director'])) : ?>
<div class="widget board">
    <h3>Este usuario es director de colección, selecciona la colección que controla</h3>
    <form id="collection_form" action="/admin/users/collection/<?php echo $user->id ?>" method="post">
        <select name="collection" onchange="document.getElementById('collection_form').submit()">
            <option value="">Ninguna colección</option>
            <?php foreach ($this['collections'] as $item=>$itemName) {
                $selected = ($item == $user->collection) ? ' selected="selected"' : ''; 
                echo '<option value="'.$item.'"'.$selected.'>'.$itemName.'</option>';
            } ?>
        </select>
    </form>
    
</div>
<?php endif; ?>

