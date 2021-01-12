<?php $form_title = $form->getEntityRelatedTitle($entity);

$this->breadcrumbs=array(
	'Billing'=>array('/billing/index/list/type/all'),
	$form_title=>Yii::app()->request->requestUri,
	'View'
);
?>
<div id="report-wrapper">
<?php
	echo $html
?>
</div>