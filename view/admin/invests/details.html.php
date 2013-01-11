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

$invest = $this['invest'];
$booka = $this['booka'];
$calls = $this['calls'];
$droped = $this['droped'];
$user = $this['user'];

$rewards = $invest->rewards;
array_walk($rewards, function (&$reward) { $reward = $reward->reward; });

?>
<div class="widget">
    <p>
        <strong>Booka:</strong> <?php echo $booka->name ?> (<?php echo $this['status'][$booka->status] ?>)
        <strong>Usuario: </strong><?php echo $user->name ?>
    </p>

    <h3>Detalles del aporte</h3>
    <dl>
        <dt>Cantidad aportada:</dt>
        <dd><?php echo $invest->amount ?> &euro;</dd>
    </dl>
    
    <dl>
        <dt>Estado:</dt>
        <dd><?php echo $this['investStatus'][$invest->status]; if ($invest->status < 0) echo ' <span style="font-weight:bold; color:red;">OJO! que este aporte no fue confirmado.<span>';  ?></dd>
    </dl>

    <dl>
        <dt>Fecha del aporte:</dt>
        <dd><?php echo $invest->invested . '  '; ?>
            <?php
                if (!empty($invest->charged))
                    echo 'Cargo ejecutado el: ' . $invest->charged;

                if (!empty($invest->returned))
                    echo 'Dinero devuelto el: ' . $invest->returned;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Método de pago:</dt>
        <dd><?php echo $invest->method . '   '; ?>
            <?php
                if (!empty($invest->campaign))
                    echo '<br />Capital riego';

                if (!empty($invest->anonymous))
                    echo '<br />Aporte anónimo';

                if (!empty($invest->resign))
                    echo "<br />Renuncia a recompensa";

                if (!empty($invest->admin))
                    echo '<br />Manual generado por admin: '.$invest->admin;
            ?>
        </dd>
    </dl>

    <?php if (!empty($invest->rewards)) : ?>
    <dl>
        <dt>Recompensas elegidas:</dt>
        <dd>
            <?php echo implode(', ', $rewards); ?>
        </dd>
    </dl>
    <?php endif; ?>

    <dl>
        <dt>Dirección:</dt>
        <dd>
            <?php echo Text::get('invest-address-name-field') .': '. $invest->address->name; ?><br />
            <?php echo Text::get('invest-address-nif-field') .': '. $invest->address->nif; ?><br />
            <?php echo Text::get('address-address-field') .': '. $invest->address->address; ?><br />
            <?php echo Text::get('address-location-field') .': '. $invest->address->location; ?><br />
            <?php echo Text::get('address-city-field') .': '. $invest->address->city; ?><br />
            <?php echo Text::get('address-zipcode-field') .': '. $invest->address->zipcode; ?><br />
            <?php echo Text::get('address-country-field') .': '. $invest->address->country; ?><br />
        </dd>
    </dl>

    <a href="/admin/invests/edit/<?php echo $invest->id ?>">Modificar importe, recompensas o datos</a>
</div>