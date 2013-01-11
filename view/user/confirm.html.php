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

$bodyClass = 'login';

include 'view/prologue.html.php';
include 'view/header.html.php';

$errors = $this['errors'];
$oauth = $this['oauth'];

extract($oauth->user_data);

if($_POST['userid']) $username = $_POST['userid'];

if($_POST['email']) $email = $_POST['email'];
if(isset($_POST['provider_email'])) $provider_email = $_POST['provider_email'];
else $provider_email = $email;

if(empty($name)) $name = strtok($email,"@");
if(is_numeric($username) || empty($username)) $username = \Base\Core\Model::idealiza(str_replace(" ","",$name));
if(empty($username)) $username = strtok($email,"@");
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#register_accept").click(function (event) {
        if (this.checked) {
            $("#register_continue").removeClass('disabled').addClass('aqua');
            $("#register_continue").removeAttr('disabled');
            $(".field-block-conditions").addClass('active');
        } else {
            $("#register_continue").removeClass('aqua').addClass('disabled');
            $("#register_continue").attr('disabled', 'disabled');
            $(".field-block-conditions").removeClass('active');
        }
    });

});
</script>

    <div id="main">
        
        <div id="sub-header" class="sh-info">
            <h2><?php echo $this['text']; ?></h2>
            <?php echo $this['content']; ?>
        </div>

        <div class="three-cols">
            <form action="/user/oauth_register" method="post">

                <div class="field-block">
                    <label for="RegisterUserid"><?php echo Text::get('login-register-userid-field'); ?></label>
                    <input type="text" id="RegisterUserid" name="userid" value="<?php echo htmlspecialchars($username) ?>"/>
                <?php if(isset($errors['userid'])) { ?><em><?php echo $errors['userid']?></em><?php } ?>
                </div>

                <div class="field-block">
                    <label for="RegisterEmail"><?php echo Text::get('login-register-email-field'); ?></label>
                    <input type="text" id="RegisterEmail" name="email" value="<?php echo htmlspecialchars($email) ?>"/>
                <?php if(isset($errors['email'])) { ?><em><?php echo $errors['email']?></em><?php } ?>
                </div>

                <div class="field-block-conditions">
                    <input class="checkbox" id="register_accept" name="confirm" type="checkbox" value="true" />
                    <label for="register_accept"><?php echo Text::html('login-register-conditions'); ?></label>
                </div>

                <button class="std-btn centered wide disabled" disabled="disabled" id="register_continue" name="register" type="submit" value="register"><?php echo Text::get('login-register-button'); ?></button>

                <?php

                //tokens para poder saber que es un registro automatico
                foreach($oauth->tokens as $key => $val) {
                        if($val['token']) echo '<input type="hidden" name="tokens[' . $key . '][token]" value="' . htmlspecialchars($val['token']) . '" />';
                        if($val['secret']) echo '<input type="hidden" name="tokens[' . $key . '][secret]" value="' . htmlspecialchars($val['secret']) . '" />';
                }
                //data extra para incluir al usuario
                foreach($oauth->user_data as $key => $val) {
                        if($val && $key!='email') echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
                }
                //proveedor
                echo '<input type="hidden" name="provider" value="' . $oauth->original_provider . '" />';
                //email original, para saber si se ha cambiado
                echo '<input type="hidden" name="provider_email" value="' . $provider_email . '" />';
                ?>

            </form>
        </div>
        
        <div class="three-cols" style="width:590px !important; padding: 15px;">
                <p><?php echo Text::get('oauth-login-imported-data'); ?></p>
                <?php
                //print_r($_POST);
                if($profile_image_url) echo '<img style="display:inline-block; margin-right: 15px;" src="'.$profile_image_url.'" alt="Imported profile image" />';
                echo '<div style="display:inline-block;">';
                foreach(array_merge($oauth->import_user_data,array('website')) as $k) {
                        if($$k) echo '<strong>'.Text::get('oauth-import-'.$k).':</strong><br />'.nl2br($$k)."<br /><br />\n";
                }

                echo '</div>';
                ?>
        </div>
    </div>

<?php 
include 'view/footer.html.php'; 
include 'view/epilogue.html.php'; 
