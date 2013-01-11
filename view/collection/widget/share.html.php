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
    Base\Core\View;

$collection = $this['collection'];

$share_title = Text::get('overview-field-collection').' '.$collection->name;

$share_url = SITE_URL . '/collection/' . $collection->id;
if (LANG != 'es')
    $share_url .= '?lang=' . LANG;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url . ' #bookallow');
$google_url = 'https://m.google.com/app/plus/x/?v=compose&content=' . urlencode($share_title . ': ' . $share_url);

// si hacemos un widget de colecciones
/*<li class="embed"><a id="embed" class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo htmlentities($widget_code); ?>'); return false;">&LT; <?php echo Text::get('regular-embed') ?> &GT;</a><span class="vr ct1 fs-XS ft3">|</span></li>*/
?>
<div class="social" style="padding-bottom: 45px;">
    <ul class="share line">
        <li class="link"><a class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo $share_url ?>'); return false;"><?php echo Text::get('regular-link') ?></a></li>
        <li class="socials">
            <span class="vr ct1 fs-XS">|</span><span class="ct1 fs-XS ft3" style="margin-right: 13px;"><?php echo Text::get('regular-share') ?>:</span>
            <a class="social-button facebook" href="<?php echo $facebook_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button twitter" href="<?php echo $twitter_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button google" href="<?php echo $google_url; ?>" target="_blank">&nbsp;</a>
        </li>
    </ul>
</div>

<div class="hr" ><hr /></div>



<?php 

/*
 * 
<div id="project-code">
    <?php echo new View('view/collection/widget/collection.html.php', array('collection'=>$collection)); ?>
</div>
<div id="widget-code">
    <div class="wc-embed" onclick="$('#code').focus();$('#code').select()"></div>
    <textarea id="code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
</div>

 * 
 */