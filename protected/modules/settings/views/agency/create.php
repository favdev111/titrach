<?php
$this->breadcrumbs=array(
	'Agencies'=>array('index'),
	'Create',
);

?>

<h1>Create Agency</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>