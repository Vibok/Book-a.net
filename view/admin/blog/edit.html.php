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
    Base\Model,
    Base\Core\Redirection,
    Base\Library\Lang,
    Base\Library\NormalForm;

$post = $this['post'];
$bookas = $this['bookas'];
$status = $this['status'];

if (!$post instanceof Model\Post) {
    throw new Redirection('/admin/blog');
}

$tags = array();

foreach ($this['tags'] as $value => $label) {
    $tags[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => isset($post->tags[$value])
        );
}

$allow = array(
    array(
        'value'     => 1,
        'label'     => 'Sí'
        ),
    array(
        'value'     => 0,
        'label'     => 'No'
        )
);


$images = array();
foreach ($post->gallery as $image) {
    $images[] = array(
        'type'  => 'html',
        'class' => 'gallery-image',
        'html'  => is_object($image) ?
                   $image . '<img src="'.SRC_URL.'/image/'.$image->id.'/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                   ''
    );

}

$booka_select = '<select name="booka">';
$booka_select .= '<option value="">--</option>';
foreach ($bookas as $booka) {
    $selected = ($post->booka == $booka->id) ? ' selected="selected"' : '';
    $booka_select .= '<option value="'.$booka->id.'"'.$selected.'>'.$booka->name.' ('.$status[$booka->status].')</option>';
}
$booka_select .= '</select>';

// el publicar esta oculto para los colaboradores
if (isset($_SESSION['user']->roles['vip-blog'])) {
    $publish = array(
        'type'      => 'hidden',
        'value'     => (int) $post->publish
    );
} else {
    $publish = array(
        'title'     => 'Publicado',
        'type'      => 'slider',
        'options'   => $allow,
        'class'     => 'currently cols_4',
        'value'     => (int) $post->publish
    );
}

?>
<script type="text/javascript" src="/view/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	// Lanza wysiwyg texto español
	CKEDITOR.replace('text_es_editor', {
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

	CKEDITOR.replace('text_en_editor', {
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
    
});
</script>

<a href="/admin/blog" class="button std-btn tight menu-btn tipsy" title="Volver a la lista sin guardar los cambios">Volver</a>

<div class="widget board">
    <form method="post" action="/admin/blog/<?php echo $this['action']; ?>/<?php echo $post->id; ?>" enctype="multipart/form-data">

        
    <ul id="lang-tabs">
        <?php foreach (Lang::$langs as $langId=>$langName) : ?>
            <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
        <?php endforeach; ?>
    </ul>
    <?php foreach (Lang::$langs as $langId=>$langName) : 
        $campo_titulo = 'title_'.$langId;
        $campo_texto = 'text_'.$langId;
        $campo_caption = 'legend_'.$langId;
        $campo_video = 'media_'.$langId;
        ?>
        <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
    <?php
    // campos por idioma
    echo new NormalForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'elements'      => array(
            $campo_titulo => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 100,
                'title'     => 'Título',
                'value'     => $post->$campo_titulo,
            ),
            $campo_texto => array(
                'type'      => 'textarea',
                'required'  => true,
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Texto',
                'value'     => $post->$campo_texto
            ),
            $campo_caption => array(
                'type'      => 'textbox',
                'title'     => 'Créditos de las imágenes',
                'value'     => $post->$campo_caption,
            )
            /*
            ,
            $campo_video => array(
                'type'      => 'textbox',
                'title'     => 'Vídeo',
                'class'     => 'media',
                'value'     => (string) $post->$campo_video,
                'children'  => array(
                    'media-preview' => array(
                        'title' => 'Vista previa',
                        'class' => 'media-preview',
                        'type'  => 'html',
                        'html'  => !empty($post->$campo_video) ? $post->$campo_video->getEmbedCode() : ''
                    )
                )
            ),
             */
        )

    ));
    ?>
    </div>
<?php endforeach; ?>

    <?php 
    // campos generales
    echo new NormalForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'footer'        => array(
            'button' => array(
                'type'  => 'submit',
                'name'  => 'save-post',
                'label' => 'Guardar',
                'class' => 'std-btn wide'
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'author' => array (
                'type' => 'hidden',
                'value' => $post->author
            ),
            'home' => array (
                'type' => 'hidden',
                'value' => $post->home
            ),
            'footer' => array (
                'type' => 'hidden',
                'value' => $post->footer
            ),
            'top' => array (
                'type' => 'hidden',
                'value' => $post->top
            ),
            'image' => array(
                'title'     => 'Imagen',
                'type'      => 'group',
                'class'     => 'image',
                'children'  => array(
                    'image_upload'    => array(
                        'type'  => 'file',
                        'class' => 'image_upload',
                        'label' => 'Añadir'
                    )
                )
            ),

            'gallery' => array(
                'type'  => 'group',
                'class' => '',
                'children'  => $images
            ),

            'tags' => array(
                'type'      => 'checkboxes',
                'name'      => 'tags[]',
                'title'     => 'Tags',
                'class'     => 'currently cols_3',
                'options'   => $tags
            ),

            'new-tag' => array(
                'type'  => 'html',
                'class' => '',
                'html'  => '<input type="text" name="new-tag" value=""/> <button type="submit" name="new-tag_save" class="std-btn" title="No repetir tags, consultar al administrador">Añadir</button>'
            ),

            'booka' => array(
                'title'     => 'Booka',
                'type'  => 'html',
                'class' => '',
                'html'  => $booka_select
            ),

            /*
            'url' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'enlace externo',
                'value'     => $post->url,
            ),
             * 
             */

            'date' => array(
                'type'      => 'datebox',
                'title'     => 'Fecha de publicación',
                'size'      => 8,
                'value'     => $post->date
            ),
            'allow' => array(
                'title'     => 'Permite comentarios',
                'type'      => 'slider',
                'options'   => $allow,
                'class'     => 'currently cols_4',
                'value'     => (int) $post->allow
            ),
            'publish' => $publish

        )

    ));
    ?>
            
            
    </form>
</div>