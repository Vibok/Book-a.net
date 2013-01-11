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
    Base\Library\SuperForm;

$booka = $this['booka'];

/* New one */
$types = array();

foreach ($this['types'] as $type) {

    $types["reward-new-type-{$type->id}"] = array(
        'name' => "reward-new-type",
        'value' => $type->id,
        'type' => 'radio',
        'class' => "reward-type reward-type no-update",
        'label' => $type->name,
        'hint' => $type->description,
        'id' => "reward-new-type-{$type->id}",
        'checked' => false
    );
}

$types["reward-new-type-other_text_es"] = array(
    'name' => "reward-new-other_text_es",
    'value' => '',
    'type' => 'textbox',
    'class' => "reward-type reward-type no-update",
    'label' => '',
    'placeholder' => '(Escribe aquí el tipo si marcas Otro)',
    'checked' => false
);

$types["reward-new-type-other_text_en"] = array(
    'name' => "reward-new-other_text_en",
    'value' => '',
    'type' => 'textbox',
    'class' => "reward-type reward-type no-update",
    'label' => 'INGLES',
    'placeholder' => '(Traducción del tipo Otro)',
    'checked' => false
);


$new_reward = array(
    'type' => 'group',
    'class' => 'reward newreward',
    'children' => array(
        "reward-new-edit" => array(
            'type' => 'hidden',
            'value' => null
        ),
        "reward-new-type" => array(
            'title' => 'Seleccionar recompensas para premiar a los usuarios impulsores:',
            'class' => 'no-update',
            'type' => 'group',
            'children' => $types,
            'value' => ''
        ),
        "reward-new-description_es" => array(
            'type' => 'textarea',
            'title' => 'Descripción',
            'cols' => 100,
            'rows' => 4,
            'class' => 'reward-description no-update',
            'value' => '',
            'placeholder' => '(300 palabras máx.)'
        ),
        "reward-new-description_en" => array(
          'type'      => 'textarea',
          'title'     => 'Descripción INGLÉS',
          'cols'      => 100,
          'rows'      => 4,
          'class'     => 'reward-description no-update',
          'value'     => '',
          'placeholder' => '(300 palabras máx.)'
          ),
        "reward-new-amount" => array(
            'title' => 'Valor',
            'type' => 'textbox',
            'size' => 5,
            'class' => 'reward-amount no-update',
            'placeholder' => '0',
            'value' => ''
        ),
        "reward-new-units" => array(
            'title' => 'Unidades',
            'type' => 'textbox',
            'size' => 5,
            'class' => 'reward-units no-update',
            'value' => ''
        ),
        "reward-new-add" => array(
            'type' => 'submit',
            'label' => Text::get('form-add-button'),
            'class' => 'std-btn add'
        )
    )
);
/* end new one */

$rewards = array();

