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
    Base\Library\SuperForm,
    Base\Core\View;

$booka = $this['booka'];

/* New one */
$costStages = array();

foreach ($this['stages'] as $id => $name) {
    $costStages["cost-new-stage-{$id}"] = array(
        'name' => "cost-new-stage",
        'value' => $id,
        'type' => 'radio',
        'class' => "cost-stage $id no-update",
        'label' => $name,
        'checked' => false
    );
}

/*
  $costTypes = array();

  foreach ($this['types'] as $id => $type) {
  $costTypes["cost-new-type-{$id}"] = array(
  'name'  => "cost-new-type",
  'value' => $id,
  'type'  => 'radio',
  'class' => "cost-type $id",
  'label' => $type->name,
  'checked' => false
  );
  }
 */
$new_cost = array(
    'type' => 'group',
    'class' => 'cost newcost',
    'children' => array(
        "cost-new-edit" => array(
            'type' => 'hidden',
            'value' => null
        ),
        "cost-new-cost" => array(
            'type' => 'hidden',
            'value' => $cost->id
        ),
        "cost-new-stage" => array(
            'type' => 'radios',
            'title' => 'Seleccionar etapa',
            'class' => 'cols_3',
            'options' => $costStages
        ),
        "cost-new-cost_es" => array(
            'title' => 'Nombre de la tarea',
            'type' => 'textbox',
            'size' => 100,
            'class' => 'no-update',
            'value' => ''
        ),
          "cost-new-cost_en" => array(
          'title'     => 'Nombre de la tarea INGLÉS',
          'type'      => 'textbox',
          'size'      => 100,
          'class'     => '',
          'value'     => ''
          ),
        "cost-new-description_es" => array(
            'type' => 'textarea',
            'title' => 'Descripción de la tarea',
            'cols' => 100,
            'rows' => 4,
            'class' => 'cost-description no-update',
            'placeholder' => '(300 palabras máx.)',
            'value' => ''
        ),
          "cost-new-description_en" => array(
          'type'      => 'textarea',
          'title'     => 'Descripción de la tarea INGLÉS',
          'cols'      => 100,
          'rows'      => 4,
          'class'     => 'cost-description no-update',
          'placeholder' => '(300 palabras máx.)',
          'value'     => ''
          ),
        "cost-new-amount" => array(
            'title' => 'Valor',
            'type' => 'textbox',
            'size' => 8,
            'class' => 'cost-amount no-update',
            'placeholder' => '0',
            'value' => ''
        ),
        "cost-new-date" => array(
            'title' => 'Fecha',
            'class' => 'cost-date no-update',
            'type' => 'datebox',
            'size' => 8,
            'value' => date('Y-m-d')
        ),
        "cost-new-add" => array(
            'type' => 'submit',
            'label' => Text::get('form-add-button'),
            'class' => 'std-btn add'
        )
    )
);
/* End new one */

$costs = array();

