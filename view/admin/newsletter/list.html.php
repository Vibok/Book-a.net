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
    Base\Library\Template;

$mailing = $this['mailing'];

$link = SITE_URL.'/mail/'.base64_encode(md5(uniqid()).'¬any¬'.$mailing->mail).'/?email=any';

// si el mailing está desactivado , mostrar mensaje y botón para iniciar de nuevo
?>
<?php if (empty($mailing) || !$mailing->active) :
    $template = Template::get(33);
?>
<div class="widget board">
    <p>No se está enviando ningún boletín actualmente. Confirmar el asunto y pulsar el botón para generar uno nuevo con los datos actuales de plantilla y portada.</p>
    <form action="/admin/newsletter/init" method="post">
        <label>Asunto: <input type="text" name="subject" value="<?php echo $template->title ?>" style="width:300px" /></label><br />
        <label>Es una prueba: <input type="checkbox" name="test" value="1" /></label><br />
        
        <input type="submit" name="init" value="Iniciar" />
    </form>
</div>
<?php endif; ?>
<?php if (!empty($mailing->id)) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?php echo $mailing->subject ?></strong><br />
           Iniciado el: <strong><?php echo $mailing->date ?></strong><br />
           Estado del envío automático: <?php echo ($mailing->active) 
               ? '<span style="color:green;font-weight:bold;">Activo</span>'
               : '<span style="color:red;font-weight:bold;">Inactivo</span>' ?>
        </p>

    <table>
        <thead>
            <tr>
                <th><!-- Si no ves --></th>
                <th>Fecha</th>
                <th><a href="/admin/newsletter/detail/receivers" title="Ver todos los destinatarios">Destinatarios</a></th>
                <th><a href="/admin/newsletter/detail/sended" title="Ver todos los enviados">Enviados</a></th>
                <th><a href="/admin/newsletter/detail/failed" title="Ver todos los fallidos">Fallidos</a></th>
                <th><a href="/admin/newsletter/detail/pending" title="Ver todos los pendientes">Pendientes</a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?php echo $link; ?>" target="_blank">[Si no ves]</a></td>
                <td><?php echo $mailing->date; ?></td>
                <td style="width:15%"><?php echo $mailing->receivers; ?></td>
                <td style="width:15%"><?php echo $mailing->sended; ?></td>
                <td style="width:15%"><?php echo $mailing->failed; ?></td>
                <td style="width:15%"><?php echo $mailing->pending; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No hay ningún envío de newsletter registrado, ni activo ni inactivo</p>
<?php endif; ?>