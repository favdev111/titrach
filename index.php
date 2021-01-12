<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/frameworks/yii/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

require_once(dirname(__FILE__).'/protected/functions.php');

defined('APP_PATH') or define ('APP_PATH',dirname(__FILE__).'/protected/');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
