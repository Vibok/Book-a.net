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
    Base\Model,
    Base\Core\Redirection,
    Base\Library\Advice;

$booka = $this['booka'];

if (!$booka instanceof Model\Booka) {
    Advice::Error('Instancia de booka corrupta');
    throw new Redirection('/admin/bookas');
}

$elements = array(
    'created' => array(
        'type'      => 'datebox',
        'title'     => 'Creación',
        'value'     => !empty($booka->created) ? $booka->created : null
    ),
    'updated' => array(
        'type'      => 'datebox',
        'title'     => 'Inicio revisión',
        'subtitle'  => '(en caso de bookas creados por un colaborador)',
        'value'     => !empty($booka->updated) ? $booka->updated : null
    ),
    'published' => array(
        'type'      => 'datebox',
        'title'     => 'Despegue',
        'value'     => !empty($booka->published) ? $booka->published : null
    ),
    'closed' => array(
        'type'      => 'datebox',
        'title'     => 'Cierre',
        'subtitle'  => '(marca la fecha de cierre de la campaña de financiacion)',
        'value'     => !empty($booka->closed) ? $booka->closed : null
    ),
    'success' => array(
        'type'      => 'datebox',
        'title'     => 'Producción',
        'subtitle'  => '(marca la fecha de producción del booka)',
        'value'     => !empty($booka->success) ? $booka->success : null
    )

);
?>
<div class="widget">
    <form method="post" action="/admin/bookas" >
        <input type="hidden" name="id" value="<?php echo $booka->id ?>" />

<?php foreach ($elements as $id=>$element) : ?>
    <div id="<?php echo $id ?>">
        <h4><?php echo $element['title'] ?>:</h4>
        <?php echo new View('library/superform/view/element/datebox.html.php', array('value'=>$element['value'], 'id'=>$id, 'name'=>$id)); ?>
        <?php if (!empty($element['subtitle'])) echo $element['subtitle'].'<br />'; ?>
    </div>
        <br />
<?php endforeach; ?>

        <input type="submit" name="save-dates" value="Guardar" />

    </form>
</div>
