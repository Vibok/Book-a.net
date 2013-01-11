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
    Base\Library\Navi;

$collections = array();

foreach ($this['collections'] as $id=>$col) {
    $collections[$id] = array(
        'label' => $col->name,
        'url' => '/collection/'.$id,
        'disabled' => false
    );
}

echo Navi::html(
        array(
            'top' => $collections,
            'bottom' => Navi::$NaviBar['bottom'],
            'current' => array(
                'top' => '',
                'bottom' => 'bookallow'
            ),
            'social' => true,
            'collections'  => true
        )
    );
