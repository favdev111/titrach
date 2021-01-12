<?php
$this->breadcrumbs=array(
	'All Students'=>array('patient/index'),
	$model->form_title=>array('patient/form/','id'=>$model->id),
	'New'
);

$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>
<h3>Add new <?php echo $model->form_title?></h3>
<?php
$this->renderPartial('_form',array('model'=>$model,'pages'=>$pages,'patient_ID'=>$patient_ID,'entity'=>$entity));
if(!$entity):?>
<script>
	jQuery(function(){
		for($i=1;$i<=system.steps_count;$i++){
			tools.disableTab('#tabs',$i);
		}
	});
</script>
<?php endif; ?>