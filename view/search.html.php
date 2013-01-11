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

$results = $this['results'];

// paginación
$pg = Navi::calcPages(count($results), $_GET['page']);


$bodyClass = 'search';
include 'view/prologue.html.php';
include 'view/header.html.php';
?>

<div id="main">

    <div class="widget">
        <!-- buscador avanzado
        <div id="searcher-adv">
            <form method="post" action="/search">
                <input type="text" name="query" value="" placeholder="(palabra clave, título, autor... )" autofocus id="search_query"/>
                <label for="search_query">Buscar</label> <button type="submit" class="go-btn" value="search">&GT;</button>
            </form>
        </div>
        -->

        <?php echo Navi::pageHtml(array('span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], 'resultados'), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
        
        <?php 
        $c = 0;
        foreach ($results as $booka) : 
            $c++;
            if ($c < $pg['from'] || $c > $pg['to']) continue;
            $noborder = $c == $pg['to'] ? true : false;
        ?>
        <div class="results-list-item">
            <?php echo new View('view/booka/widget/result_item.html.php', array('booka' => $booka, 'show' => 'result', 'noborder' => $noborder)); ?>
        </div>
        <?php endforeach; ?>

        <?php echo Navi::pageHtml(array('footer'=>true, 'span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], 'resultados'), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
        
    </div>

</div>

<?php 
include 'view/footer.html.php';
include 'view/epilogue.html.php';
?>
