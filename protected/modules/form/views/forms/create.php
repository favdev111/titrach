<?php
$this->breadcrumbs=array(
	'Forms'=>array('index'),
	'Create',
);

?>

<h1>Create Forms</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>