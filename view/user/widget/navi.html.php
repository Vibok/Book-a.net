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

$user = $this['user'];
echo Navi::html(
        array(
            'top' => array(
                'home' => array(
                    'label' => Text::get('profile-menu-home'),
                    'url' => '/user/profile/'.$user->id,
                    'disabled' => false
                ),
                'shelves' => array(
                    'label' => Text::get('profile-menu-shelves'),
                    'url' => '/user/profile/'.$user->id.'/shelves',
                    'disabled' => true
                ),
                'proposals' => array(
                    'label' => Text::get('profile-menu-proposals'),
                    'url' => '/user/profile/'.$user->id.'/proposals',
                    'disabled' => true
                )
            ),
            'bottom' => Navi::$NaviBar['bottom'],
            'current' => array(
                'top' => !empty($this['show']) ? $this['show'] : 'home',
                'bottom' => 'bookallow'
            ),
            'social' => false,
            'collections'  => true
        )
    );