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
    Base\Core\ACL;

$status = $this['status'];

// paginacion
require_once 'library/pagination/pagination.php';

$filters = $this['filters'];
if (empty($filters['show'])) $filters['show'] = 'all';
$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$the_posts = array();
foreach ($this['posts'] as $apost) {
    $the_posts[] = $apost;
}
$pagedResults = new \Paginated($the_posts, 10, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/blog/add" class="button std-btn tight menu-btn">Nueva entrada</a>
<?php if (isset($_SESSION['user']->roles['superadmin']) || isset($_SESSION['user']->roles['admin'])) : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/home" class="button std-btn tight menu-btn">Ordenar Home</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/footer" class="button std-btn tight menu-btn">Ordenar Footer</a>
<!-- &nbsp;&nbsp;&nbsp;
<a href="/admin/blog/top" class="button">Ordenar Top</a>
-->
<?php endif; ?>

<div class="widget board">
    <form id="filter-form" action="/admin/blog" method="get">

        <label for="show-filter">Mostrar:</label><br />
        <select id="show-filter" name="show" onchange="document.getElementById('filter-form').submit();">
        <?php foreach ($this['show'] as $itemId=>$itemName) : ?>
            <option value="<?php echo $itemId; ?>"<?php if ($filters['show'] == $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
        <?php endforeach; ?>
        </select>
        
    </form>
    <p>Leyenda estilos: <span style="font-weight:bold;">Home</span> | <span style="font-style:italic;">Footer</span> <!-- / <span style="text-decoration: underline;">Top</span> --><p>
</div>

<?php if (!empty($this['posts'])) : ?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- published --></th>
                <th colspan="6">Entrada</th> <!-- title -->
                <th>Autor</th>
                <th>Booka</th>
                <th>Fecha</th> <!-- date -->
            </tr>
        </thead>

        <tbody>
            <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td><?php if ($post->publish) echo '<strong style="color:#20b2b3;font-size:10px;">Publicada</sttrong>'; ?></td>
                <td colspan="6"><?php
                        $style = '';
                        if (isset($this['tops'][$post->id]))
                            $style .= ' text-decoration: underline;';
                        if (isset($this['homes'][$post->id]))
                            $style .= ' font-weight:bold;';
                        if (isset($this['footers'][$post->id]))
                            $style .= ' font-style:italic;';
                            
                      echo "<span style=\"{$style}\">{$post->title}</span>";
                ?></td>
                <td><?php echo $post->user->name; ?></td>
                <td><?php if (!empty($post->booka)) echo substr($post->bookaData->name, 0, 30) . ' ('.$status[$post->bookaData->status].')'; ?></td>
                <td><?php echo date('d-m-Y', strtotime($post->date)); ?></td>
            </tr>
            <tr>
                <td><a href="/blog/<?php echo $post->id; ?>?preview=<?php echo $_SESSION['user']->id ?>" target="_blank" title="Previsualizar contenido" class="tipsy">[Ver]</a></td>
        <?php if (isset($_SESSION['user']->roles['director']) && $post->author != $_SESSION['user']->id) : ?>                
                <td></td>
        <?php else : ?>
                <td><a href="/admin/blog/edit/<?php echo $post->id; ?>">[Editar]</a></td>
        <?php endif; ?>
        <?php if (isset($_SESSION['user']->roles['superadmin']) || isset($_SESSION['user']->roles['admin'])) : ?>                
                <td><?php if (isset($this['homes'][$post->id])) {
                        echo '<a href="/admin/blog/remove_home/'.$post->id.'" style="color:red;" title="Quitar de la portada" class="tipsy">Home (X)</a>';
                    } elseif ($post->publish) {
                        echo '<a href="/admin/blog/add_home/'.$post->id.'" style="color:blue;" title="Poner en la portada" class="tipsy">[Home]</a>';
                    } ?></td>
                <td><?php if (isset($this['footers'][$post->id])) {
                            echo '<a href="/admin/blog/remove_footer/'.$post->id.'" style="color:red;" title="Quitar del pie" class="tipsy">Footer (X)</a>';
                        } elseif ($post->publish) {
                            echo '<a href="/admin/blog/add_footer/'.$post->id.'" style="color:blue;" title="Poner en el pie" class="tipsy">[Footer]</a>';
                        } ?></td>
                <td><?php /* if (isset($this['tops'][$post->id])) {
                            echo '<a href="/admin/blog/remove_top/'.$post->id.'" style="color:red;">Top (X)</a>';
                        } elseif ($post->publish) {
                            echo '<a href="/admin/blog/add_top/'.$post->id.'" style="color:blue;">[Top]</a>';
                        } */ ?></td>
                <td><a href="/admin/blog/remove/<?php echo $post->id; ?>" onclick="return confirm('Seguro que deseas eliminar esta entrada?');">[Eliminar]</a></td>
        <?php else : ?>
                <td colspan="3"></td>
        <?php endif; ?>
                <td></td>
            </tr>
            <tr>
                <td colspan="10"><hr /></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<ul id="pagination" style="margin-bottom: 10px; padding-left: 150px;">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $the_filters)); ?>
</ul>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>