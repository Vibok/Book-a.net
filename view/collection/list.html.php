<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci�n Fuentes Abiertas (see README for details)
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

use Base\Core\View,
    Base\Library\Text;

$bodyClass = 'collection';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

<div id="main">

    <?php foreach ($this['list'] as $collection) : ?>
    <div class="collections-list-item">
        <?php echo new View('view/collection/widget/name.html.php', array('collection' => $collection, 'show'=>'list')); ?>
    </div>
    <?php endforeach; ?>

</div>

<?php 
include 'view/footer.html.php';
include 'view/epilogue.html.php';
?>