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
    Base\Model\Image;

$bodyClass = 'collection'; 

$collection = $this['collection'];
//die(\trace($collection));
// metas og: para que al compartir en facebook coja bien el nombre y la imagen (todas las de proyecto y las novedades
$ogmeta = array(
    'title' => Text::get('overview-field-collection').' '.$collection->name,
    'description' => Text::get('regular-by').' '.$collection->director,
    'url' => SITE_URL . '/collection/'.$collection->id
);

// todas las imagenes del booka
if (!empty($collection->image)) {
    if ($collection->image instanceof Image) {
        $ogmeta['image'] = $collection->image->getLink('436', '297', true);
    }
}


include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
    <div id="main" class="sided">
        <?php echo new View('view/collection/widget/name.html.php', $this); ?>
        <?php echo new View('view/collection/widget/navi.html.php', $this); ?>
        
        <div class="center">
        <?php echo new View('view/collection/widget/content.html.php', $this); ?>
         </div>

        <div class="side">
        <?php // el lateral: menu colecciones y top ten
            echo new View('view/collection/widget/side_menu.html.php', $this);
            echo new View('view/collection/widget/side_topten.html.php', $this);
        ?>
        </div>
    <div id="main">
    </div>

<?php 
include 'view/footer.html.php'; 
include 'view/epilogue.html.php';
?>