if (!empty($booka->costs)) {

    foreach ($booka->costs as $cost) {

        if (!empty($this["cost-{$cost->id}-edit"])) {

            $costStages = array();

            foreach ($this['stages'] as $id => $name) {
                $costStages["cost-{$cost->id}-stage-{$id}"] = array(
                    'name' => "cost-{$cost->id}-stage",
                    'value' => $id,
                    'type' => 'radio',
                    'class' => "cost-stage $id",
                    'label' => $name,
                    'checked' => $id == $cost->stage ? true : false
                );
            }

            /*
              $costTypes = array();

              foreach ($this['types'] as $id => $type) {
              $costTypes["cost-{$cost->id}-type-{$id}"] = array(
              'name'  => "cost-{$cost->id}-type",
              'value' => $id,
              'type'  => 'radio',
              'class' => "cost-type $id",
              'label' => $type->name,
              'checked' => $id == $cost->type  ? true : false
              );
              }
             */
            $costs["cost-{$cost->id}"] = array(
                'type' => 'group',
                'class' => 'cost editcost',
                'children' => array(
                    "cost-{$cost->id}-edit" => array(
                        'type' => 'hidden',
                        'value' => '1'
                    ),
                    "cost-{$cost->id}-cost" => array(
                        'type' => 'hidden',
                        'value' => $cost->id
                    ),
                    "cost-{$cost->id}-stage" => array(
                        'title' => 'Seleccionar etapa',
                        'class' => 'cols_3',
                        'type' => 'radios',
                        'options' => $costStages
                    ),
                    "cost-{$cost->id}-cost_es" => array(
                        'title' => 'Nombre de la tarea',
                        'type' => 'textbox',
                        'size' => 100,
                        'class' => '',
                        'value' => $cost->cost_es
                    ),
                      "cost-{$cost->id}-cost_en" => array(
                      'title'     => 'Nombre de la tarea INGLÉS',
                      'type'      => 'textbox',
                      'size'      => 100,
                      'class'     => '',
                      'value'     => $cost->cost_en
                      ),
                    "cost-{$cost->id}-description_es" => array(
                        'type' => 'textarea',
                        'title' => 'Descripción de la tarea',
                        'cols' => 100,
                        'rows' => 4,
                        'class' => 'cost-description',
                        'value' => $cost->description_es,
                        'placeholder' => '(300 palabras máx.)'
                    ),
                      "cost-{$cost->id}-description_en" => array(
                      'type'      => 'textarea',
                      'title'     => 'Descripción de la tarea INGLÉS',
                      'cols'      => 100,
                      'rows'      => 4,
                      'class'     => 'cost-description',
                      'value'     => $cost->description_en,
                      'placeholder' => '(300 palabras máx.)'
                      ),
                    "cost-{$cost->id}-amount" => array(
                        'type' => 'textbox',
                        'title' => 'Valor',
                        'size' => 8,
                        'class' => 'cost-amount',
                        'value' => $cost->amount
                    ),
                    "cost-{$cost->id}-date" => array(
                        'class' => 'cost-date',
                        'type' => 'datebox',
                        'size' => 8,
                        'title' => 'Fecha',
                        'value' => $cost->date ? : date('Y-m-d')
                    ),
                    "cost-{$cost->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "cost-{$cost->id}-ok" => array(
                                'type' => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline std-btn ok'
                            ),
                            "cost-{$cost->id}-remove" => array(
                                'type' => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline std-btn remove'
                            )
                        )
                    )
                )
            );
        } else {
            $costs["cost-{$cost->id}"] = array(
                'class' => 'cost',
                'view' => 'view/booka/edit/costs/cost.html.php',
                'data' => array('cost' => $cost), //, 'types' => $this['types']
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
        'process_costs' => array(
            'type' => 'hidden',
            'value' => 'costs'
        ),
        'inheader1' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Presupuesto</h3>'
        ),
        // siempre uno nuevo abierto para añadir
        'new' => $new_cost,
        'inheader2' => array(
            'type' => 'html',
            'html' => '<h3 class="in-header">Previsualización de costes</h3>'
        ),
        'costs' => array(
            'type' => 'group',
            'children' => $costs
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
    
        var costs = $('div#<?php echo $sfid ?> li.element#costs');    
    
        costs.delegate('li.element.cost input.edit', 'click', function (event) {
            var data = {};
            data[this.name] = '1';
            Superform.update(costs, data); 
            event.preventDefault();
        });
    
        costs.delegate('li.element.editcost input.ok', 'click', function (event) {
            var data = {};
            data[this.name.replace('ok', 'edit')] = '0';
            Superform.update(costs, data);         
            event.preventDefault();
        });
    
        costs.delegate('li.element.editcost input.remove, li.element.cost input.remove', 'click', function (event) {        
            var data = {};
            data[this.name] = '1';
            Superform.update(costs, data);
            event.preventDefault();
        });
    
        costs.delegate('#cost-new-add input', 'click', function (event) {
            event.preventDefault();
            /*
       alert($("#cost-new-stage").val() + '  ' + $("#cost-new-cost_es").val());
       if ($("#cost-new-stage").val() == '') {
           alert('Debes indicar la etapa');
           return false;
       }
       if ($("#cost-new-cost_es").val() == '') {
           alert('Debes poner un nombre a la tarea');
           return false;
       }
             */
            var data = {};
            data[this.name] = '1';
            Superform.update(costs, data); 
        });
    });
</script>
