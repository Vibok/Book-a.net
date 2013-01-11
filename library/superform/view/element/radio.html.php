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
use Base\Core\View;
?>
<label class="ft3">

    
    <input id="<?php echo htmlspecialchars($this['id']) ?>" type="radio" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo 'checked="checked"' ?> />
    <?php if (isset($this['label'])) echo htmlspecialchars($this['label']); ?>
    
</label>

<?php if (!empty($this['children'])): ?>
<div class="<?php if (!$this['checked']) echo 'jshidden ' ?>children" id="<?php echo htmlspecialchars($this['id']) ?>-children">
        <?php echo new View('library/superform/view/elements.html.php', Base\Library\SuperForm::getChildren($this['children'], $this['level'])) ?>
</div>
<?php  /*
<script type="text/javascript">
$(function () {
   $("div.superform input#<?php echo $this['id'] ?>").click(function () {
       
       $(this).closest('li.group').first().find("input[type='radio'][name='<?php echo $this['name'] ?>']").each(function (i, r) {
          try {
              if ('<?php echo $this['id'] ?>' == r.id) {
                  $('div.children#' + r.id + '-children').slideDown(400);
              } else {
                  $('div.children#' + r.id + '-children').slideUp(400);
              }
          } catch (e) {}
       });       
   });
});  
</script>
 */ ?>
<?php endif; ?>

<?php  /*
<script type="text/javascript">
<?php include __DIR__ . '/radio.js.src.php' ?>
</script>
 */ ?>
