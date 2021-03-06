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
use Base\Library\Text;

$booka = $this['booka'];
$step = $this['step'];
$step_errors = count($booka->errors[$step]);
$total_errors = 0;
foreach ($booka->errors as $st => $errors) {
    $total_errors += count($errors);
}

if($total_errors == 0) : ?>
<div>
    <p><?php echo Text::get('form-errors-no_errors') ?></p>
</div>
<?php else : ?>
<div>

    <?php if ($step != 'review') : ?>
    <p><?php echo Text::get('form-errors-info', $total_errors, $step_errors) ?></p>

        <?php if (!empty($booka->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($booka->errors[$step] as $id => $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php else : ?>
        <p><?php echo Text::get('form-errors-total', $total_errors) ?></p>
        <?php foreach ($booka->errors as $st => $errors)  :
            if (!empty($errors)) : ?>
            <h4 class="title"><?php echo Text::get('step-'.$st); ?></h4>
            <ul class="sf-footer-errors">
            <?php foreach ($errors as $id => $error) : ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif;
            endforeach; ?>
    <?php endif; ?>

</div>
<?php endif; ?>

<script type="text/javascript">
$(function () {    
    $('div.superform').one('sfafterupdate', function (ev, el, html) {
        Superform.updateElement($('li#errors'), null, html);
    });
});
</script>
    