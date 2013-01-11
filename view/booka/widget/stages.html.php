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
use Base\Library\Text;

$booka = $this['booka'];
$stage = $booka->stageData;

$stages = array(
    1 => 'Edición impresa',
    2 => 'Edición internacional',
    3 => 'Edición en la nube'
);
?>        
<div id="booka-stages">
    <ul class="line">
        <!-- orden a la inversa para el float right -->
        <?php for ($s=3; $s>0; $s--) {
            $got = ($stage->current >= $s) ? ' archieved' : '';
            echo '<li class="stage'.$s.' tipsy'.$got.'" title="'.$stages[$s].'">'.$s.'</li>';
        } ?>
    </ul>
</div>

