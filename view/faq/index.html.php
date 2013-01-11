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

$current = $this['current'];

$bodyClass = 'faq';

include 'view/prologue.html.php';
include 'view/header.html.php';
# $go_up = Text::get('regular-go_up');
?>
<script type="text/javascript">
    $(function(){
        
        var lpos = $("#faq-content").offset().left;
        var pos = lpos +640;
        $("#faq-side").css({left:pos+'px'});
        
        $(window).resize(function () {
            var lpos = $("#faq-content").offset().left;
            var pos = lpos +640;
            $("#faq-side").css({left:pos+'px'});
        });
        
        $(window).scroll(function(){
            $("#faq-side").css("top",Math.max(0,200-$(this).scrollTop()));
        });

        /*
        $(".faq-question").click(function (event) {
            event.preventDefault();

            if ($($(this).attr('href')).is(":visible")) {
                $($(this).attr('href')).hide();
            } else {
                $($(this).attr('href')).show();
            }

        });

        var hash = document.location.hash;
        if (hash != '') {
            $(hash).show();
        }
        */
    });


</script>
        
        <div id="main">
            <div id="navi-bar">
                <ul class="top line">
                <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                    <li <?php if ($sectionId == $current) echo ' class="current"'; ?>><a href="/faq/<?php echo ($sectionId == 'main') ? '' : $sectionId; ?>"><?php echo $sectionName; ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>
            
			<div id="faq-content" class="widget">
				<h2 class="htitle wshadow"><?php echo Text::get('regular-faq') ?></h2>
                
                <ul class="central-content">
                    <?php foreach ($this['faqs'][$current] as $question)  : ?>
                        <li>
                            <a name="q<?php echo $question->id; ?>">&nbsp;</a>
                            <h4><?php echo $question->title; ?></h4>
                            <div><?php echo $question->description; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
<!--                    <a class="up" href="#"><?php echo $go_up; ?></a> -->
                
                <div class="goask"><?php echo Text::html('faq-ask-question'); ?></div>
                
                <!-- lateral flotante, fijo a la altura -->
                <div id="faq-side">
                    <ul class="side-content">
                    <?php foreach ($this['faqs'][$current] as $question)  : ?>
                        <li>
                            <a href="#q<?php echo $question->id; ?>"><?php echo $question->title; ?></a></h4>
                        </li>
                    <?php endforeach; ?>
                        <li>
                            <a href="/contact"><?php echo Text::get('regular-contact_us'); ?>: <span class="undline ct1"><?php echo \CONF_CONTACT_MAIL; ?></span></a></h4>
                        </li>
                    </ul>
                </div>

			</div>
            

            
        </div>
	<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>