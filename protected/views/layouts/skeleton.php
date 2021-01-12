<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=1280" />
		<meta name="language" content="en" />
		<script type="text/javascript" src="<?php echo Yii::app()->baseUrl;?>/js/core.js"></script>
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/font-awesome/css/font-awesome.min.css" />
		<!--[if IE 7]>
		<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/font-awesome/css/font-awesome-ie7.min.css">
		<![endif]-->
		<!--[if  lte IE 9]>
		<script type="text/javascript" src="<?php echo Yii::app()->baseUrl; ?>/js/jquery.placeholder.js"></script>
		<![endif]-->
		<!--[if lte IE 8]>
		<script type="text/javascript" src="<?php echo Yii::app()->baseUrl; ?>/js/css3-mediaqueries.js"></script>
		<![endif]-->
		<link href="<?php echo Yii::app()->baseUrl;?>/css/styles.css" rel="stylesheet" media="screen">
	</head>
	<body>
		<!--[if lte IE 9]>
		<script type="text/javascript">
			jQuery(function($){
				$('input,textarea').placeholder();
			})
		</script>
		<![endif]-->		
		<?php echo $content; ?>
	</body>
</html>
