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

$linkCode = '<link rel="stylesheet" type="text/css" href="'.SRC_URL.'/view/css/%s" />';

if ($bodyClass == 'screen') {
    echo sprintf ($linkCode, 'styles.css');
} else {
    echo sprintf ($linkCode, 'main.css');
    if (!empty($bodyClass))
        echo sprintf ($linkCode, $bodyClass.'.css');
}

/* Páginas con widgets o listas de proyectos/libros */
if (in_array($bodyClass, array('home', 'search', 'booka', 'collection', 'user'))) {
    echo sprintf ($linkCode, 'widget/booka.css');
}

/* Páginas con widgets o listas de usuarios */
if (in_array($bodyClass, array('community', 'user', 'booka', 'collection'))) {
    echo sprintf ($linkCode, 'widget/user.css');
}
?>
