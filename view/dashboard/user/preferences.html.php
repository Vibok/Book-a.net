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
    Base\Library\NormalForm;

$user = $_SESSION['user'];
$errors = $this['errors'];

$preferences = $this['preferences'];

$notifis = array(
    array(
        'value'     => 'updates',
        'label'     => Text::get('user-preferences-updates'),
        'checked'   => (int) $preferences->updates
    ),
    array(
        'value'     => 'messages',
        'label'     => Text::get('user-preferences-messages'),
        'checked'   => (int) $preferences->messages
    ),
    array(
        'value'     => 'progress',
        'label'     => Text::get('user-preferences-progress'),
        'checked'   => (int) $preferences->progress
    ),
    array(
        'value'     => 'mailing',
        'label'     => Text::get('user-preferences-mailing'),
        'checked'   => (int) $preferences->mailing
    ),
    array(
        'value'     => 'sideads',
        'label'     => Text::get('user-preferences-sideads'),
        'checked'   => (int) $preferences->sideads
    )
);



extract($_POST);
?>
<a name="check"></a>
<form action="/dashboard/preferences" method="post" enctype="multipart/form-data">

<?php
echo new NormalForm(array(

    'level'         => 3,
    'method'        => 'post',
    'elements'      => array(

        'inheader1' => array(
            'type'      => 'html',
            'html'      => '<h3 class="in-header">'.Text::get('dashboard-menu-profile-access').'</h3>'
        ),
        
        'data' => array(
            'type'  => 'html',
            'html'  => '<span class="upcase fs-M ct1">'.Text::get('login-register-userid-field').':</span>  <span class="ct2">'.$user->id . '</span>' .
                 '<br /><br /><span class="upcase fs-M ct1">'.Text::get('login-register-email-field') .':</span>  <span class="ct2">'.$user->email . '</span>'
        ),

        'change_email' => array(
            'type'      => 'group',
            'title' => Text::get('user-changeemail-title'),
            'class' => 'in-header',
            'children'  => array(
                'user_nemail' => array(
                    'type'  => 'textbox',
                    'class' => '',
                    'title' => Text::get('login-register-email-field'),
                    'hint'=> $errors['email'],
                    'value' => $user_nemail
                ),
                'user_remail' => array(
                    'type'  => 'textbox',
                    'class' => '',
                    'title' => Text::get('login-register-confirm-field'),
                    'hint'=> $errors['email_retry'],
                    'value' => $user_remail
                ),
                'change_email' => array(
                    'type'      => 'submit',
                    'name'      => 'save-email-data',
                    'label'     => Text::get('form-apply-button'),
                    'class'     => 'std-btn wide'
                )

            )
        ),

        'change_password' => array(
            'type'      => 'group',
            'title' => Text::get('user-changepass-title'),
            'class' => 'in-header',
            'children'  => array(
                'pass_anchor' => array(
                    'type'  => 'html',
                    'class' => '',
                    'html'  => '<a name="password"></a>' . $messge
                ),
                'user_npassword' => array(
                    'type'  => 'password',
                    'class' => '',
                    'title' => Text::get('user-changepass-new'),
                    'hint'=> $errors['password_new'],
                    'value' => $user_npassword
                ),
                'user_rpassword' => array(
                    'type'  => 'password',
                    'class' => '',
                    'title' => Text::get('user-changepass-confirm'),
                    'hint'=> $errors['password_retry'],
                    'value' => $user_rpassword
                ),
                'change_password' => array(
                    'type'      => 'submit',
                    'name'      => 'save-password-data',
                    'label'     => Text::get('form-apply-button'),
                    'class'     => 'std-btn wide'
                )

            )
        ),

        'prefers' => array(
            'type'      => 'group',
            'title' => Text::get('user-preferences-title'),
            'class' => 'in-header',
            'children'  => array(
                'preferences' => array(
                    'type'      => 'checkboxes',
                    'name'      => 'preferences[]',
                    'class'     => '',
                    'options'   => $notifis
                ),

                'change_preferences' => array(
                    'type'      => 'submit',
                    'name'      => 'save-preferences',
                    'label'     => Text::get('form-apply-button'),
                    'class'     => 'std-btn wide'
                )
            )
        ),
        
    )

));

?>

</form>
<hr />
<a class="button remove" href="<?php echo SITE_URL ?>/user/leave?email=<?php echo $user->email ?>"><?php echo Text::get('login-leave-header'); ?></a>
