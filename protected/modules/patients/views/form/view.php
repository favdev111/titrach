<?php $form_title = $form->getEntityRelatedTitle($entity);

$this->breadcrumbs=array(
	'Students'=>array('patient/index'),
	$patient->getFullName(true)=>array('patient/history/','pid'=>$patient->id),
	$form_title=>Yii::app()->request->requestUri,
	'View'
);
?>
<div id="report-wrapper">
<?php
	echo $html
?>
</div>