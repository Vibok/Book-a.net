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
    Base\Library\Lang,
    Base\Model\Image;

$collection = $this['collection'];
?>
<div class="widget board">
    <form action="/admin/collections/edit" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $collection->id; ?>" />
        <input type="hidden" name="order" value="<?php echo $collection->order; ?>" />
        <input type="hidden" name="image" value="<?php echo $collection->image->id; ?>" />

        <ul id="lang-tabs">
            <?php foreach (Lang::$langs as $langId=>$langName) : ?>
                <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
            <?php endforeach; ?>
        </ul>
        <?php foreach (Lang::$langs as $langId=>$langName) : 
            $campo_nombre = 'name_'.$langId;
            $campo_palabras = 'keywords_'.$langId;
            $campo_entradilla = 'description_'.$langId;
            $campo_texto = 'text_'.$langId;
            ?>
            <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
            <p>
                <label for="name_<?php echo $langId ?>">Nombre:</label><br />
                <input type="text" name="name_<?php echo $langId ?>" id="name_<?php echo $langId ?>" value="<?php echo $collection->$campo_nombre; ?>" style="width: 250px;"/>
            </p>
            <p>
                <label for="keywords_<?php echo $langId ?>">Palabras clave:</label><br />
                <input type="text" name="keywords_<?php echo $langId ?>" id="keywords_<?php echo $langId ?>" value="<?php echo $collection->$campo_palabras; ?>" style="width: 400px;"/>
            </p>
            <p>
                <label for="description_<?php echo $langId ?>">Entradilla:</label><br />
                <textarea name="description_<?php echo $langId ?>" id="description_<?php echo $langId ?>_editor" ><?php echo $collection->$campo_entradilla; ?></textarea>
            </p>
            <p>
                <label for="text_<?php echo $langId ?>">Contenido:</label><br />
                <textarea name="text_<?php echo $langId ?>" id="text_<?php echo $langId ?>_editor"><?php echo $collection->$campo_texto; ?></textarea>
            </p>
            </div>
        <?php endforeach; ?>
        
        <p>
            <label for="director">Director:</label><br />
            <input type="text" name="director" id="director" value="<?php echo $collection->director; ?>" style="width: 250px;"/>
        </p>

        <?php if (!isset($_SESSION['user']->roles['director'])) : ?>
        <p>
            <label>Color:</label><br />
            <input type="text" name="color" value="<?php echo $collection->color; ?>" size="10" class="color"/>
        </p>
        <?php else : ?>
            <input type="hidden" name="color" value="<?php echo $collection->color; ?>" />
        <?php endif; ?>

        <p>
            <label for="new-image">Imagen:</label><br />
            <input type="file" name="new-image" id="new-image" />
        </p>
        
        <?php if (!empty($collection->image) && $collection->image instanceof Image) : ?>
            <div>
                <img src="<?php echo $collection->image->getLink(220, 151); ?>" alt="IMAGEN" title="<?php echo $collection->name_es ?>">
                <input type="submit" name="image-remove" value="X" class="std-btn remove" title="Quitar esta imagen" />
            </div>
        <?php endif; ?>
        
        <input type="submit" name="save" value="Guardar" />
    </form>
</div>
<script type="text/javascript" src="/view/js/jscolor/jscolor.js"></script>
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
     wysiwygCode('description_en'),
     wysiwygCode('text_es'),
     wysiwygCode('text_en'); 
?>
});
</script>

