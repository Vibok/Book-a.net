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
    Base\Library\Navi;

$booka = $this['booka'];

// paginación
$pg = Navi::calcPages(count($booka->messages), $_GET['page']);
?>
<div class="center-widget">
    
<?php if (!empty($booka->messages)): ?>
    
    <?php echo Navi::pageHtml(array('span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], 'comentarios'), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
    
    <?php $c = 0;
    foreach ($booka->messages as $comment) : 
        $c++;
        if ($c < $pg['from'] || $c > $pg['to']) continue;
        ?>
        <div class="comment<?php if ($comment->user->id == $booka->owner) echo ' author'; ?>">
            <a name="comment<?php echo $comment->id; ?>" >&nbsp;</a>
            <div class="avatar">
                <a href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank">
                    <img src="<?php echo $comment->user->avatar->getLink(60, 60, true); ?>" alt="<?php echo $comment->user->name; ?>" />
                </a>
            </div>
            <div class="content">
                <a class="user ft2 ct1 bloque" href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank"><?php echo htmlspecialchars($comment->user->name) ?></a>
                <span class="date ct2 wshadow bloque" ><?php echo Text::get('feed-timeago', $comment->timeago) ?></span>
                <div class="text fs-M"><?php echo $comment->text; ?></div>
            </div>
            <?php if ($c != $pg['to']) : ?>
            <div class="clear bottom dashed"></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <?php echo Navi::pageHtml(array('footer' => true, 'span' => Text::get('showing_results', $pg['from'], $pg['to'], $pg['of'], 'comentarios'), 'go' => $pg['next'], 'back' => $pg['prev'])); ?>
    
<?php else : ?>

    <p><?php echo Text::get('blog-comments_no_comments'); ?></p>

<?php endif; ?>
</div>
