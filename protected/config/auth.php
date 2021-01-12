<?php

return array(
	'listPatients'=>array(
		'type'=>CAuthItem::TYPE_OPERATION,
		'description'=>'List Patients'
	),
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    'doctor' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Doctor',
        'children' => array(
            'guest',
        ),
        'bizRule' => null,
        'data' => null
    ),
    'manager' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Manager',
        'children' => array(
            'doctor','listPatients'
        ),
        'bizRule' => null,
        'data' => null
    ),
    'administrator' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
        'children' => array(
            'manager',
        ),
        'bizRule' => null,
        'data' => null
    ),
);