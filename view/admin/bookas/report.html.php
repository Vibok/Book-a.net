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

$booka = $this['booka'];
$Data    = $this['reportData'];

?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <h3 class="title">Informe de financiación para del booka <span style="color:#20B2B3;"><?php echo $booka->name ?></span></h3>

    <?php
    $sumData['total'] = $booka->amount;
    $sumData['fail']  = $Data['tpv']['total']['fail']   + $Data['paypal']['total']['fail']   + $Data['cash']['total']['fail'];
    $sumData['brute'] = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount'];
    $sumData['tpv_fee_booka'] = $Data['tpv']['total']['amount']  * 0.008;
    $sumData['pp_booka'] = $Data['paypal']['total']['amount'] * 0.08;
    $sumData['pp_book'] = $Data['paypal']['total']['amount'] - $sumData['pp_booka'];
    $sumData['pp_fee_booka'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_booka'] * 0.034);
    $sumData['pp_fee_book'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_book'] * 0.034);
    $sumData['pp_net_book'] = $sumData['pp_book'] - $sumData['pp_fee_book'];
    $sumData['fee_booka'] = $sumData['tpv_fee_booka'] + $sumData['pp_fee_booka'];
    $sumData['net'] = $sumData['brute'] - $sumData['tpv_fee_booka'] - $sumData['pp_fee_booka'] - $sumData['pp_fee_book'];
    $sumData['booka'] = $sumData['brute'] * 0.08;
    $sumData['restbook'] = $sumData['brute'] - $sumData['booka'] - $sumData['pp_book'];
    ?>
<br />

    <table>
        <tr>
            <th style="text-align:left;">Resumen de recaudación</th>
        </tr>
        <tr>
            <td>- Mostrado en el termómetro: <?php echo \amount_format($sumData['total'], 2) ?></td>
        </tr>
        <tr>
            <td>- Ingresado realmente descontando incidencias<strong>*</strong> (usuarios que no tienen fondos, cancelaciones, etc): <?php echo \amount_format($sumData['brute'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisión del 8&#37;: <?php echo \amount_format($sumData['booka'], 2) ?></td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">Comisiones de bancos</th>
        </tr>
        <tr>
            <td>- Comisiones cobradas a booka por los bancos (asumidas por la Fundación, no se cobran al impulsor): <?php echo \amount_format($sumData['fee_booka'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisiones cobradas al impulsor por PayPal (estimadas): <?php echo \amount_format($sumData['pp_fee_book'], 2) ?></td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">Transferencias de la Fundación Fuentes Abiertas al impulsor</th>
        </tr>
        <tr>
            <td>- Enviado a través de PayPal (sin descontar comisiones de PayPal al impulsor): <?php echo \amount_format($sumData['pp_book'], 2) ?> (/fecha/)</td>
        </tr>
        <tr>
            <td>- Enviado a través de cuenta bancaria: <?php echo \amount_format($sumData['restbook'], 2) ?> (/fecha/)</td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">Desglose informativo de lo pagado mediante PayPal</th>
        </tr>
        <tr>
            <td>- Cantidad transferida: <?php echo \amount_format($sumData['pp_book'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisión aproximada cobrada al impulor: <?php echo \amount_format($sumData['pp_fee_book'], 2) ?></td>
        </tr>
        <tr>
            <td>- Cantidad aproximada recibida por el impulsor: <?php echo \amount_format($sumData['pp_net_book'], 2) ?></td>
        </tr>
    </table>

<?php if (!empty($Data['issues'])) : ?>
    <br />
    <table>
        <tr>
            <th style="text-align:left;"><strong>*</strong> Pagos de usuarios con incidencias</th>
        </tr>
        <?php foreach ($Data['issues'] as $issue) : ?>
        <tr>
            <td><?php echo '<a href="/admin/accounts/details/'.$issue->invest.'" target="_blank">[Ir al aporte]</a> Usuario <a href="/admin/users/manage/' . $issue->user . '" target="_blank">' . $issue->userName . '</a> [<a href="mailto:'.$issue->userEmail.'">'.$issue->userEmail.'</a>], ' . $issue->statusName . ', ' . $issue->amount . ' euros.'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</div>

