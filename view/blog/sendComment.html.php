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
    Base\Model\Post;

$post = $this['post'];
?>
<?php if ($post->allow) : ?>
<script type="text/javascript">
	jQuery(document).ready(function ($) { 
	    //change div#preview content when textarea lost focus
		$("#message").blur(function(){
			$("#preview").html($("#message").val().replace(/\n/g, "<br />"));
		});
		
		//add fancybox on #a-preview click
		$("#a-preview").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
        
        <?php if (empty($_SESSION['user'])) : ?>
        $("#message").focus(function () {
            $("#advice").show();            
        });
        <?php endif; ?>
	});
</script>

<form method="post" action="/message/post/<?php echo $post->id; ?>">
    <div id="send-msg" class="activable">
        <h5 class="ct1 upcase ft2 fs-S"><?php echo Text::get('blog-send_comment-header'); ?></h5>
            <textarea id="message" name="message" cols="50" rows="5" style="width: 98%;"></textarea>
            <a target="_blank" id="a-preview" href="#preview" class="preview ct2 wshadow"><?php echo Text::get('regular-preview'); ?></a>
            <div style="display:none">
                <div id="preview" class="ft3" style="width:400px;height:300px;overflow:auto;">

                    </div>
            </div>
    </div>
    <div class="hr" style="margin:20px 0;"><hr /></div>

    <button class="std-btn" type="submit" style="float:right;"<?php if (empty($_SESSION['user'])) echo ' disabled="disabled"'; ?>><?php echo Text::get('blog-send_comment-button'); ?></button>

</form>
<?php else : ?>
    <p><?php echo Text::get('blog-comments_no_allowed'); ?></p>
<?php endif; ?>
