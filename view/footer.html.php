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
    Base\Model\Post,
    Base\Model\Footer;

    $cols = Footer::getList();
    $posts = Post::getAll(array('show'=>'footer'), true);
    $about = Footer::getAll(array('column'=>'about'));
    $faq = Footer::getAll(array('column'=>'faq'));
?>
<script type="text/javascript">
    $(function(){
        if (document.location.hash == '#footer') {
            $("#suscribe_mail").focus();
        }
    });


</script>
    <div id="footer">
        <a name="footer"></a>
        <div class="w940">
            <div class="block news">
                <h8 class="title"><?php echo $cols['news']; ?></h8>
                <ul>
                    <?php foreach ($posts as $post) : ?>
                    <li><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="block about">
                <h8 class="title"><?php echo $cols['about']; ?></h8>
                <ul>
                    <?php foreach ($about as $item) : ?>
                    <li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="block faq">
                <h8 class="title"><?php echo $cols['faq']; ?></h8>
                <ul>
                    <?php foreach ($faq as $item) : ?>
                    <li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="block logos">
                <h8 class="title"><?php echo Text::get('footer-header-suscribe'); ?></h8>
                <form action="/newsletter/suscribe" method="post" onsubmit="if (document.getElementById('suscribe_mail').value == '') return false;">
                    <input type="text" id="suscribe_mail" name="email" value="" placeholder="<?php echo Text::get('footer-header-suscribe'); ?>"/>
                    <button type="submit" class="go-btn">&GT;</button>
                </form>
                
                <div class="social">
                    <ul>
                        <li><span class="ft3"><?php echo Text::get('booka-social-header') ?></span>
                            <a href="<?php echo Text::get('social-account-facebook') ?>" class="social-icon facebook" target="_blank">f</a>
                            <a href="<?php echo Text::get('social-account-flickr') ?>" class="social-icon flickr" target="_blank">&#8734;</a>
                            <a href="<?php echo Text::get('social-account-vimeo') ?>" class="social-icon vimeo" target="_blank">v</a>
                            <a href="<?php echo Text::get('social-account-twitter') ?>" class="social-icon twitter" target="_blank">t</a>
                        </li>
                    </ul>
                </div>
                
                <script type="text/javascript">
                    $(function(){
                        $('#slides_sponsor').slides({
                            container: 'slides_container',
                            paginationClass: 'slide-ctrl',
                            generatePagination: false,
                            effect: 'fade', 
                            crossfade: false,
                            fadeSpeed: 350,
                            play: 2500, 
                            pause: 1
                        });
                    });
                </script>
               <div id="slides_sponsor" style="height: 60px;">
                    <div class="slides_container" style="height: 47px;">
                        <div>
                            <img src="/view/css/assets/logo_mcu.jpg" alt="Ministerio de educación, cultura y deporte" title="Gobierno de España - Ministerio de educación, cultura y deporte - Secretaría de estado de cultura" />
                        </div>
                        <div>
                            <a href="http://goteo.org" target="_blank"><img src="/view/css/assets/logo_goteo.jpg" alt="Goteo.org" title="Plataforma de crowdfunding - Goteo" /></a>
                        </div>
                        <div>
                            <a href="http://www.vibokworks.com" target="_blank"><img src="/view/css/assets/logo_vibokworks.jpg" alt="Vibok Works" title="Vibok Works" /></a>
                        </div>
                        <div>
                            <a href="http://arqa.com" target="_blank"><img src="/view/css/assets/logo_arqa.jpg" alt="Arqua" title="Arqua" /></a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <div id="sub-footer">
        <ul class="line">
            <li class="logo-vibok"><a href="http://www.vibokworks.com/"><img src="/view/css/assets/logo_vibok.png" alt="(vw) vibok works" title="Vibok Works" /></a></li>
            <li><a class="ft3 fs-XS" href="/legal/terms"><?php echo Text::get('legal-terms') ?></a></li>
            <li><a class="ft3 fs-XS" href="/legal/privacy"><?php echo Text::get('legal-privacy') ?></a></li>
            <li class="ft3 fs-XS">Copyright 03INNOVA24H SLU</li>
            <li class="ft3 fs-XS"><?php echo Text::get('legal-license') ?></li>
        </ul>
    </div>
