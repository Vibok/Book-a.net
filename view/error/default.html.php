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
    Base\Library\Page;

$page = Page::get('error');

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <h2><?php echo $error->getMessage() ?>!</h2>
    </div>

    <div id="main">
        <h3 class="title"><?php echo $page->name; ?></h3>
        <?php echo $page->content; ?>
        <!-- <?php echo \trace($_SERVER); ?> -->
    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>