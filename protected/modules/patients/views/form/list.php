<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.maskedinput-1.0.js');

$breadcrumbs = array();
if($p_entity)
{
	$breadcrumbs[$p_entity->form->form_title] = $this->createUrl('form/list',array('fid'=>$form->parent));
}
$breadcrumbs[]=$form->form_title;

$this->breadcrumbs=$breadcrumbs;

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
$('.btn[type=\"reset\"]').click(function(){
	$('.search-form form input,.search-form form select').each(function(){
		$(this).val('');
	});
	
	setTimeout(function(){\$('.search-form form').submit() },100);
	return false;
});

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Browse <?php echo $form->form_title;?>s<?php echo $p_entity ? ' for '.$p_entity->form->getEntityRelatedTitle($p_entity) : ''?></h2>
<div class="search-form compacted one-row" style="display:block">
<?php $this->renderPartial('_search',array(
	'entity'=>$entities,
	'forms'=>$form,
	'show_only_related'=>$show_only_related
)); ?>
	<div class="pull-right" style="margin-top: -55px;margin-right: 15px;">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Export to excel',
			'type'=>'info',
			'icon'=>'download white',
			'buttonType'=>'link',
			'htmlOptions' => array('onclick'=>"var params = $(this).parents('.search-form').find('form').serialize(); handlers.ExportXLS(params);return false;"),
			'url'=>'/patients/form/exportXls',
		));
	?>
	</div>
</div><!-- search-form -->
<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form compacted" style="display:none">
<?php /*$this->renderPartial('_historySearch',array(
	'entity'=>$form,
	'patient'=>$patient,
)); */?>
</div><!-- search-form -->
<?php if($form->id == Yii::app()->params['main_form_id']): ?>
<p style="margin-bottom: -20px;">
	<b>Note:</b> Rows with blue background contains initial records.
</p>
<?php endif;
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
				'header' => 'F.ID',
				'value' =>'"f_".$data->id'
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
				'name'=>'timestamp',
				'type'=>'raw',
				'header'=>'Date added',
				'value' => function (FormsEntities $data) {
					return Yii::app()->dateFormatter->formatDateTime($data->timestamp, 'medium', '');
				},				
				//'value'=>'date("m/d/Y h:m",$data->timestamp)'
			),
			array(
				'name'=>'add',
				'type'=>'raw',
				'header'=>'',
				'value'=>array($this,'renderFormEntitiesListRowButtons'),
			),		
			array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'template'=>'{update} {view} {pdf} {delete}',
				'buttons' =>array(
					'update'=>array(
						'visible'=>function($i,$data)
						{
							if($data->form_id==2)
							{
								return $data->canEdit();
							}else{
								return true;
							}
						}
					),
					'delete'=>array(
						'visible'=>function($i,$data)
						{
							if($data->form_id==2)
							{
								return $data->canEdit();
							}else{
								return true;
							}
						}
					),					
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
						'visible'=>'$data->form->is_printable',
						'url' => 'Yii::app()->createUrl("patients/form/renderPDF",array("eid"=>$data->id,"pid"=>$data->patient_id,"fid"=>$data->form_id))',
					),
				),
				'afterDelete'=> 'function(th,success,data){$("body").append(data)}',
				'deleteConfirmation'=>'Are you sure you want to delete this record? All related data will be deleted too.',
				'updateButtonUrl'=> 'Yii::app()->createUrl( "/patients/form/edit", array( "eid"=>$data->id, "pid"=>$data->patient_id, "fid"=>$data->form_id) )',
			),
		));

//var_dump($data->formsEntitiesFields[22]);

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
var handlers = {
	ExportXLS:function(par){
			$.post('<?php
			$params['fid'] = $form->id;
			if($p_entity)
				$params['peid'] = $entities->parent;
			$params['pid'] = $pid;
			echo $this->createUrl('exportXls',$params);?>',par,function(response){
					if(response.status)
					{
						alert('<?php echo mb_convert_case($form->form_title, MB_CASE_TITLE, 'UTF-8');?> has been successfully exported.');
						window.open('/public/export/'+response.filename);
					}
					else
					{
						if("message" in response)
							alert(response.message);
						else
							alert('Some errors were occurred during export.');
					}
					//tools.hideOverlay();
				},'json');
			return false;
		}		
}
</script>