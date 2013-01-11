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
    Base\Core\View;

$booka = $this['booka'];
$Data = $this['Data'];

$desglose = array();
$autor    = array();
$benefit = array();
$estado   = array();
$usuario  = array();

$users = array();
foreach ($this['users'] as $user) {
    $amount = $users[$user->user]->amount + $user->amount;
    $users[$user->user] = (object) array(
        'name'   => $user->name,
        'user'   => $user->user,
        'amount' => $amount
    );
}

uasort($this['users'],
    function ($a, $b) {
        if ($a->name == $b->name) return 0;
        return ($a->name > $b->name) ? 1 : -1;
        }
    );

// recorremos los aportes
foreach ($this['invests'] as $invest) {

// para cada metodo acumulamos desglose, comision * 0.08, pago * 0.092
    $desglose[$invest->method] += $invest->amount;
    $autor[$invest->method] += ($invest->amount * 0.08);
    $benefit[$invest->method] += ($invest->amount * 0.92);
// para cada estado
    $estado[$invest->status]['total'] += $invest->amount;
    $estado[$invest->status][$invest->method] += $invest->amount;
// para cada usuario
    $usuario[$invest->user->id]['total'] += $invest->amount;
    $usuario[$invest->user->id][$invest->method] += $invest->amount;
// por metodo
    $usuario[$invest->method]['users'][$invest->user->id] = 1;
    $usuario[$invest->method]['invests']++;

}

