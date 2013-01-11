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

$sfid = 'sf-booka';

// cabecera
    echo new SuperForm( array(
            'elements' => array(
                'process_milestones' => array (
                    'type' => 'hidden',
                    'value' => 'milestones'
                ),

                'inheader1' => array(
                    'type'      => 'html',
                    'html'      => '<h3 class="in-header">Objetivos</h3>'
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
        $campo_1 = 'milestone1_'.$langId;
        $campo_2 = 'milestone2_'.$langId;
        $campo_3 = 'milestone3_'.$langId;
        $campo_4 = 'milestone4_'.$langId;
        $campo_video = 'media_'.$langId;
        $campo_caption = 'media_caption_'.$langId;
        
        // media del proyecto
        if (!empty($booka->$campo_video->url)) {
            $$campo_video = array(
                    'type'  => 'media',
                    'title' => Text::get('overview-field-media_preview'),
                    'class' => 'inline booka-media',
                    'type'  => 'html',
                    'html'  => !empty($booka->$campo_video) ? $booka->$campo_video->getEmbedCode($booka->media_usubs) : ''
            );
        } else {
            $$campo_video = array(
                'type'  => 'hidden',
                'class' => 'inline'
            );
        }

        
        ?>
        <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
    <?php // campos por idioma
        echo new SuperForm( array(
                'level'         => 3,
                'elements'      => array(
                    $campo_1 => array(
                        'type'      => 'textarea',
                        'title'     => 'Recorrido de la campaña',
                        'value'     => $booka->$campo_1,
                        'placeholder' => '(300 palabras máx)'
                    ),

                    $campo_2 => array(
                        'type'      => 'textarea',
                        'title'     => 'Hitos marcados',
                        'value'     => $booka->$campo_2,
                        'placeholder' => '(300 palabras máx)'
                    ),

                    $campo_3 => array(
                        'type'      => 'textarea',
                        'title'     => 'Límite y garantías',
                        'value'     => $booka->$campo_3,
                        'placeholder' => '(300 palabras máx)'
                    ),

                    $campo_4 => array(
                        'type'      => 'textarea',
                        'title'     => '¿Y después qué?',
                        'value'     => $booka->$campo_4,
                        'placeholder' => '(300 palabras máx)'
                    ),
                    
                    $campo_video => array(
                        'type'      => 'textbox',
                        'title'     => 'Enlazar video',
                        'value'     => (string) $booka->$campo_video,
                        'placeholder' => '(opcional)'
                    ),

                    'pos_preview_'.$langId => array (
                        'type' => 'html',
                        'class' => 'inline',
                        'html' => '<a name="preview"></a>'
                    ),
                    'media-preview_'.$langId => $$campo_video,

                    $campo_caption => array(
                        'type'      => 'textbox',
                        'title'     => 'Créditos del video',
                        'value'     => $booka->$campo_caption,
                        'placeholder' => '(solo si hay video)'
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

echo wysiwygCode('milestone1_es'),
     wysiwygCode('milestone2_es'),
     wysiwygCode('milestone3_es'),
     wysiwygCode('milestone4_es'); 

echo wysiwygCode('milestone1_en'),
     wysiwygCode('milestone2_en'),
     wysiwygCode('milestone3_en'),
     wysiwygCode('milestone4_en'); 
?>
});
</script>
