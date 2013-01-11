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
    Base\Core\ACL,
    Base\Controller\Admin;

if (LANG != 'es') {
    header('Location: /admin/?lang=es');
}

if (!isset($_SESSION['admin_menu'])) {
    $_SESSION['admin_menu'] = Admin::menu();
}

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/admin/header.html.php'; ?>
<!-- funcion jquery para cambiar idioma -->
<script type="text/javascript">
    $(function(){
        $(".lang-tab").click(function (event) {
            event.preventDefault();

            $(".lang-tab").removeClass('current');
            $(this).addClass('current');
            $(".lang-content").hide();
            $("#lang-" + $(this).attr('href') + "-content").show();

        });
    });
</script>
        <div id="main">

        <?php if (!empty($this['folder']) && !empty($this['file'])) : 
                if ($this['folder'] == 'base') {
                    $path = 'view/admin/'.$this['file'].'.html.php';
                } else {
                    $path = 'view/admin/'.$this['folder'].'/'.$this['file'].'.html.php';
                }

                echo new View ($path, $this);

                else : ?>
            <div class="admin-menu" style="width:940px; margin:0 auto;">
                <?php foreach ($_SESSION['admin_menu'] as $sCode=>$section) : ?>
                <fieldset>
                    <legend><?php echo $section['label'] ?></legend>
                    <ul>
                    <?php foreach ($section['options'] as $oCode=>$option) :
                        echo '<li><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>';
                    endforeach; ?>
                    </ul>
                </fieldset>

                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div> <!-- fin main -->

<?php
    include 'view/admin/footer.html.php';
include 'view/epilogue.html.php';
