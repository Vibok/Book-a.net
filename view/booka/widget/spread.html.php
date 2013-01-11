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
    Base\Model\Booka;

$user    = $_SESSION['user'];
$booka = $this['booka'];

$lsuf = (LANG != 'es') ? '?lang='.LANG : '';

$url = SITE_URL . '/widget/booka/' . $booka->id;
$widget_code = Text::widget($url . $lsuf);

$share_title = Text::get('booka-spread-social', $booka->name);
$share_url = SITE_URL . '/booka/'.$booka->id;
$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url . ' #bookallow');
?>
<div class="center-widget spread-message">
    <div class="thanks ct1 fs-XL"><?php echo Text::get('regular-thanks', $_SESSION['user']->name); ?></div>
    <div class="message ct1 fs-L underlined"><?php echo Text::get('booka-invest-ok'); ?></div>
    
    <div class="booka-spread">
        <span class="ft2 fs-M bloque"><?php echo Text::get('booka-spread-header'); ?></span>
    
            <ul class="share line">
                <li class="social-button twitter"><a class="ct1" href="<?php echo htmlspecialchars($twitter_url) ?>" target="_blank"><?php echo Text::get('spread-twitter'); ?></a></li>
                <li class="social-button facebook"><a class="ct1" href="<?php echo htmlspecialchars($facebook_url) ?>" target="_blank"><?php echo Text::get('spread-facebook'); ?></a></li>
            </ul>
    </div>

    <div class="booka-embed">
        <span class="ft2 fs-M bloque"><?php echo Text::get('booka-spread-embed_header'); ?></span>

        <div id="widget-code" class="left">
            <div class="wc-embed" onclick="$('#widget_code').focus();$('#widget_code').select()"><p class="ct1"><?php echo Text::get('booka-spread-embed_code'); ?></p></div>
            <textarea id="widget_code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
        </div>
    </div>

</div>

<?php echo $widget_code; ?>

