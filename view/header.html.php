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

use Base\Core\ACL,
    Base\Library\Text,
    Base\Library\Lang;

$is_admin = ACL::check('/admin/access');
?>

<?php if (!empty($_SESSION['user'])): ?>            
    <ul id="top-mybooka">
        <li><a href="/user/<?php echo $_SESSION['user']->id; ?>" class="top-mybooka-myname"><?php echo $_SESSION['user']->name; ?></a></li>
        <li><a href="/dashboard"><?php echo Text::get('top-menu-myedit'); ?></a></li>
        <?php if ($is_admin) : ?>
        <li><a href="/admin"><?php echo Text::get('regular-admin_board'); ?></a></li>
        <?php endif; ?>
        <li><a href="/user/logout"><?php echo Text::get('regular-logout'); ?></a></li>
    </ul>
    <script type="text/javascript">
    jQuery(document).ready(function ($) {
         $("#login").hover(function(){
           //desplegar idiomas
           try{clearTimeout(TID_LANG)}catch(e){};
           var pos = $(this).offset().left + $(this).width() - $("#top-mybooka").width();
/*           alert('pos: ' + pos + '= left:' + $(this).offset().left + ' this-width: ' + $(this).width() + ' menu-width:' + $("#top-mybooka").width()); */
/*           var pos = $(this).offset().left - $("#top-mybooka").width() + 20; */
/*           var pos = $(this).offset().left; */
           $("#top-mybooka").css({left:pos+'px'});
           $("#top-mybooka").fadeIn();

       },function() {
           TID_LANG = setTimeout('$("#top-mybooka").hide()',100);
        });
        $('#top-mybooka').hover(function(){
            try{clearTimeout(TID_LANG)}catch(e){};
        },function() {
           TID_LANG = setTimeout('$("#top-mybooka").hide()',100);
        });


    });
    </script>                                  
<?php endif; ?>            


<div id="header">
    <div id="super-header">
        <ul class="top-menu line">
            <li><a href="/about"><?php echo Text::get('booka-about-header') ?></a></li>
            <li><a href="/collection"><?php echo Text::get('booka-collections-header') ?></a></li>
            <li><a href="/community"><?php echo Text::get('booka-community-header') ?></a></li>
            <li><a href="/blog"><?php echo Text::get('booka-blog-header') ?></a></li>
            <li><a href="/faq"><?php echo Text::get('booka-faq-header') ?></a></li>  
            <li id="lang">
                <?php foreach (Lang::$langs as $langId=>$langName) : 
                    $curLang = ($langId == LANG) ? 'lang-current' : 'lang-other';
                    ?>
                    <a href="?lang=<?php echo $langId ?>" class="<?php echo $curLang; ?>"><?php echo $langName; ?></a>
                <?php endforeach; ?>
            </li>
            <li id="login">
            <?php if (!empty($_SESSION['user'])): ?>            
                <a href="/dashboard" class="top-login"><?php echo Text::get('top-menu-mybooka'); ?></a>
            <?php else: ?>            
                <a href="/user/login" class="top-login"><?php echo Text::get('regular-login'); ?></a>
            <?php endif; ?>
            </li>
        </ul>
    </div>
</div>

<div id="header-logo">
<?php if ($bodyClass == 'blog') : ?>
    <h1><a href="<?php echo empty($home) ? '/' : $home ;?>" title="<?php echo Text::get('logo-booka'); ?>"><img src="/view/css/assets/logo_blog.jpg" alt="el blog de book-a" title="el blog de booka" /></a></h1>
<?php else: ?>
    <h1><a href="/" title="<?php echo Text::get('logo-booka'); ?>">book-a</a></h1>
<?php endif; ?>

    <div id="quick-search">
        <form action="/search" method="post">
            <label for="search_query" class="ct1 ft3 fs-S"><?php echo Text::get('regular-search'); ?> </label>
            <input type="text" name="query" id="search_query" value="<?php echo $_SESSION['current_search'] ?>" class="fs-S"/>
            <button type="submit" class="go-btn">&GT;</button>
        </form>
    </div>
</div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>


