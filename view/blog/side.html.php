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

use Base\Library\Text;

$list = array();

switch ($this['type']) {
    case 'posts':
        // enlace a la entrada
        foreach ($this['items'] as $item) {
            $list[] = '<span class="date ct2 fs-XS">'.$item->fecha.'</span>
                    <a href="/blog/'.$item->id.'">'.Text::recorta($item->title, 60).'<span class="ct1"> | '.$item->num_comments.' '.Text::get('blog-coments-header').'</span></a>';
        }
        break;
    case 'tags':
        // enlace a la lista de entradas con filtro tag
        foreach ($this['items'] as $item) {
            if ($item->used > 0) {
                $list[] = '<a href="/blog/?tag='.$item->id.'">'.$item->name.'<span class="ct1"> | '.$item->used.' '.Text::get('blog-posts-header').'</span></a>';
            }
        }
        break;
    case 'collections':
        // enlace a la lista de entradas con filtro tag
        foreach ($this['items'] as $item=>$itemName) {
            $list[] = '<a href="/blog/?collection='.$item.'" class="upcase">'.$itemName.'</a>';
        }
        break;
    case 'comments':
        // enlace a la entrada en la que ha comentado
        foreach ($this['items'] as $item) {
            $list[] = '<span class="date ct2 fs-XS">'.$item->date.'</span>
                    <a class="expand" href="/blog/'.$item->post.'#comment'.$item->id.'">'.Text::recorta($item->text, 60).'<span class="ct1"> | '.$item->user->name.'</span></a>';
            }
        break;
}

if (!empty($list)) : ?>
<div class="side-widget">
    <h3 class="htitle"><?php echo $this['title']; ?></h3>
    <ul class="blog-side-list">
        <?php foreach ($list as $item) : ?>
        <li><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
