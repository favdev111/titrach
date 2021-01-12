<?php
$this->breadcrumbs=array(
	'Patients Mandates'=>array('index','pid'=>$patient->id),
	$patient->getFullName()=>array('update','id'=>$model->id),
	'Update',
);

?>

<h1>Update mandate for <?php echo $patient->getFullName(); ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>