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
    Base\Library\Text;

?>
<!-- Bookas que cofinancio -->
<?php if (!empty($this['invests'])) : ?>
    <div class="widget bookas">
        <h2><?php echo Text::get('profile-invest_on-header'); ?></h2>
        <?php foreach ($this['invests'] as $booka) :

            // codigos widgets
            $url = SITE_URL . '/widget/booka/' . $booka->id;
            $widget_code = Text::widget($url . $lsuf);
            $widget_code_investor = Text::widget($url.'/invested/'.$user->id.'/'.$lsuf);
            ?>
        <p>Cofinancias el booka tal, puedes difundirlo mediante widget, facebook, twitter </p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

