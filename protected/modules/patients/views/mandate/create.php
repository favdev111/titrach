<?php
$this->breadcrumbs=array(
	'Mandates for '.$patient->getFullName()=>array('index','pid'=>$patient->id),
	'Create',
);

?>

<h1>Create mandates for <?php echo $patient->getFullName(); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>