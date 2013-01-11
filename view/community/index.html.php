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

use Base\Core\View,
    Base\Library\Text,
    Base\Library\Feed,
    Base\Library\Navi;

$community = $this['items']['community'];
$users = $this['items']['users'];

// paginación segun la columna que tenga más
if (count($community) > count($users)) {
    $pg = Navi::calcPages(count($community), $_GET['page'], 10);
} else {
    $pg = Navi::calcPages(count($users), $_GET['page'], 10);
}



$bodyClass = 'community';
include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>

    <div id="main">

        <div class="widget">
            <h2 class="htitle wshadow"><?php echo Text::get('community-header'); ?></h2>
            <?php echo Navi::pageHtml(array('go' => $pg['next'], 'back' => $pg['prev'])); ?>
            
            <!-- dos columnas -->
            <div id="community-columns">
                
                <!-- novedades en proyectos -->
                <div class="column">
                    <h3 class="fs-L ct1"><?php echo Text::get('community-header_updates'); ?></h3>
                    <?php $c = 0;
                    foreach ($community as $item) {
                        $c++;
                        if ($c < $pg['from'] || $c > $pg['to']) continue;
                        echo Feed::subItem($item); 
                    }
                    ?>
                </div>
                
                <!-- intercambios recientes -->
                <div class="column">
                    <h3 class="fs-L ct1"><?php echo Text::get('community-header_recent'); ?></h3>
                    <?php $c = 0;
                    foreach ($users as $item) {
                        $c++;
                        if ($c < $pg['from'] || $c > $pg['to']) continue;
                        echo Feed::subItem($item); 
                    } ?>
                </div>
            </div>

            <br clear="both" />
            <br />
            <?php echo Navi::pageHtml(array('footer'=>true, 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
            
        </div>

    </div>
    
<?php 
include 'view/footer.html.php';
include 'view/epilogue.html.php';