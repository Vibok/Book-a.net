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

?>
<script type="text/javascript">
    $(function(){
        $(".go-step").click(function (event) {
            event.preventDefault();
            
            var go = $(this).attr('href').replace('#', '');

            $("#next-step").val(go);
            $("#next-step").attr('name', 'view-step-'+go);
            $("#booka_form").submit();

        });

    });
</script>


<div id="navi-bar">
    <ul class="top line">
        <?php foreach ($this['steps'] as $step) : ?>
        <li><a class="go-step<?php if ($step == $this['step']) echo ' current'; ?>" href="#<?php echo $step; ?>"><?php echo Text::get('step-'.$step); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>