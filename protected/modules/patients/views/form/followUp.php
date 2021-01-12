<?php
$form_title = $form->getEntityRelatedTitle($p_entity);

$this->breadcrumbs=array(
	'Patients'=>array('patient/index'),
	$patient->getFullName(true)=>array('patient/history/','pid'=>$patient->id),
	$form_title=>Yii::app()->request->requestUri,
	'Follow Up'
);

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
			'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        ),
));
?>

<h3> Follow Up <?php echo $form_title; ?> for <?php echo $form_title?> for <?php echo $patient->getFullName();?></h3>
<?php $this->renderPartial('_form',array('model'=>$form,'pages'=>$pages,'patient_ID'=>$patient->id,'entity'=>$entity))?>
<script>
	jQuery(function(){
		
	});
</script>