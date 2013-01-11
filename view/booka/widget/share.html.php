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

$booka = $this['booka'];

$share_title = trim($booka->clr_name);

$share_url = SITE_URL . '/booka/' . $booka->id;
if (LANG != 'es')
    $share_url .= '?lang=' . LANG;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url . ' #bookallow');
$google_url = 'https://m.google.com/app/plus/x/?v=compose&content=' . urlencode($share_title . ': ' . $share_url);

$url = SITE_URL . '/widget/booka/' . $booka->id;
if (LANG != 'es')
    $url .= '?lang=' . LANG;

$widget_code = Text::widget($url);
?>
<script type="text/javascript">
	// Mark DOM as javascript-enabled
	jQuery(document).ready(function ($) { 
		$("#embed").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
		$("#embed").click(function(){
			$("#code").select();					
		});
	});
</script>
<div class="social">
    <ul class="share line">
        <li class="embed"><a id="embed" class="ct1 fs-XS ft3" href="#embed-widget">&LT; <?php echo Text::get('regular-embed') ?> &GT;</a><span class="vr ct1 fs-XS ft3">|</span></li>
        <li class="link"><a class="ct1 fs-XS ft3" href="#" onclick="alert('<?php echo $share_url ?>'); return false;"><?php echo Text::get('regular-link') ?></a></li>
        <li class="socials">
            <span class="vr ct1 fs-XS">|</span><span class="ct1 fs-XS ft3" style="margin-right: 13px;"><?php echo Text::get('regular-share') ?>:</span>
            <a class="social-button facebook" href="<?php echo $facebook_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button twitter" href="<?php echo $twitter_url; ?>" target="_blank">&nbsp;</a>
            <a class="social-button google" href="<?php echo $google_url; ?>" target="_blank">&nbsp;</a>
        </li>
    </ul>
</div>
<div style="display:none">
    <div id="embed-widget" style="width:620px;height:623px;overflow:auto;">
        <div style="float:left; width: 260px; margin-right: 20px; padding: 20px;"><blockquote id="code" class="ft3 fs-M"><?php echo htmlentities($widget_code); ?></blockquote></div>
        <div style="float:left;"><?php echo $widget_code; ?></div>
    </div>
</div>

<br />

<div class="hr" ><hr /></div>



<?php 

/*
 * 
<div id="project-code">
    <?php echo new View('view/booka/widget/booka.html.php', array('booka'=>$booka)); ?>
</div>
<div id="widget-code">
    <div class="wc-embed" onclick="$('#code').focus();$('#code').select()"></div>
    <textarea id="code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
</div>

 * 
 */