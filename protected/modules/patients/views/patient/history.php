<?php
$this->breadcrumbs=array(
	'Students'=>array('/patients/patient/index'),
	$patient->getFullName(),
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



Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});

$('.btn[type=\"reset\"]').click(function(){
	setTimeout(function(){\$('.search-form form').submit() },100);
});

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>History for student: <?php echo $patient->getFullName();?></h2>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form compacted" style="display:none">
<?php /*$this->renderPartial('_historySearch',array(
	'entity'=>$form,
	'patient'=>$patient,
)); */?>
</div><!-- search-form -->
<p style="margin-bottom: -20px;">
	<b>Note:</b> Rows with blue background contains initial records.
</p>
<?php

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$entities->search(),
	//'filter'=>$patietnts,
	'type' => 'striped bordered condensed',
	'rowCssClassExpression'=>'$data->initial ? "info":""',
	'columns'=>array(
		array(
			'name' =>'patient_id',
			'type' => 'raw',
			'header' => 'Pat.ID',
			'value' =>'"psy_".$data->patient_id'
		), 
		array(
			'name' =>'id',
			'type' => 'raw',
			'header' => 'F.ID',
			'value' =>'"f_".$data->id'
		),		
		array(
			'name'=>'form_title',
			'header'=>'Form',
			'value'=>'$data->form->getEntityRelatedTitle($data)',
		),
		'patient.student_id',	
		'patient.lastname',		
		'patient.firstname',
		'patient.gender',
		'patient.dob',
		array(
			'name'=>'timestamp',
			'type'=>'raw',
			'header'=>'Date added',
			'value' => function (FormsEntities $data) {
				return Yii::app()->dateFormatter->formatDateTime($data->timestamp, 'medium', '');
			},			

		),
		array(
			'name'=>'add',
			'type'=>'raw',
			'header'=>'',
			'value'=>array($this,'renderPatientHistoryRowButtons'),
		),		
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update}  {view} {pdf}  {delete}',
			'buttons' =>array(
				'view' =>array(
					'label'=>'View template',
					'icon' =>'eye-open',
					'url' => 'Yii::app()->createUrl("patients/form/view",array("eid"=>$data->id,"pid"=>$data->patient_id,"fid"=>$data->form_id))',
				),
				'pdf' =>array(
					'label'=>'Download PDF',
					'icon' =>'download-alt',
					'options'=>array(
						'target'=>'_blank',
					),
					'url' => 'Yii::app()->createUrl("patients/form/renderPDF",array("eid"=>$data->id,"pid"=>$data->patient_id,"fid"=>$data->form_id))',
				),				
			),
			'deleteConfirmation'=>'Are you sure you want to delete this record? All related data will be deleted too',
			'afterDelete'=> 'function(th,success,data){$("body").append(data)}',
			'updateButtonUrl'=>'"/patients/form/edit/eid/".$data->id."/pid/".$data->patient_id."/fid/$data->form_id"',
			'deleteButtonUrl'=>'"/patients/patient/delete/id/".$data->id."/entity/1"',
		),
	),
)); ?>
