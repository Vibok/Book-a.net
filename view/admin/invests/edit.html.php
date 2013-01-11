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
    Base\Library\NormalForm;

$invest = $this['invest'];
$booka = $this['booka'];
$user = $this['user'];
$types = Base\Model\Booka\Reward\Type::getAll();

uasort($booka->rewards,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );
    
$rewards = array();
foreach ($invest->rewards as $key => $data) {
    $rewards[$data->reward] = $data->reward;
}
?>
<div class="widget">
    <p>
        <strong>Booka:</strong> <?php echo $booka->name ?> (<?php echo $this['status'][$booka->status] ?>)<br />
        <strong>Usuario: </strong><?php echo $user->name ?>
    </p>
    
<div class="hr"><hr /></div>

<form method="post" action="/admin/invests/edit/<?php echo  $invest->id; ?>" >
    <p>
    <label>Importe<br />
        <input type="text" name="amount" value="<?php echo $invest->amount ?>" /> &euro;
    </label>
    </p>
    
    <p>
    <label>Anónimo<br />
        <input type="checkbox" name="anonymous" value="1" <?php if ($invest->anonymous) echo ' checked="checked"'; ?> />
    </label>
    </p>
    
    <label>Recompensa<br/>(No marcar ninguna si renuncia a recompensa)</label>
    <ul style="list-style: none;">
    <?php foreach ($booka->rewards as $reward) : 
        if ($reward->type == 9999) {
            $reward->name = $reward->other_text;
        } else {
            $reward->name = $types[$reward->type]->name;
        }
        ?>
    <li>

        <label>
            <input type="checkbox" name="selected_rewards[]" id="reward_<?php echo $reward->id; ?>" value="<?php echo $reward->id; ?>" amount="<?php echo $reward->amount; ?>" class="reward" title="<?php echo htmlspecialchars($reward->name) ?>" <?php if ($reward->none) echo 'disabled="disabled"' ?>  <?php if (isset($rewards[$reward->id])) echo ' checked="checked"'; ?>/>
            <?php echo '<strong>' .$reward->amount . ' &euro;</strong> ' . htmlspecialchars($reward->name); ?>
        </label>

    </li>
    <?php endforeach ?>
    </ul>
<br />

<?php
echo new NormalForm(array(

    'level'         => 3,
    'method'        => 'post',
    'footer'        => array(
        'button' => array(
            'type'  => 'submit',
            'name'  => 'update',
            'label' => 'Guardar',
            'class' => 'std-btn wide'
        )
    ),
    'elements'      => array(

        'name' => array(
            'type'      => 'textbox',
            'size'      => 40,
            'title'     => Text::get('invest-address-name-field'),
            'value'     => $invest->address->name
        ),

        'nif' => array(
            'type'      => 'textbox',
            'title'     => Text::get('invest-address-nif-field'),
            'size'      => 15,
            'value'     => $invest->address->nif
        ),

        'address' => array(
            'type'  => 'textbox',
            'title' => Text::get('address-address-field'),
            'size'  => 55,
            'value' => $invest->address->address
        ),

        'location' => array(
            'type'  => 'textbox',
            'title' => Text::get('address-location-field'),
            'size'  => 55,
            'value' => $invest->address->location
        ),

        'city' => array(
            'type'  => 'textbox',
            'title' => Text::get('address-city-field'),
            'size'  => 55,
            'value' => $invest->address->city
        ),

        'zipcode' => array(
            'type'  => 'textbox',
            'title' => Text::get('address-zipcode-field'),
            'size'  => 7,
            'value' => $invest->address->zipcode
        ),

        'country' => array(
            'type'  => 'textbox',
            'title' => Text::get('address-country-field'),
            'size'  => 55,
            'value' => $invest->address->country
        ),

    )

));

?>
</form>
    
</div>