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
/*
use Base\Core\View,
    Base\Model\User,
    Base\Model\Booka\Cost,
    Base\Model\Booka\Support,
    Base\Model\Booka\Category,
    Base\Library\Text;
*/

$booka = $this['booka'];

$bodyClass = 'booka';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

        <div id="sub-header">
            <div class="booka-header">
                <a href="/user/<?php echo $booka->owner; ?>"><img src="<?php echo $booka->user->avatar->getLink(56,56, true) ?>" /></a>
                <h2><span><?php echo htmlspecialchars($booka->name) ?></span></h2>
                <div class="booka-subtitle"><?php echo htmlspecialchars($booka->subtitle) ?></div>
                <div class="booka-by"><a href="/user/<?php echo $booka->owner; ?>"><?php echo Text::get('regular-by') ?> <?php echo $booka->user->name; ?></a></div>
                <br clear="both" />

            </div>

        </div>




        <div id="main">

            <div class="widget">
                <h3>Buy</h3>
                <p>Pagina de comprar este booka en formato papel o digital</p>
            </div>

        </div>

        <?php include 'view/footer.html.php' ?>
		<?php include 'view/epilogue.html.php' ?>
