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

$_SESSION['msg_token'] = uniqid(rand(), true);

?>
<script type="text/javascript">
	// Mark DOM as javascript-enabled
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
	});
</script>


<div id="sub-header">
    <div>
        <h2><a href="/user/<?php echo $user->id; ?>"><img src="<?php echo $user->avatar->getLink(75, 75, true); ?>" /></a> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>
</div>



<div id="main">

    <div class="center">

    <?php if (!empty($_SESSION['user']->id)) : ?>
    <div class="center-widget user-message">

        <h3 class="title"><?php echo Text::get('user-message-send_personal-header'); ?></h3>

        <form method="post" action="/message/personal/<?php echo $user->id; ?>">
            <input type="hidden" name="msg_token" value="<?php echo $_SESSION['msg_token'] ; ?>" />
            
            <label for="contact-subject"><?php echo Text::get('contact-subject-field'); ?></label>
            <input id="contact-subject" type="text" name="subject" value="" placeholder="" />
            
            <label for="message"><?php echo Text::get('contact-message-field'); ?></label>
            <textarea id="message" name="message" cols="50" rows="5"></textarea>

            <a target="_blank" id="a-preview" href="#preview" class="preview"><?php echo Text::get('regular-preview'); ?></a>
            <div style="display:none">
                <div id="preview" class="ft3" style="width:400px;height:300px;overflow:auto;">

                    </div>
            </div>



            <button class="green" type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
        </form>

    </div>
    <?php endif; ?>

        <?php echo new View('view/user/widget/about.html.php', array('user' => $user)) ?>

        <?php echo new View('view/user/widget/social.html.php', array('user' => $user)) ?>

    </div>
</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
