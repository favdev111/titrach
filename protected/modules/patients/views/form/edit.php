<?php
$form_title = $form->getEntityRelatedTitle($entity);

$this->breadcrumbs=array(
	'Patients'=>array('patient/index'),
	$patient->getFullName(true)=>array('patient/history/','pid'=>$patient->id),
	$form_title=>Yii::app()->request->requestUri,
	'Edit'
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

<h3>
<?php
if($form->is_printable)
	$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Download PDF',
			'type'=>'primary', 
			'size'=>'small',
			'url'=>'/patients/form/renderPDF/eid/'.$entity->id.'/pid/'.$patient->id.'/fid/'.$form->id,
			'htmlOptions'=>array(
				'target'=>'_blank',
				'class'=>'pull-right'
			),
		));
?>
	Edit <?php echo $form_title; ?> for <?php echo $patient->getFullName(true);?></h3>
<?php $this->renderPartial('_form',array('model'=>$form,'pages'=>$pages,'patient_ID'=>$patient->id,'entity'=>$entity))?>
<script>
	jQuery(function(){
		
	});
</script>