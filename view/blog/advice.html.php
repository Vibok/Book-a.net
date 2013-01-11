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
	  jQuery(document).ready(function ($) { 
		   $(".advice-close").click(function (event) {
					$("#advice").fadeOut(2000);
           });
	  });
</script>
    <div id="advice" style="display:none; width: 615px !important;">
    	<div id="advice-content" style="width: auto !important;">
        	<input type="button" class="advice-close" >
            <ul>
                <li>
                    <span class="ui-icon ui-icon-error">&nbsp;</span>
                    <span><?php echo Text::get('user-login-required'); ?></span>
                </li>
            </ul>
		</div>
    </div>
