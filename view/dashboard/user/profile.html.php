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
    Base\Library\NormalForm,
    Base\Core\View;

$user   = $_SESSION['user'];
$user_data   = $this['personal'];
$errors = $this['errors'];

// categorias (varias)
$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, array_keys($user->interests))
        );            
}

$sfid = 'sf-booka-profile';

?>

<form method="post" action="/dashboard/profile" class="booka" enctype="multipart/form-data">

<?php echo new NormalForm(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'footer'        => array(
        'button' => array(
            'type'  => 'submit',
            'name'  => 'save-profile',
            'label' => Text::get('regular-save'),
            'class' => 'std-btn wide'
        )
    ),
    'elements'      => array(
        
        // datos personales
        'personal' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-personal-title'),
            'class'     => 'in-header',
            'children'  => array(
                'name' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 43,
                    'title'     => Text::get('profile-name-field'),
                    'value'     => $user->name
                ),
                'nif' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 40,
                    'title'     => Text::get('profile-nif-field'),
                    'value'     => $user_data->nif
                ),
                'address' => array(
                    'type'      => 'textbox',
                    'class'     => '',
//                    'size'      => 60,
                    'title'     => Text::get('address-address-field'),
                    'value'     => $user_data->address
                ),
                'location' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 43,
                    'title'     => Text::get('address-location-field'),
                    'value'     => $user_data->location
                ),
                'zipcode' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 40,
                    'title'     => Text::get('address-zipcode-field'),
                    'value'     => $user_data->zipcode
                ),
                'separa' => array(
                    'type' => 'html',
                    'html' => ''
                ),
                'city' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 43,
                    'title'     => Text::get('address-city-field'),
                    'value'     => $user_data->city
                ),
                'country' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'size'      => 40,
                    'title'     => Text::get('address-country-field'),
                    'value'     => $user_data->country
                ),

                'privacy' => array(
                    'type'      => 'html',
                    'html'      => '<span class="rojo">'.Text::get('profile-personal-tooltip').'</span>'
                ),

                
            )
        ),
        
        'public' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-public-title'),
            'class'     => 'in-header',
            'children'  => array(
                'avatar' => array(
                    'type'      => 'group',
                    'title'     => Text::get('profile-image-title'),
                    'class'     => 'user_avatar',
                    'children'  => array(
                        'avatar_upload'    => array(
                            'type'  => 'file',
                            'label' => Text::get('form-image_upload-button'),
                            'class' => 'avatar_upload'
                        ),
                        'avatar-current' => array(
                            'name' => 'avatar',
                            'type' => 'hidden',
                            'value' => $user->avatar->id == 1 ? '' : $user->avatar->id,
                        ),
                        'avatar-image' => array(
                            'type'  => 'html',
                            'class' => 'inline avatar-image',
                            'html'  => is_object($user->avatar) &&  $user->avatar->id != 1 ?
                                       $user->avatar . '<img src="'.SRC_URL.'/image/' . $user->avatar->id . '/128/128/1" alt="Avatar" /><button class="image-remove" type="submit" name="avatar-'.$user->avatar->id.'-remove" title="Quitar imagen" value="remove">X</button>' :
                                       ''
                        )

                    )
                ),
                
                'about' => array(
                    'type'      => 'textarea',
                    'cols'      => 40,
                    'rows'      => 4,
                    'title'     => Text::get('profile-about-field'),
                    'value'     => $user->about,
                    'placeholder' => '(100 palabras mÃ¡x.)'
                ),

                'interests' => array(    
                    'type'      => 'checkboxes',
                    'name'      => 'interests[]',
                    'title'     => Text::get('profile-interests-field'),
                    'class'     => 'cols_4',
                    'options'   => $categories
                ),       

                'keywords' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('profile-keywords-field'),
                    'class'     => '',
                    'value'     => $user->keywords
                ),

                'so-why' => array(
                    'type' => 'html',
                    'html' => Text::html('profile-why_is_important')
                )
            )
        ),

        'social' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-social-title'),
            'class'     => 'in-header',
            'children'  => array(
                'web' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('profile-website-field'),
                    'class'     => '',
                    'value'     => $user->web
                ),
                'facebook' => array(
                    'type'      => 'textbox',
                    'class'     => 'social-field facebook',
                    'size'      => 40,
//                    'title'     => Text::get('regular-facebook'),
                    'value'     => empty($user->facebook) ? Text::get('regular-facebook-url') : $user->facebook
                ),
                'twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'social-field twitter',
                    'size'      => 40,
//                    'title'     => Text::get('regular-twitter'),
                    'value'     => empty($user->twitter) ? Text::get('regular-twitter-url') : $user->twitter
                ),
                'google' => array(
                    'type'      => 'textbox',
                    'class'     => 'social-field google',
                    'size'      => 40,
//                    'title'     => Text::get('regular-google'),
                    'value'     => empty($user->google) ? Text::get('regular-google-url') : $user->google
                ),
                'linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'social-field linkedin',
                    'size'      => 40,
//                    'title'     => Text::get('regular-linkedin'),
                    'value'     => empty($user->linkedin) ? Text::get('regular-linkedin-url') : $user->linkedin
                )
            )
        )
    )
));

?>
</form>
