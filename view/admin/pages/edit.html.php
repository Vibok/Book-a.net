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
    Base\Library\Lang;

$page = $this['page'];
?>
<script type="text/javascript" src="/view/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    <?php  foreach (Lang::$langs as $langId=>$langName) : ?>
	CKEDITOR.replace('richtext_content_<?php echo $langId ?>', {
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
		width: '730px'
	});
    
    <?php endforeach;  ?>
});
</script>

<div class="widget board">
    <form method="post" action="/admin/pages/edit/<?php echo $page->id; ?>">

        <ul id="lang-tabs">
            <?php foreach (Lang::$langs as $langId=>$langName) : ?>
                <li><a href="<?php echo $langId ?>" class="lang-tab <?php if ($langId == 'es') echo 'current'; ?>"><?php echo $langName; ?></a>
            <?php endforeach; ?>
        </ul>
        <?php foreach (Lang::$langs as $langId=>$langName) : 
            $campo_text = 'text_'.$langId;
            $campo_content = 'content_'.$langId;
            ?>
            <div class="lang-content" id="lang-<?php echo $langId ?>-content" <?php if ($langId == 'es') echo ' style="display:block;"'; ?>>
                <p>
                    <label for="page-text_<?php echo $langId ?>">Cabecera:</label><br />
                    <textarea name="text_<?php echo $langId ?>" id="page-text_<?php echo $langId ?>" cols="60" rows="4"><?php echo $page->$campo_text; ?></textarea>
                </p>
                <p>
                    <label for="richtext_content_<?php echo $langId ?>">Contenido:</label><br />
                    <textarea id="richtext_content_<?php echo $langId ?>" name="content_<?php echo $langId ?>" cols="100" rows="20"><?php echo $page->$campo_content; ?></textarea>
                </p>
            </div>
        <?php endforeach; ?>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>