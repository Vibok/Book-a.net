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
    Base\Model\Invest,
    Base\Library\Text;

$booka = $this['booka'];
$personal = $this['personal'];
$min = 9;
foreach ($booka->rewards as $rw) {
    if ($rw->amount < $min) $min = $rw->amount; 
}

uasort($booka->rewards,
    function ($a, $b) {
        global $min;
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );
    
$amount = !empty($_GET['amount']) ? $_GET['amount'] : $min;
if ($amount < $min) $amount = $min;
    
?>

<div class="center-widget booka-invest">
    <h3 class="ct1 upcase fs-L"><?php echo Text::get('invest-amount') ?></h3>
    
    <form method="post" action="<?php echo '/invest/' . $booka->id; ?>">
        
    <label class="ft2 fs-M"><?php echo Text::get('invest-amount-tooltip') ?><br />
        <input type="text" id="amount" name="amount" class="amount ft3 ct3 fs-L" value="<?php if (!empty($amount)) echo $amount ?>" />
        <span class="euro ct3 fs-L">&euro;</span>
    </label>
    <span class="bloque ft3 fs-S rojo"><?php echo Text::get('invest-min_amount', $min) ?></span><br />

    <div class="hr" style="margin-bottom: 6px;"><hr /></div>
    
    <label class="ft2 fs-M ct2"><input type="checkbox" name="anonymous" value="1" style="margin-right: 6px;" /><?php echo Text::get('invest-anonymous') ?></label>
    
</div>

    
<div class="center-widget booka-invest">
    <h3 class="ct1 upcase fs-L"><?php echo Text::get('invest-rewards-header') ?></h3>
    
    <p class="ft2 fs-M"><?php echo Text::get('invest-rewards-tooltip') ?></p>
    
    <div class="hr"><hr /></div>
    
    <ul class="underlined invest-rewards">
        <li class="underlined resign">
            <label class="fs-M ct2"><input class="resign" type="radio" name="selected_reward" value="0" /><?php echo Text::get('invest-resign') ?></label>
        </li>
    <?php $c = 1; foreach ($booka->rewards as $reward) : ?>
        <li class="<?php if ($reward->none) echo ' disabled' ?> bottom dashed" <?php if ($c == count($booka->rewards)) echo ' style="border: none !important;"'; ?>>
            <div class="radio">
                <input type="radio"<?php if ($reward->none) echo ' disabled="disabled"';?> name="selected_reward" id="reward_<?php echo $reward->id; ?>" amount="<?php echo $reward->amount; ?>" value="<?php echo $reward->id; ?>" class="reward" title="<?php echo htmlspecialchars($reward->name) ?>"/>
            </div>
            
            <div class="amount">
                <span class="amount fs-L ft3 ct2"><?php echo $reward->amount; ?></span><span class="euro fs-M ct2">&euro;</span>
            </div>
            
            <div class="reward-content">
                <label for="reward_<?php echo $reward->id; ?>">
                    <span class="upcase fs-M ct1"><?php echo htmlspecialchars($reward->name) ?></span><br />
                    <p class="ft2<?php if ($reward->none) echo ' rojo' ?>"><?php echo htmlspecialchars($reward->description)?></p>
                    <?php if (!empty($reward->units)) : 
                        $units = ($reward->units - $reward->taken); ?>
                        <span class="ft2 ct5"><?php echo Text::get('booka-rewards-limited') ?> | </span><span class="upcase ft2 ct5"><?php echo Text::html('booka-rewards-units_left', $units); ?></span>
                    <?php endif; ?>
                </label>
            </div>
            <br clear="both" />
        </li>
    <?php $c++; endforeach; ?>
    </ul>

</div>

<div class="center-widget booka-invest address">
    <h3 class="ct1 upcase fs-L"><?php echo Text::get('invest-address-header') ?></h3>
    
    <p class="ft2 fs-M"><?php echo Text::get('invest-address-tooltip') ?></p>
    
    <table>
        <tr>
            <td colspan="3">
                <label class="ft2 ct1"><?php echo Text::get('invest-address-name-field') ?><br />
                    <input type="text" id="name" name="name" value="<?php echo $_SESSION['user']->name; ?>" style="width: 100%"/></label>
            </td>
            <td style="width: 30px;">&nbsp</td>
            <td>
                <label class="ft2 ct1" for="nif"><?php echo Text::get('invest-address-nif-field') ?><br />
                    <input type="text" id="nif" name="nif" value="<?php echo $personal->nif; ?>" /></label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">
                <label class="ft2 ct1" for="address"><?php echo Text::get('address-address-field') ?><br />
                    <input type="text" id="address" name="address" value="<?php echo $personal->address; ?>" style="width: 100%"/></label>
            </td>
            <td style="width: 30px;">&nbsp</td>
            <td>
                <label class="ft2 ct1" for="zipcode"><?php echo Text::get('address-zipcode-field') ?><br />
                    <input type="text" id="zipcode" name="zipcode" value="<?php echo $personal->zipcode; ?>" /></label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <label class="ft2 ct1" for="location"><?php echo Text::get('address-location-field') ?><br />
                    <input type="text" id="location" name="location" value="<?php echo $personal->location; ?>" /></label>
            </td>
            <td style="width: 15px;">&nbsp</td>
            <td>
                <label class="ft2 ct1" for="city"><?php echo Text::get('address-city-field') ?><br />
                    <input type="text" id="city" name="city" value="<?php echo $personal->city; ?>" /></label>
            </td>
            <td style="width: 30px;">&nbsp</td>
            <td>
                <label class="ft2 ct1" for="country"><?php echo Text::get('address-country-field') ?><br />
                    <input type="text" id="country" name="country" value="<?php echo $personal->country; ?>" /></label>
            </td>
        </tr>
    </table>
</div>


<div class="center-widget booka-invest method">
    <h3 class="ct1 upcase fs-L"><?php echo Text::get('invest-method-header') ?></h3>
            
    <input type="hidden" id="paymethod"  />

    <ul class="line">
        <li><button type="submit" class="process pay-tpv" name="method"  value="tpv">TPV</button></li>
        <li><button type="submit" class="process pay-paypal" name="method"  value="paypal">PAYPAL</button></li>
        <li><button type="submit" class="process pay-cash" name="method"  value="cash">CASH</button></li>
    </ul>

    </form>

    <div class="hr"><hr /></div>
    <br />
    <span class="ft2 fs-M"><?php echo Text::get('invest-reminder-label') ?> </span><span class="invest-amount-reminder ft3 ct3 fs-L"><?php echo $amount; ?></span><span class="euro ct3 fs-M">&euro;</span>
</div>

    
<div class="center-widget booka-invest footer">
    <h3 class="ct1 upcase fs-L"><?php echo Text::get('invest-contribution-header') ?></h3>
            
    <ul>
        <li class="ft2"><?php echo Text::get('invest-contribution-line_1') ?> <span class="ct1"><?php echo $booka->name; ?></span></li>
        <li class="ft2"><?php echo Text::get('invest-contribution-line_2') ?> <span style="color:<?php echo '#'.$booka->collData->color; ?>"><?php echo $booka->collData->name; ?></span></li>
        <li class="ft2"><?php echo Text::get('invest-contribution-line_3') ?></li>
        <li class="ft2"><?php echo Text::get('invest-contribution-line_4') ?></li>
        <li class="ft2"><?php echo Text::get('invest-contribution-line_5') ?></li>
    </ul>

    </form>

    <div class="hr"><hr /></div>
    <br />
    <span class="ct1 fs-M"><?php echo Text::get('invest-bottom_thanks') ?></span>
</div>

    
<script type="text/javascript">
    
    $(function () {
        
        var update = function () {

            var $reward = null;
            var val = parseFloat($('#amount').val());
            
            if (val < <?php echo $min; ?>) {
                val = <?php echo $min; ?>;
                reset_amount(<?php echo $min; ?>);
                reset_reminder(<?php echo $min; ?>);
            }

            

            $('input.reward').each(function (i, cb) {
               var $cb = $(cb);
               $cb.closest('li').removeClass('chosed');
               /* importe de esta recompensa */
               var rval = parseFloat($cb.attr('amount'));
               if (rval > 0 && rval <= val) {
                   /* si aun quedan */
                   if ($cb.attr('disabled') != 'disabled') {
                       /* nos quedamos con esta y seguimos */
                       $reward = $cb;
                   }
               }

               if ($reward != null) {
                 $reward.click();
                 $reward.closest('li').addClass('chosed');
               } else {
                 $('#resign_reward').click();
                 $('#resign_reward').closest('li').addClass('chosed');
               }
            });
        };    

/*
        var reset_reward = function (chosen) {

            $('input.reward').each(function (i, cb) {
               var $cb = $(cb);
               $cb.closest('li').removeClass('chosed');

               if ($cb.attr('id') == chosen) {
                 $cb.closest('li').addClass('chosed');
               }
            });
        };
*/

        /* funcion comparar valores */
        var greater = function (a, b) {
            if (parseFloat(a) > parseFloat(b)) {
                return true;
            } else {
                return false;
            }
        };

        /* funcion resetear inpput de cantidad */
        var reset_amount = function (preset) {
            $('#amount').val(preset);
            update();
        };

        /* funcion resetear copy de cantidad */
        var reset_reminder = function (amount) {
            var euros = parseFloat(amount);
            if (isNaN(euros)) {
                euros = 0;
            }

            $('#amount').val(euros);
            $('span.invest-amount-reminder').html(euros);
        };

/* Actualizar el copy */
        $('#amount').bind('paste', function () {reset_reminder($('#amount').val());update()});

        $('#amount').change(function () {reset_reminder($('#amount').val());update()});


/* Si estan marcando o quitando el renuncio */
        $(':radio').bind('change', function () {
            var curr = $('#amount').val();
            var a = $(this).attr('amount');
            var i = $(this).attr('id');

/*
            if ($('#resign_reward').attr('checked') == 'checked') {
                reset_reward(i);
            } else {
                reset_reward(i);
            }
 */          
            if (greater(a, curr)) {
                reset_reminder(a);
            }
        });

/* Verificacion */
        $('button.process').click(function () {

            var amount = $('#amount').val();

            if (parseFloat(amount) == 0 || isNaN(amount)) {
                alert('<?php echo Text::get('invest-amount-error') ?>');
                $('#amount').focus();
                return false;
            }

            if (parseFloat(amount) < <?php echo $min?>) {
                alert('<?php echo Text::get('invest-amount-min', $min) ?>');
                $('#amount').focus();
                return false;
            }

            /* Renuncias pero no has puesto tu NIF para desgravar el donativo */
            if ($('#resign_reward').attr('checked') == 'checked') {
                if ($('#nif').val() == '' && !confirm('<?php echo Text::get('invest-alert-renounce') ?>')) {
                    $('#nif').focus();
                    return false;
                }
            } else {
                var reward = '';
                var chosen = 0;
                /* No has marcado ninguna recompensa, renuncias? */
                $('input.reward').each(function (i, cb) {
                   var prize = $(this).attr('amount');
                   if (greater(prize, 0) && $(this).attr('checked') == 'checked') {
                       reward = $(this).attr('title');
                       chosen = prize;
                   }
                });

               if (greater(chosen, amount)) {
                   alert('<?php echo Text::get('invest-alert-lackamount') ?>');
                   return false;
               }
               
                if (reward == '') {
                    if (confirm('<?php echo Text::get('invest-alert-noreward') ?>')) {
                        /* if (confirm('<?php echo Text::get('invest-alert-noreward_renounce') ?>')) {
                            $('#resign_reward').click();
                            $('#nif').focus();
                            return false;
                        } */
                    } else {
                        $('#nif').focus();
                        return false;
                    }
                }
            }

            return confirm('<?php echo Text::get('invest-alert-investing') ?> '+amount+' EUR');
        });

/* Seteo inicial por url */
        reset_amount('<?php echo $amount ?>');

    });    
    
</script>
