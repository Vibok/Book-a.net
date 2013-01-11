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
    Base\Library\Paypal,
    Base\Library\Tpv;

$invest = $this['invest'];
$booka = $this['booka'];
$user = $this['user'];

?>
<a href="/admin/accounts/update/<?php echo $invest->id ?>" class="button std-btn tight menu-btn" onclick="return confirm('Seguro que deseas cambiarle el estado a este aporte?, esto es delicado')" class="button">Cambiarle el estado</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/invests/edit/<?php echo $invest->id ?>" class="button std-btn tight menu-btn" class="button">Gestionar recompensa / dirección</a>
<?php if ($invest->issue) : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/accounts/solve/<?php echo $invest->id ?>" class="button std-btn tight menu-btn" onclick="return confirm('Esta incidencia se dará por resuelta: se va a cancelar el preaproval, el aporte pasará a ser de tipo Cash y en estado Cobrado por goteo, seguimos?')" class="button">Nos han hecho la transferencia</a>
<?php endif; ?>
<div class="widget">
    <p>
        <strong>Booka:</strong> <?php echo $booka->clr_name ?> (<?php echo $this['status'][$booka->status] ?>)
        <strong>Usuario: </strong><?php echo $user->name ?> [<?php echo $user->email ?>]
    </p>
    <p>
        <?php if ($invest->status < 1 || ($invest->method == 'tpv' && $invest->status < 2) ||($invest->method == 'cash' && $invest->status < 2)) : ?>
        <a href="/admin/accounts/cancel/<?php echo $invest->id ?>"
            onclick="return confirm('¿Estás seguro de querer cancelar este aporte y su preapproval?');"
            class="button">Cancelar este aporte</a>&nbsp;&nbsp;&nbsp;
        <?php endif; ?>

        <?php if ($invest->method == 'paypal' && $invest->status == 0) : ?>
        <a href="/admin/accounts/execute/<?php echo $invest->id ?>"
            onclick="return confirm('¿Seguro que quieres ejecutar ahora? ¿No quieres esperar a la ejecución automática al final de la ronda? ?');"
            class="button">Ejecutar cargo ahora</a>
        <?php endif; ?>

        <?php if ($invest->method != 'paypal' && $invest->status == 1) : ?>
        <a href="/admin/accounts/move/<?php echo $invest->id ?>" class="button">Reubicar este aporte</a>
        <?php endif; ?>
    </p>
    
    <h3>Detalles de la transaccion</h3>
    <dl>
        <dt>Cantidad aportada:</dt>
        <dd><?php echo $invest->amount ?> &euro;
            <?php
                if (!empty($invest->campaign))
                    echo 'Campaña: ' . $campaign->name;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Estado:</dt>
        <dd><?php echo $this['investStatus'][$invest->status]; if ($invest->status < 0) echo ' <span style="font-weight:bold; color:red;">OJO! que este aporte no fue confirmado.<span>'; if ($invest->issue) echo ' <span style="font-weight:bold; color:red;">INCIDENCIA!<span>'; ?></dd>
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
                if (!empty($invest->anonymous))
                    echo '<br />Aporte anónimo';

                if (!empty($invest->resign))
                    echo "<br />Donativo de: {$invest->address->name} [{$invest->address->nif}]";

                if (!empty($invest->admin))
                    echo '<br />Manual generado por admin: '.$invest->admin;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Código de seguimiento:</dt>
        <dd><?php echo 'Cargo: '.$invest->payment; ?></dd>
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
            <?php echo $invest->address->address; ?>,
            <?php echo $invest->address->location; ?>,
            <?php echo $invest->address->zipcode; ?>
            <?php echo $invest->address->country; ?>
        </dd>
    </dl>

    <?php if ($invest->method == 'paypal') : ?>
        <?php if (!isset($_GET['full'])) : ?>
        <p>
            <a href="/admin/accounts/details/<?php echo $invest->id; ?>/?full=show">Mostrar detalles técnicos</a>
        </p>
        <?php endif; ?>

        <?php /* if (!empty($invest->transaction)) : ?>
        <dl>
            <dt><strong>Detalles de la devolución:</strong></dt>
            <dd>Hay que ir al panel de paypal para ver los detalles de una devolución</dd>
        </dl>
        <?php endif */ ?>
    <?php elseif ($invest->method == 'tpv') : ?>
        <p>Hay que ir al panel del banco para ver los detalles de los aportes mediante TPV.</p>
    <?php else : ?>
        <p>No hay nada que hacer con los aportes manuales.</p>
    <?php endif ?>

</div>

<div class="widget">
    <h3>Log</h3>
    <?php foreach (\Base\Model\Invest::getDetails($invest->id) as $log)  {
        echo "{$log->date} : {$log->log} ({$log->type})<br />";
    } ?>
</div>

<?php if (isset($_GET['full']) && $_GET['full'] == 'show') : ?>
<div class="widget">
    <h3>Detalles técnicos de la transaccion</h3>
    <?php Paypal::getDetails($invest); ?>
</div>
<?php endif; ?>

