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
    Base\Library\Lang,
    Base\Library\SuperForm;

$booka = $this['booka'];

$images = array();
foreach ($booka->gallery as $image) {
    $images[] = array(
        'type'  => 'html',
        'class' => 'inline gallery-image',
        'html'  => is_object($image) ?
                   $image . '<img src="'.SRC_URL.'/image/'.$image->id.'/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                   ''
    );

}

$images2 = array();
foreach ($booka->gallery2 as $image2) {
    $images2[] = array(
        'type'  => 'html',
        'class' => 'inline gallery-image',
        'html'  => is_object($image2) ?
                   $image2 . '<img src="'.SRC_URL.'/image/'.$image2->id.'/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery2-'.$image2->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                   ''
    );

}

// categorias (varias)
$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $booka->categories)
        );            
}

// coleccion una
$collections = array();

foreach ($this['collections'] as $value => $label) {
    $collections[] =  array(
        'name' => 'collection',
        'label'     => $label,
        'value'     => $value,
        'checked' => ($value == $booka->collection) ? true : false
        );            
}

$sfid = 'sf-booka';

// cabecera
    echo new SuperForm( array(
            'elements' => array(
                'process_overview' => array (
                    'type' => 'hidden',
                    'value' => 'overview'
                ),

                'inheader1' => array(
                    'type'      => 'html',
                    'html'      => '<h3 class="in-header">Contenidos por idioma</h3><br />'
                )
            )
        )
    );
?>
    <ul id="lang-tabs">
        <?php foreach (Lang::$langs as $langId=>$langName) : ?>
            <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
        <?php endforeach; ?>
    </ul>
    <?php foreach (Lang::$langs as $langId=>$langName) : 
        $campo_nombre = 'name_'.$langId;
        $campo_info = 'info_'.$langId;
        $campo_keywords = 'keywords_'.$langId;
        $campo_description = 'description_'.$langId;
        $campo_about = 'about_'.$langId;
        $campo_motivation = 'motivation_'.$langId;
        $campo_goal = 'goal_'.$langId;
        $campo_related = 'related_'.$langId;
        $campo_caption = 'caption_'.$langId;
        ?>
        <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
    <?php // campos por idioma
        echo new SuperForm( array(
                'level'         => 3,
                'elements'      => array(
                    $campo_nombre => array(
                        'type'      => 'textbox',
                        'title'     => 'Título',
                        'value'     => $booka->$campo_nombre
                    ),

                    $campo_info => array(
                        'type'      => 'textbox',
                        'title'     => 'Info adicional',
                        'value'     => $booka->$campo_info
                    ),

                    $campo_keywords => array(
                        'type'      => 'textbox',
                        'title'     => 'Palabras clave',
                        'value'     => $booka->$campo_keywords
                    ),

                    $campo_description => array(
                        'type'      => 'textarea',
                        'title'     => 'Resumen/Descripción',
                        'value'     => $booka->$campo_description
                    ),

                    $campo_about => array(
                        'type'      => 'textarea',       
                        'title'     => 'Sobre los contenidos',
                        'value'     => $booka->$campo_about
                    ),
                    $campo_motivation => array(
                        'type'      => 'textarea',       
                        'title'     => 'Sobre los artífices',
                        'value'     => $booka->$campo_motivation
                    ),
                    $campo_goal => array(
                        'type'      => 'textarea',
                        'title'     => 'Sobre la edición / coleción',
                        'value'     => $booka->$campo_goal
                    ),

                    $campo_related => array(
                        'type'      => 'textarea',
                        'title'     => 'Ficha técnica',
                        'value'     => $booka->$campo_related
                    ),

                    $campo_caption => array(
                        'type'      => 'textbox',
                        'title'     => 'Créditos de las imágenes',
                        'value'     => $booka->$campo_caption
                    ),
                )
            )
        ); 
    ?>        
        </div>
    <?php endforeach; ?>

    <?php 
    // campos generales
    echo new SuperForm(array(
                'id'            => $sfid,
                'level'         => 3,
                'action'        => '',
                'method'        => 'post',
                'class'         => 'aqua',        
                'elements'      => array(
                    'inheader2' => array(
                        'type'      => 'html',
                        'html'      => '<h3 class="in-header">Generales</h3>'
                    ),

                    'author' => array(
                        'type'      => 'textbox',
                        'title'     => 'Autor',
                        'value'     => $booka->author
                    ),

                    'collection' => array(
                        'name'      => 'collection',
                        'type'      => 'radios',
                        'title'     => 'Selecciona la colección',
                        'class'     => 'cols_4',
                        'options'   => $collections
                    ),

                    'category' => array(    
                        'type'      => 'checkboxes',
                        'name'      => 'categories[]',
                        'title'     => 'Selecciona los temas',
                        'class'     => 'cols_4',
                        'options'   => $categories
                    ),       

                    'hash-gallery' => array(
                        'type'      => 'html',
                        'html'     => '<a name="gallery">&nbsp;</a>'
                    ),

                    'images' => array(        
                        'title'     => 'Imágenes de contenido para el encabezado (5 máx.)',
                        'type'      => 'group',
                        'hint'      => '* Atención: Solo imágenes sobre los contenidos',
                        'class'     => 'images',
                        'children'  => array(
                            'image_upload'    => array(
                                'type'  => 'file',
                                'label' => 'Añadir',
                                'class' => 'inline image_upload'
                            )
                        )
                    ),        
                    'gallery' => array(
                        'type'  => 'group',
                        'title' => '',
                        'class' => 'inline',
                        'children'  => $images
                    ),

                    'inheader3' => array(
                        'type'      => 'html',
                        'html'      => '<h3 class="in-header">Imágenes de contenido</h3>'
                    ),

                    'hash-gallery2' => array(
                        'type'      => 'html',
                        'html'     => '<a name="gallery2">&nbsp;</a>'
                    ),

                    'images2' => array(        
                        'title'     => 'Imágenes del autor / proceso (5 máx.)',
                        'type'      => 'group',
                        'hint'      => '* Atención: Solo imágenes sobre el autor / proceso',
                        'class'     => 'images',
                        'children'  => array(
                            'image2_upload'    => array(
                                'type'  => 'file',
                                'label' => 'Añadir',
                                'class' => 'inline image_upload'
                            )
                        )
                    ),        
                    'gallery2' => array(
                        'type'  => 'group',
                        'title' => '',
                        'class' => 'inline',
                        'children'  => $images2
                    ),

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
            )
        );
?>        
<script type="text/javascript" src="/view/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function(){
<?php

function wysiwygCode($field) {
    $code = "
    CKEDITOR.replace('{$field}_editor', {
		toolbar: 'Full',
		toolbar_Full: [
				['Source','-'],
				['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
				['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
				'/',
				['Bold','Italic','Underline','Strike'],
				['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['Link','Unlink','Anchor'],
                ['Image','Format','FontSize'],
			  ],
		skin: 'v2',
		language: 'es',
		height: '300px',
		width: '675px'
	});
";
    return $code;
}

echo wysiwygCode('description_es'),
     wysiwygCode('about_es'),
     wysiwygCode('motivation_es'),
     wysiwygCode('goal_es'),
     wysiwygCode('related_es');
        
echo wysiwygCode('description_en'),
     wysiwygCode('about_en'),
     wysiwygCode('motivation_en'),
     wysiwygCode('goal_en'),
     wysiwygCode('related_en'); 
?>
});
</script>

