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

$data = $this['data'];

$filters = $_SESSION['mailing']['filters'];
$receivers = $_SESSION['mailing']['receivers'];
$users = $this['users'];

// contamos los correctos y los fallos
$oks = array();
$fails = array();
$new_receivers = array();
foreach ($users as $usr) {
    if ($receivers[$usr]->ok) {
        $oks[] = $usr;
    } else {
        $fails[] = $usr;
        $new_receivers[$usr] = $_SESSION['mailing']['receivers'][$usr];
    }
}
$_SESSION['mailing']['receivers'] = $new_receivers;

?>
<div class="widget">
    <?php if (!empty($this['time']) ) : ?><p>El envio se ha procesado en <?php echo $this['time']; ?> milisegundos</p><?php endif ?>
    <p><?php echo 'Buscábamos comunicarnos con ' . $_SESSION['mailing']['filters_txt']; ?> </p>
    <p>De los <?php echo count($users) ?> destinatarios, se ha enviado correctamente a <?php echo count($oks) ?> y han fallado <?php echo count($fails) ?> envios.</p>
    <p>El último "si no ves" ha sido: <a href="<?php echo $_SESSION['MAILING_TOKEN'] ?>" target="_blank"><?php echo $_SESSION['MAILING_TOKEN'] ?></a></p>
    <?php if (!empty($fails) ) : ?>
    <p>Han fallado los siguientes:</p>
        <blockquote><?php foreach ($fails as $usrId) {
                echo '<strong>' .$receivers[$usrId]->name . '</strong> ('.$receivers[$usrId]->id.') al mail <strong>' . $receivers[$usrId]->email . '</strong><br />';
        } ?></blockquote>
    <p>Puedes cargarlos como nueva lista de destinatarios pulsando <a href="/admin/mailing/edit/?recover=receivers">este enlace</a></p>
    <?php endif; ?>
</div>

