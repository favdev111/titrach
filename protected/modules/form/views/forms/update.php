<?php
$this->breadcrumbs=array(
	'Forms'=>array('index'),
	$model->form_title=>array('view','id'=>$model->id),
	'Update',
);
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile(Yii::app()->baseUrl.'/js/ui.min.js');
  
?>

<h1>Update Forms <?php echo $model->form_title; ?></h1>

<?php echo $this->renderPartial('/forms/_form',array('model'=>$model)); ?>