if (!empty($booka->rewards)) {

    foreach ($booka->rewards as $reward) {

        // a ver si es el que estamos editando o no
        if (!empty($this["reward-{$reward->id}-edit"])) {

            $types = array();

            foreach ($this['types'] as $type) {

                $types["reward-{$reward->id}-type-{$type->id}"] = array(
                    'name' => "reward-{$reward->id}-type",
                    'value' => $type->id,
                    'type' => 'radio',
                    'class' => "reward-type reward-type",
                    'label' => $type->name,
                    'hint' => $type->description,
                    'id' => "reward-{$reward->id}-type-{$type->id}",
                    'checked' => $type->id == $reward->type ? true : false
                );
            }

            $types["reward-{$reward->id}-type-other_text_es"] = array(
                'name' => "reward-{$reward->id}-other_text_es",
                'value' => $reward->other_text_es,
                'type' => 'textbox',
                'class' => '',
                'label' => '',
                'placeholder' => '(Escribe aquí el tipo si marcas Otro)'
            );

            $types["reward-{$reward->id}-type-other_text_en"] = array(
                'name' => "reward-{$reward->id}-other_text_en",
                'value' => $reward->other_text_en,
                'type' => 'textbox',
                'class' => '',
                'label' => 'INGLES',
                'placeholder' => '(Traducción del tipo Otro)'
            );


            // a este grupo le ponemos estilo de edicion
            $rewards["reward-{$reward->id}"] = array(
                'type' => 'group',
                'class' => 'reward editreward',
                'children' => array(
                    "reward-{$reward->id}-edit" => array(
                        'type' => 'hidden',
                        'value' => '1'
                    ),
                    "reward-{$reward->id}-reward" => array(
                        'type' => 'hidden',
                        'value' => $reward->id
                    ),
                    "reward-{$reward->id}-type" => array(
                        'title' => 'Seleccionar recompensas para premiar a los usuarios impulsores:',
                        'class' => '',
                        'type' => 'group',
                        'children' => $types,
                        'value' => $reward->type
                    ),
                    "reward-{$reward->id}-description_es" => array(
                        'type' => 'textarea',
                        'title' => 'Descripción',
                        'cols' => 100,
                        'rows' => 4,
                        'class' => 'reward-description',
                        'value' => $reward->description_es,
                        'placeholder' => '(300 palabras máx.)'
                    ),
                    "reward-{$reward->id}-description_en" => array(
                      'type'      => 'textarea',
                      'title'     => 'Descripción INGLÉS',
                      'cols'      => 100,
                      'rows'      => 4,
                      'class'     => 'reward-description',
                      'value'     => $reward->description_en,
                      'placeholder' => '(300 palabras máx.)'
                      ),
                    "reward-{$reward->id}-amount" => array(
                        'title' => 'Valor',
                        'type' => 'textbox',
                        'size' => 5,
                        'class' => 'reward-amount',
                        'value' => $reward->amount
                    ),
                    "reward-{$reward->id}-units" => array(
                        'title' => 'Unidades',
                        'type' => 'textbox',
                        'size' => 5,
                        'class' => 'reward-units',
                        'value' => $reward->units
                    ),
                    "reward-{$reward->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "reward-{$reward->id}-ok" => array(
                                'type' => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline std-btn ok'
                            ),
                            "reward-{$reward->id}-remove" => array(
                                'type' => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline std-btn remove'
                            )
                        )
                    )
                )
            );
        } else {
            $rewards["reward-{$reward->id}"] = array(
                'class' => 'reward',
                'view' => 'view/booka/edit/rewards/reward.html.php',
                'data' => array('reward' => $reward, 'types' => $this['types']),
            );
        }
    }
}

$sfid = 'sf-booka';

echo new SuperForm(array(
    'id' => $sfid,
    'action' => '',
    'level' => 3,
    'method' => 'post',
    'class' => 'aqua',
    'elements' => array(
        'process_rewards' => array(
            'type' => 'hidden',
            'value' => 'rewards'
        ),
        'inheader1' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Recompensas</h3>'
        ),
        // siempre una nueva abierta para añadir
        'new' => $new_reward,
        'inheader2' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Previsualización de recompensa</h3>'
        ),
        'rewards' => array(
            'type' => 'group',
            'children' => $rewards
        ),
        'footer' => array(
            'type' => 'group',
            'children' => array(
                'button' => array(
                    'type' => 'submit',
                    'name' => 'save-exit',
                    'label' => 'Guardar',
                    'class' => 'std-btn wide'
                )
            )
        )
    )
));
?>
<script type="text/javascript">
    $(function () {
    
        $('.no-update').bind('change', function () {
            Superform.update = function () {};
        });
    
        /* rewards buttons */
        var rewards = $('div#<?php echo $sfid ?> li.element#rewards');

        rewards.delegate('li.element.reward input.edit', 'click', function (event) {
            var data = {};
            data[this.name] = '1';
            Superform.update(rewards, data);
            event.preventDefault();
        });

        rewards.delegate('li.element.editreward input.ok', 'click', function (event) {
            var data = {};
            data[this.name.replace('ok', 'edit')] = '0';
            Superform.update(rewards, data);
            event.preventDefault();
        });

        rewards.delegate('li.element.editreward input.remove, li.element.reward input.remove', 'click', function (event) {
            var data = {};
            data[this.name] = '1';
            Superform.update(rewards, data);
            event.preventDefault();
        });

        rewards.delegate('#reward-new-add input', 'click', function (event) {
            var data = {};
            data[this.name] = '1';
            Superform.update(rewards, data);
            event.preventDefault();
        });
    });
</script>