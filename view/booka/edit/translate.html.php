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

$booka = $this['booka'];
$errors = $booka->errors[$this['step']] ?: array();
$okeys  = $booka->okeys[$this['step']] ?: array();

// media del proyecto
if (!empty($booka->media_en->url)) {
    $media = array(
            'type'  => 'media',
            'title' => Text::get('overview-field-media_preview'),
            'class' => 'inline media',
            'type'  => 'html',
            'html'  => !empty($booka->media_en) ? $booka->media_en->getEmbedCode($booka->media_usubs) : ''
    );
} else {
    $media = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );
}

$sfid = 'sf-booka';

$superform = array(
    'id'            => $sfid,
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'class'         => 'aqua',        
    'elements'      => array(
        'process_translate' => array (
            'type' => 'hidden',
            'value' => 'translate'
        ),

        'insideheader' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Encabezado/Widget</h3>'
        ),
        
        'name_en' => array(
            'type'      => 'textbox',
            'title'     => 'Nombre',
            'value'     => $booka->name_en
        ),
        
        'info_en' => array(
            'type'      => 'textbox',
            'title'     => 'Info adicional',
            'value'     => $booka->info_en
        ),

        'keywords_en' => array(
            'type'      => 'textbox',
            'title'     => 'Palabras clave',
            'value'     => $booka->keywords_en,
            'placeholder' => '(5 palabras máx.)'
        ),

        'description_en' => array(
            'type'      => 'textarea',
            'title'     => 'Resumen/Descripción',
            'value'     => $booka->description_en,
            'placeholder' => '(100 palabras máx.)'
        ),
        
        'insideheader' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Proyecto</h3>'
        ),

        'about_en' => array(
            'type'      => 'textarea',       
            'title'     => 'Sobre los contenidos',
            'value'     => $booka->about_en
        ),
        'motivation_en' => array(
            'type'      => 'textarea',       
            'title'     => 'Sobre los artífices',
            'value'     => $booka->motivation_en
        ),
        
        'goal_en' => array(
            'type'      => 'textarea',
            'title'     => 'Sobre la edición/colección',
            'value'     => $booka->goal_en
        ),
        'related_en' => array(
            'type'      => 'textarea',
            'title'     => 'Ficha técnica',
            'value'     => $booka->related_en
        ),
        
        'caption_en' => array(
            'type'      => 'textbox',
            'title'     => 'Créditos de las imágenes',
            'value'     => $booka->caption_en
        ),

        'media_caption_en' => array(
            'type'      => 'textbox',
            'title'     => 'Créditos del video',
            'value'     => $booka->media_caption_en
        ),


        'insideheader' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Objetivos</h3>'
        ),
        
        
        'milestone1_en' => array(
            'type'      => 'textarea',
            'title'     => 'Recorrido de la campaña',
            'value'     => $booka->milestone1_en,
            'placeholder' => '(300 palabras máx)'
        ),
       
        'milestone2_en' => array(
            'type'      => 'textarea',
            'title'     => 'Hitos marcados',
            'value'     => $booka->milestone2_en,
            'placeholder' => '(300 palabras máx)'
        ),
       
        'milestone3_en' => array(
            'type'      => 'textarea',
            'title'     => 'Limite y garantías',
            'value'     => $booka->milestone3_en,
            'placeholder' => '(300 palabras máx)'
        ),
       
        'milestone4_en' => array(
            'type'      => 'textarea',
            'title'     => '¿Y después qué?',
            'value'     => $booka->milestone4_en,
            'placeholder' => '(300 palabras máx)'
        ),
       
        /*
        'media_en' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-media'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => (string) $booka->media_en
        ),

        'media-upload' => array(
            'name' => "upload",
            'type'  => 'submit',
            'label' => Text::get('form-upload-button'),
            'class' => 'inline media-upload'
        ),
        
        'media-preview' => $media,
        */
        // fin media
        
        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'button' => array(
                    'type'  => 'submit',
                    'name'  => 'save-exit',
                    'label' => 'Guardar',
                    'class' => 'std-btn wide'
                )
            )
        
        )

    )

);


foreach ($superform['elements'] as $id => &$element) {
    
    if (!empty($this['errors'][$this['step']][$id])) {
        $element['errors'] = array();
    }
    
}

echo new SuperForm($superform);