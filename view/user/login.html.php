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
extract($_POST);
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#RegisterPassword").focus(function (event) {
        $("#password-minlength").show();
    });
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

        <a name="#access"></a>
        <div class="three-cols">
            <h3 class="htitle"><?php echo Text::get('login-oneclick-header'); ?></h3>
            
            <ul class="sign-in-with">
            <?php

            //posarem primer l'ultim servei utilitzat
            //de manera que si l'ultima vegada t'has autentificat correctament amb google, el tindras el primer de la llista

            //la cookie serveix per saber si ja ens hem autentificat algun cop amb "un sol click"
            $openid = $_COOKIE['booka_oauth_provider'];

            //l'ordre que es vulgui...
            $logins = array(
                'facebook' => '<a class="ft3" href="/user/oauth?provider=facebook">' . Text::get('login-signin-facebook') . '</a>',
                'twitter' => '<a class="ft3" href="/user/oauth?provider=twitter">' . Text::get('login-signin-twitter') . '</a>',
                'google' => '<a class="ft3" href="/user/oauth?provider=google">' . Text::get('login-signin-google') . '</a>',
                'linkedin' => '<a class="ft3" href="/user/oauth?provider=linkedin">' . Text::get('login-signin-linkedin') . '</a>'
            );

            foreach($logins as $k => $v) {
                echo '<li class="social-button '.strtolower($k) .'">'.$v.'</li>';
            }
            ?>

            </ul>
        </div>

        <div class="three-cols">
            <h3 class="htitle"><?php echo Text::get('login-access-header'); ?></h3>
            
            <form action="/user/login" method="post">
                <input type="hidden" name="return" value="<?php echo $_GET['return']; ?>" />
                <div class="field-block">
                    <label for="LoginUserid"><?php echo Text::get('login-access-username-field'); ?></label>
                    <input  id="LoginUserid" type="text" name="userid" value="<?php echo $userid?>" />
                </div>

                <div class="field-block">
                    <label for="LoginPassword"><?php echo Text::get('login-access-password-field'); ?></label>
                    <input  id="LoginUserid" type="password" name="password" value="<?php echo $username?>" />
                </div>
                
                <div class="field-block recover action">
                    <a href="/user/recover" class="ct1 fs-S ft3"><?php echo Text::get('login-recover-link'); ?></a>
                </div>

                <button type="submit" id="login-btn" name="login" class="std-btn centered wide" value="login"><?php echo Text::get('login-access-button'); ?></button>

<!--                 
                <div class="field-block leave action">
                    <a href="/user/leave" class="ct1 fs-S"><?php echo Text::get('login-leave-button'); ?></a>
                </div>
-->
                <div class="espacio-vacio-relleno" style="height: 36px;"></div>
            </form>

        </div>

        <div class="three-cols">
            <h3 class="htitle"><?php echo Text::get('login-register-header'); ?></h3>
            
            <form action="/user/register" method="post">

                <div class="field-block">
                    <label for="RegisterUserid"><?php echo Text::get('login-register-userid-field'); ?></label>
                    <input type="text" id="RegisterUserid" name="userid" value="<?php echo htmlspecialchars($userid) ?>" maxlength="15" />
                <?php if(isset($errors['userid'])) { ?><em><?php echo $errors['userid']?></em><?php } ?>
                </div>

                <div class="field-block">
                    <label for="RegisterUsername"><?php echo Text::get('login-register-username-field'); ?></label>
                    <input type="text" id="RegisterUsername" name="username" value="<?php echo htmlspecialchars($username) ?>" maxlength="20" />
                <?php if(isset($errors['username'])) { ?><em><?php echo $errors['username']?></em><?php } ?>
                </div>

                <div class="field-block">
                    <label for="RegisterEmail"><?php echo Text::get('login-register-email-field'); ?></label>
                    <input type="text" id="RegisterEmail" name="email" value="<?php echo htmlspecialchars($email) ?>"/>
                <?php if(isset($errors['email'])) { ?><em><?php echo $errors['email']?></em><?php } ?>
                </div>

                <div class="field-block">
                    <label for="RegisterREmail"><?php echo Text::get('login-register-confirm-field'); ?></label>
                    <input type="text" id="RegisterREmail" name="remail" value="<?php echo htmlspecialchars($remail) ?>"/>
                <?php if(isset($errors['remail'])) { ?><em><?php echo $errors['remail']?></em><?php } ?>
                </div>


                <div class="field-block">
                    <label for="RegisterPassword"><?php echo Text::get('login-register-password-field'); ?></label> <?php if (strlen($password) < 6) echo '<em id="password-minlength">'.Text::get('login-register-password-minlength').'</em>'; ?>
                    <input type="password" id="RegisterPassword" name="password" value="<?php echo htmlspecialchars($password) ?>"/>
                <?php if(isset($errors['password'])) { ?><em><?php echo $errors['password']?></em><?php } ?>
                </div>

                 <div class="field-block">
                    <label for="RegisterRPassword"><?php echo Text::get('login-register-confirm_password-field'); ?></label>
                    <input type="password" id="RegisterRPassword" name="rpassword" value="<?php echo htmlspecialchars($rpassword) ?>"/>
                <?php if(isset($errors['rpassword'])) { ?><em><?php echo $errors['rpassword']?></em><?php } ?>
                </div>

                <div class="field-block-conditions">
                    <input class="checkbox" id="register_accept" name="confirm" type="checkbox" value="true" />
                    <label class="ft3 fs-XS" for="register_accept"><?php echo Text::html('login-register-conditions'); ?></label>
                </div>

                <button class="std-btn centered upcase disabled" disabled="disabled" id="register_continue" name="register" type="submit" value="register"><?php echo Text::get('login-register-button'); ?></button>

            </form>
        </div>

    </div>

<?php 
include 'view/footer.html.php'; 
include 'view/epilogue.html.php'; 
