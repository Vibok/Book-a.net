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
    Base\Library\Navi,
    Base\Library\Text;

//si no tiene impulsados nada
if (empty($this['invest_on'])) return '';

// paginacion
$pg = Navi::calcPages(count($this['invest_on']), $_GET['page']);

?>
<div class="center-widget pagination">
    <?php echo Navi::pageHtml(array('span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], 'book-as impulsados'), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
</div>
<?php $b = 0;
foreach ($this['invest_on'] as $booka) :
    $b++;
    if ($b < $pg['from'] || $b > $pg['to']) continue;
?>
<div class="center-widget invested-list-item">
    <?php echo new View('view/booka/widget/result_item.html.php', array('booka' => $booka, 'show' => 'invested', 'user' => $this['user'])); ?>
</div>
<?php endforeach; ?>