?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <p>Informe de financiación de <strong><?php echo $booka->clr_name ?></strong> al d&iacute;a <?php echo date('d-m-Y') ?></p>
    <p>Se encuentra en la etapa <strong><?php echo $booka->stageData ?></strong>.</p>
    <p>Este libro-semilla lleva conseguidos <strong> <?php echo \amount_format($booka->invested) ?> &euro;</strong> de <strong><?php echo \amount_format($booka->cost) ?> &euro;</strong>, esto es un <strong><?php echo $booka->percent . '%' ?></strong> del total.</p>

    <h3>Informe de aportes</h3>
    <p style="font-style:italic;">Cantidades en bruto (no se tiene en cuenta ejecuciones fallidas ni comisiones PayPal ni TPV)</p>

    <h4>Por destinatario</h4>
    <table>
        <tr>
            <th>M&eacute;todo</th>
            <th>Cantidad</th>
            <th>Autor</th>
            <th>Booka</th>
        </tr>
        <tr>
            <td>PayPal</td>
            <td style="text-align:right;"><?php echo \amount_format($desglose['paypal']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($autor['paypal'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($benefit['paypal'], 2) ?></td>
        </tr>
        <tr>
            <td>Tpv</td>
            <td style="text-align:right;"><?php echo \amount_format($desglose['tpv']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($autor['tpv'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($benefit['tpv'], 2) ?></td>
        </tr>
        <tr>
            <td>Cash</td>
            <td style="text-align:right;"><?php echo \amount_format($desglose['cash']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($autor['cash'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($benefit['cash'], 2) ?></td>
        </tr>
        <tr>
            <td>TOTAL</td>
            <td style="text-align:right;"><?php echo \amount_format(($desglose['paypal'] + $desglose['tpv'] + $desglose['cash']), 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format(($autor['paypal'] + $autor['tpv'] + $autor['cash']), 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format(($benefit['paypal'] + $benefit['tpv'] + $benefit['cash']), 2) ?></td>
        </tr>
    </table>

    <h3>Por estado</h3>
    <table>
        <tr>
            <th>Estado</th>
            <th>Cantidad</th>
            <th>PayPal</th>
            <th>Tpv</th>
            <th>Cash</th>
        </tr>
        <?php foreach ($this['investStatus'] as $id=>$label) : if (in_array($id, array('-1'))) continue;?>
        <tr>
            <td><?php echo $label ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['total']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['paypal']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['tpv']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['cash']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Por cofinanciadores (<?php echo count($this['users']) ?>)</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Cantidad</th>
            <th>PayPal</th>
            <th>Tpv</th>
            <th>Cash</th>
        </tr>
        <?php foreach ($this['users'] as $user) : ?>
        <tr>
            <td><?php echo $user->name ?></td>
            <td style="text-align:right;"><?php echo \amount_format($user->amount, 0) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($usuario[$user->user]['paypal']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($usuario[$user->user]['tpv']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($usuario[$user->user]['cash']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- resumen financiero booka -->
    <a name="detail">&nbsp;</a>
    <?php echo new View('view/admin/bookas/report.html.php', array('booka'=>$booka, 'reportData'=>$Data, 'admin'=>true)); ?>
    <hr>
    
<div class="widget">
<!-- información detallada apra tratar transferencias a bookas -->
    <h3 class="title">Desglose de financiación por rondas</h3>
    <p style="font-style:italic;">Las incidencias NO se tienen en cuenta en el conteo de usuarios/operaciones ni en importes ni en comisiones ni en netos.</p>

<?php if (!empty($Data['tpv'])) : ?>
    <h4>TPV</h4>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['tpv']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['tpv']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['tpv']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['tpv']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['tpv']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['tpv']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['total']['amount']) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['fee']  = $Data['tpv']['first']['amount']  * 0.008;
            $Data['tpv']['second']['fee'] = $Data['tpv']['second']['amount'] * 0.008;
            $Data['tpv']['total']['fee']  = $Data['tpv']['total']['amount']  * 0.008;
            ?>
            <th>Comisi&oacute;n</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['first']['fee'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['second']['fee'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['total']['fee'], 2) ?></td>
            <td>banco 0,80&#37; de cada operaci&oacute;n</td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['net']  = $Data['tpv']['first']['amount']  - $Data['tpv']['first']['fee'];
            $Data['tpv']['second']['net'] = $Data['tpv']['second']['amount'] - $Data['tpv']['second']['fee'];
            $Data['tpv']['total']['net']  = $Data['tpv']['total']['amount']  - $Data['tpv']['total']['fee'];
            ?>
            <th>Neto</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['first']['net'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['second']['net'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['total']['net'], 2) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['autor']  = $Data['tpv']['first']['net']  * 0.08;
            $Data['tpv']['second']['autor'] = $Data['tpv']['second']['net'] * 0.08;
            $Data['tpv']['total']['autor']  = $Data['tpv']['total']['net']  * 0.08;
            ?>
            <th>Autor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['first']['autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['second']['autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['total']['autor'], 2) ?></td>
            <td>8&#37; del neto</td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['project']  = $Data['tpv']['first']['net']  - $Data['tpv']['first']['autor'];
            $Data['tpv']['second']['project'] = $Data['tpv']['second']['net'] - $Data['tpv']['second']['autor'];
            $Data['tpv']['total']['project']  = $Data['tpv']['total']['net']  - $Data['tpv']['total']['autor'];
            ?>
            <th>Booka</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['first']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['second']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['tpv']['total']['project'], 2) ?></td>
            <td>92&#37; del neto</td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['paypal'])) : ?>
    <h4>PayPal</h4>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['paypal']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['paypal']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['paypal']['total']['users']) ?></td>
            <td>Sin incidencias</td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['paypal']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['paypal']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['paypal']['total']['invests'] ?></td>
            <td>Sin incidencias</td>
        </tr>
        <tr>
            <th>Importe Incidencias</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['fail']) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['ok']  = $Data['paypal']['first']['amount'];
            $Data['paypal']['second']['ok'] = $Data['paypal']['second']['amount'];
            $Data['paypal']['total']['ok']  = $Data['paypal']['total']['amount'];
            ?>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['ok']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['ok']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['ok']) ?></td>
            <td>Preapprovals ejecutados correctamente</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['autor']  = $Data['paypal']['first']['ok'] * 0.08;
            $Data['paypal']['second']['autor'] = $Data['paypal']['second']['ok'] * 0.08;
            $Data['paypal']['total']['autor']  = $Data['paypal']['total']['ok'] * 0.08;
            ?>
            <th>Autor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['autor'], 2) ?></td>
            <td>8&#37; de las operaciones correctas</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['project']  = $Data['paypal']['first']['ok']  - $Data['paypal']['first']['autor'];
            $Data['paypal']['second']['project'] = $Data['paypal']['second']['ok'] - $Data['paypal']['second']['autor'];
            $Data['paypal']['total']['project']  = $Data['paypal']['total']['ok']  - $Data['paypal']['total']['autor'];
            ?>
            <th>Booka</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['project'], 2) ?></td>
            <td>92&#37; de las operaciones correctas</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['fee_autor']  = ($Data['paypal']['first']['invests'] * 0.35) + ($Data['paypal']['first']['autor'] * 0.034);
            $Data['paypal']['second']['fee_autor'] = ($Data['paypal']['second']['invests'] * 0.35) + ($Data['paypal']['second']['autor'] * 0.034);
            $Data['paypal']['total']['fee_autor']  = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['autor'] * 0.034);
            ?>
            <th>Fee a Autor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['fee_autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['fee_autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['fee_autor'], 2) ?></td>
            <td>0,35 por operacion + 3,4&#37; del importe de autor (8&#37; del correcto)</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['fee_project']  = ($Data['paypal']['first']['invests'] * 0.35) + ($Data['paypal']['first']['project'] * 0.034);
            $Data['paypal']['second']['fee_project'] = ($Data['paypal']['second']['invests'] * 0.35) + ($Data['paypal']['second']['project'] * 0.034);
            $Data['paypal']['total']['fee_project']  = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['project'] * 0.034);
            ?>
            <th>Fee al Promotor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['fee_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['fee_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['fee_project'], 2) ?></td>
            <td>0,35 por operacion + 3,4&#37; del importe del booka (92&#37; del correcto)</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['net_autor']  = $Data['paypal']['first']['autor']  - $Data['paypal']['first']['fee_autor'];
            $Data['paypal']['second']['net_autor'] = $Data['paypal']['second']['autor'] - $Data['paypal']['second']['fee_autor'];
            $Data['paypal']['total']['net_autor']  = $Data['paypal']['total']['autor']  - $Data['paypal']['total']['fee_autor'];
            ?>
            <th>Neto Autor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['net_autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['net_autor'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['net_autor'], 2) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['net_project']  = $Data['paypal']['first']['project']  - $Data['paypal']['first']['fee_project'];
            $Data['paypal']['second']['net_project'] = $Data['paypal']['second']['project'] - $Data['paypal']['second']['fee_project'];
            $Data['paypal']['total']['net_project']  = $Data['paypal']['total']['project']  - $Data['paypal']['total']['fee_project'];
            ?>
            <th>Neto Booka</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['first']['net_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['second']['net_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['paypal']['total']['net_project'], 2) ?></td>
            <td></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['cash'])) : ?>
    <h4>CASH</h4>
    <?php
        $users_ok = count($usuarios['cash']['users']);
        $invests_ok = $usuarios['cash']['invests'];
        $incidencias = 0;
        $correcto = $desglose['cash'] - $incidencias;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['cash']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['cash']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Incidencias</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['fail']) ?></td>
            <td>Aportes mediante PayPal, TPV o de Capital Riego activos</td>
        </tr>
        <tr>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['amount']) ?></td>
            <td>Aportes de cash</td>
        </tr>
        <tr>
            <?php
            $Data['cash']['first']['autor']  = $Data['cash']['first']['amount'] * 0.08;
            $Data['cash']['second']['autor'] = $Data['cash']['second']['amount'] * 0.08;
            $Data['cash']['total']['autor']  = $Data['cash']['total']['amount'] * 0.08;
            ?>
            <th>Autor</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['autor']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['autor']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['autor']) ?></td>
            <td>8&#37; del importe</td>
        </tr>
        <tr>
            <?php
            $Data['cash']['first']['project']  = $Data['cash']['first']['amount']  - $Data['cash']['first']['autor'];
            $Data['cash']['second']['project'] = $Data['cash']['second']['amount'] - $Data['cash']['second']['autor'];
            $Data['cash']['total']['project']  = $Data['cash']['total']['amount']  - $Data['cash']['total']['autor'];
            ?>
            <th>Booka</th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['project']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['project']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['project']) ?></td>
            <td>92&#37; del importe</td>
        </tr>
    </table>
<?php endif; ?>

</div>