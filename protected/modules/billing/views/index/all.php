<?php
/* @var $this DefaultController */

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.maskedinput-1.0.js');

$this->breadcrumbs=array(
	'Billing'=>'#',
	$title
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

<h2>Browse <?php echo $title; ?> bills</h2>
<div class="search-form compacted one-row" style="display:block">
<?php $this->renderPartial('_search',array(
	'entity'=>$entities,
	'forms'=>$form
)); ?>
</div><!-- search-form -->
<?php
$columns = array_merge(array(
			array(
				'name' =>'patient_id',
				'type' => 'raw',
				'header' => 'Pat.ID',
				'value' =>'"std_".$data->patient_id'
			),
			array(
				'name' =>'id',
				'type' => 'raw',
				'header' => 'Bill.ID',
				'value' =>'"b_".$data->id'
			),		
			array(
				'name'=>'form_title',
				'header'=>'Form',
				'value'=>'$data->form->getEntityRelatedTitle($data)',
			)
		),
		$this->getFormBrowsableColumns($form),
		array(
			array(
				'name'=>'rowsAmount',
				'type'=>'raw',
				'header'=>'Rows Count',
				'value'=>'$data->billRowsCount'
				),
			array(
				'name'=>'timestamp',
				'type'=>'raw',
				'header'=>'Date added',
				'value' => function (FormsEntities $data) {
					return Yii::app()->dateFormatter->formatDateTime($data->timestamp, 'medium', '');
				},				
				//'value'=>'date("m/d/Y h:m",$data->timestamp)'
			),
			array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'template'=>'{view} {pdf} {delete} ',//{update}   
				'buttons' =>array(
					'view' =>array(
						'label'=>'View template',
						'icon' =>'eye-open',
						'url' => 'Yii::app()->createUrl("/billing/index/view",array("eid"=>$data->id,"pid"=>$data->patient_id,"fid"=>$data->form_id))',
					),
					'pdf' =>array(
						'label'=>'Download PDF',
						'icon' =>'download-alt',
						'options'=>array(
							'target'=>'_blank',
						),
						'url' => 'Yii::app()->createUrl("/patients/form/renderPDF",array("eid"=>$data->id,"pid"=>$data->patient_id,"fid"=>$data->form_id))',
					),					
				),
				'deleteConfirmation'=>'Are you sure you want to delete this bill?',
				'updateButtonUrl'=>'"/billing/index/add/eid/".$data->id."/pid/".$data->patient_id."/fid/$data->form_id"',
			),
		));
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$entities->search('search'),
	//'filter'=>$patietnts,
	'type' => 'striped bordered condensed',
	'rowCssClassExpression'=>'$data->initial ? "info":""',	
	'columns'=>$columns
		
	/*array(
		
		
		'patient.firstname',
		'patient.lastname',
		'patient.gender',
		'patient.dob',
		
		
	)*/,
)); ?>
<script type="text/javascript">
$(document).ready(function(){
	$("input.phone").mask("(999) 999-9999");
	$("input.date").mask("99/99/9999").click(function(){
		tools.setCursor(this,0);
	});
 
});
$('.search-form form').submit(function(){
	$('.grid-view .empty').text('Loading...');
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});

</script>
