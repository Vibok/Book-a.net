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
    Base\Model\Post;

$post = $this['post'];
?>
<?php if (!empty($post->comments)): ?>

    <?php foreach ($post->comments as $comment) : ?>
        <div class="comment<?php if ($comment->user->id == $post->user->id) echo ' author'; ?>">
            <a name="comment<?php echo $comment->id; ?>" ></a>
            <div class="avatar">
                <a href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank">
                    <img src="<?php echo $comment->user->avatar->getLink(60, 60, true); ?>" alt="<?php echo $comment->user->name; ?>" />
                </a>
            </div>
            <div class="content">
                <a class="user ft2 ct1 bloque" href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank"><?php echo htmlspecialchars($comment->user->name) ?></a>
                <span class="date ct2 wshadow bloque" ><?php echo Text::get('feed-timeago', $comment->timeago) ?></span>
                <div class="text fs-S"><?php echo $comment->text; ?></div>
            </div>
            <div class="clear bottom dashed"></div>
        </div>
    <?php endforeach; ?>

<?php else : ?>

    <p><?php echo Text::get('blog-comments_no_comments'); ?></p>

<?php endif; ?>