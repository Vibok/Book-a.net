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
    Base\Library\Text;

$bodyClass = 'user';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* todo esto para paginacion si se hace con ajax */
    });
</script>

<div id="main" class="sided">
    <?php echo new View('view/user/widget/about.html.php', $this); ?>
    <?php echo new View('view/user/widget/navi.html.php', $this); ?>

    <div class="center">
        <?php // los modulos centrales son diferentes segun el show

        switch ($this['show']) {
            case 'message':
                    echo new View('view/user/widget/message.html.php', $this);
                break;
            case 'home':
            default:
                echo
                    new View('view/user/widget/invests.html.php', $this),
                    new View('view/user/widget/message.html.php', $this);
                break;
        }
        ?>
     </div>

    <div class="side">
    <?php 
        echo new View('view/user/widget/sharemates.html.php', array('user'=>$user, 'show'=>'support'));
        echo new View('view/user/widget/sharemates.html.php', array('user'=>$user, 'show'=>'shares'));
    ?>
    </div>

</div>

<?php 
include 'view/footer.html.php';
include 'view/epilogue.html.php';
