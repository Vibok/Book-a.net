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
    Base\Library\SuperForm;

$bodyClass = 'booka';

$booka = $this['booka'];

//die(\trace($this));
//    Base\Library\Advice::Info(Text::get('form-ajax-info'));

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
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

    <div id="main" class="booka-form <?php echo htmlspecialchars($this['step']) ?>">

        <form id="booka_form" method="post" action="/booka/edit/<?php echo $this['booka']->id ?>" class="booka" enctype="multipart/form-data" >

            <input type="hidden" id="next-step" name="view-step-<?php echo $this['step'] ?>" value="please" />

            <?php echo new View('view/booka/edit/steps.html.php', $this); ?>

            <div class="widget board">
                <h2 class="htitle"><?php echo (empty($booka->name_es)) ? "Crear proyecto" : "Editando <span>{$booka->name_es}</span>"; ?></h2>
                <?php echo new View("view/booka/edit/{$this['step']}.html.php", $this) ?>
            </div>

        </form>

    </div>

<?php 
include 'view/footer.html.php';
include 'view/epilogue.html.php';
