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
$stages = $this['stages'];

// separar los costes por tipo
$costs = array();
$total = array();

foreach ($booka->costs as $cost) {

    $costs[$cost->stage][] = (object) array(
        'name' => $cost->cost,
        'description' => $cost->description,
        'amount' => $cost->amount
    );
    
    $total[$cost->stage] += $cost->amount;
    $total['total'] += $cost->amount;
}


/*
     <script stage="text/javascript">
	$(document).ready(function() {
	   $("div.click").click(function() {
		   $(this).children("blockquote").toggle();
		   $(this).children("span.icon").toggleClass("closed");
		});
	 });
	</script>
 */
?>
<div id="booka-needs" class="center-widget">
        
    <?php foreach ($stages as $stage => $stageName): 
        if (empty($costs[$stage])) continue;
        ?>
    <h3 class="ct2 fs-L upcase wshadow underlined stage<?php echo $stage; ?>"><?php echo Text::get('stage-'.$stage); ?> <?php echo $stageName; ?></h3>
    <table width="100%">
        
        <?php foreach ($costs[$stage] as $cost): ?>
        <tbody>            
            <tr class="bottom dashed">
                <td class="need">
                    <span class="ct1 upcase ft2 fs-M"><?php echo htmlspecialchars($cost->name) ?>:</span>
                    <blockquote class="ft2"><?php echo htmlspecialchars($cost->description) ?></blockquote>
                </td>
                <td class="amount ct1 ft3"><?php echo \amount_format($cost->amount) ?>&euro;</td>
            </tr>            
        </tbody>
        <?php endforeach ?>
        
        <tfoot>
            <tr class="subtotal bottom dashed">
                <td>
                    <span class="ct1 upcase ft2 fs-M"><?php echo Text::get('regular-total').' '.$stageName; ?></span>
                </td>
                <td class="amount ct1 ft3"><?php echo \amount_format($total[$stage]) ?>&euro;</td>
            </tr>
        </tfoot>
        
    </table>
    
    <div class="espacio-vacio-relleno" style="height: 36px;"></div>
    <?php endforeach ?>
    
    <table width="100%">
        <tbody>
            <tr class="total underlined">
                <td>
                    <span class="ct1 upcase ft2 fs-M"><?php echo Text::get('costs-total'); ?></span>
                </td>
                <td class="amount ct1 ft3 fs-M"><?php echo \amount_format($total['total']) ?>&euro;</td>
            </tr>
            <tr class="underlined">
                <td class="need info" colspan="2">
                    <blockquote><?php echo Text::html('costs-table-footer'); ?></blockquote>
                </td>
            </tr>
        </tbody>
    </table>
    
</div>