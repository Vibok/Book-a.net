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
    Base\Core\View;

$bodyClass = 'dashboard';

$user = $_SESSION['user'];

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>

    <div id="main">

        <div id="navi-bar">
            <ul class="top line">
                <li<?php if ($this['show'] == 'profile') echo ' class="current"'; ?>><a href="/dashboard/profile"><?php echo Text::get('dashboard-menu-profile'); ?></a></li>
                <li<?php if ($this['show'] == 'preferences') echo ' class="current"'; ?>><a href="/dashboard/preferences"><?php echo Text::get('dashboard-menu-preferences'); ?></a></li>
                <li><a href="/user/profile/<?php echo $user->id ?>" target="_blank"><?php echo Text::get('dashboard-menu-access'); ?></a></li>
            </ul>
        </div>

        <div class="widget">
            <h2 class="htitle wshadow"><?php echo Text::get('dashboard-menu-home'); ?></h2>

            <?php echo new View ('view/dashboard/user/'.$this['show'].'.html.php', $this); ?>

        </div>


    </div>

<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';
