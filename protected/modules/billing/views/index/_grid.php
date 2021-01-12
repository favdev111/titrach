<?php

// !!!!! HARDCODE OF FIELDS ID'S !!!!!
$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'bill-rows-grid',
    'ajaxUrl' => array('/billing/index/grid'),
    'type'=>'striped bordered condensed',
    'dataProvider'=>$billRows,
    'columns'=>array(
		array(
            'class'=>'CCheckBoxColumn',
            'selectableRows'=>'100',
            'checkBoxHtmlOptions'=>array('class'=>'entitiy-id','name'=>'entities_id[]',)
        ),
		array(
			'name'=>'id',
			'header'=>'Session ID',
			'value'=>'"f_".$data->id'
		),
		array(
			'name'=>'date',
			'header'=>'Date',
			'value'=>'isset($data->formsEntitiesFields[15]) ? $data->formsEntitiesFields[15]->value: ""',
		),
		array(
			'name'=>'service',
			'header'=>'Service',
			'value'=>'$data->form->getEntityRelatedTitle($data)',
		),			
		array(
			'name'=>'start_time',
			'header'=>'Time In',
			'value'=>'isset($data->formsEntitiesFields[16]) ? $data->formsEntitiesFields[16]->value: ""',
		),
		array(
			'name'=>'end_time',
			'header'=>'Time Out',
			'value'=>'isset($data->formsEntitiesFields[17]) ? $data->formsEntitiesFields[17]->value: ""',
		),
		array(
			'name'=>'group_size',
			'header'=>'Group Size',
			'value'=>'isset($data->formsEntitiesFields[22]) ? implode(";",(array)$data->formsEntitiesFields[22]->getValuesAsArray()): ""',
		),
        array(
            'name'=>'mandate',
            'header'=>'Mandate',
            'value'=>function($data)
            {
                if(isset($data->formsEntitiesFields[36]) && !empty($data->formsEntitiesFields[36]->value))
                {
                    $mandate = PatientsMandates::model()->findByPk($data->formsEntitiesFields[36]->value);
                    if($mandate)
                        return $mandate->Caption;
                }
                return '-';
            }
        ),
    ),
)); ?>