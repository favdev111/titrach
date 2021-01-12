<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Tifrach Learning',
    'timeZone' => 'GMT',
	// preloading 'log' component
	'preload'=>array(
		'log',
		'bootstrap',
		'sartparams'
		),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.actions.*',
		'application.widgets.*',
        'application.behaviors.*',
		'application.helpers.*'		
	),
	'aliases' => array(
		'xupload' => 'ext.xupload'
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array(
					//'application.gii',
					'bootstrap.gii',
				),
			),
		'form',
		'settings',
		'patients',
		'calendar',
		'billing'
	),
	

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class'=>'WebUser',
		),
		'bootstrap'=>array(
			'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
		),
		
		// uncomment the following to enable URLs in path-format
		/*'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'patients/files/get/pid/<pid:\d+>/<file:.+>'=>'/patients/files/get',
                'patients/files/delete/pid/<pid:\d+>/<file:.+>'=>'/patients/files/delete',
				'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
			),
		),*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=tifrach_learning',
			'emulatePrepare' => true,
			'username' => 'tifrach_learning',
			'password' => 'uQl~_FTW8XIu',
			'charset' => 'utf8',
			//'enableProfiling'=>true,
			//'enableParamLogging'=>true,

		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				//array(
				//	'class'=>'CWebLogRoute',
				//	'levels'=>'profile, trace'
				//),
				
			),
		),
		'authManager' => array(
			'class' => 'PhpAuthManager',
				'defaultRoles' => array('guest'),
		),
		
		'sartparams'=>array(
			'class'=>'SartParams'
			
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'main_form_id'=>'1',
		'validation_rules'=>array(
			'required'=>'Field {{field_name}} is required',
			'date'=>array('message'=>'Invalid date format','format'=>'MM/DD/YYYY'),
			'alphanumeric'=>'Field {{field_name}}  should be alphanumeric',
			'numeric'=>'Field {{field_name}}  should be numeric',
			'email'=>'Invalid email format',
			'range'=>'Field value should be in range'
		),
		'file_storage'=>'{{CONST_APP_PATH}}/../file_storage/',
		'tmp_file_storage'=>'{{CONST_APP_PATH}}/../file_storage/tmp/',
		'rows_per_page' =>50,
        'services_field_id'=>18
	),
);