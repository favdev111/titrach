<?php
$this->breadcrumbs=array(
	'Browse all students'
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

<h1>Students</h1>

<div class="search-form compacted" style="display:block">
<?php $this->renderPartial('_search',array(
	'model'=>$patients,
)); ?>

	<div class="pull-right" style="margin-top: -55px;margin-right: 15px;">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Export to excel',
			'type'=>'info',
			'icon'=>'download white',
			'buttonType'=>'link',
			'htmlOptions' => array('onclick'=>"var params = $(this).parents('div').prev('form').serialize(); handlers.ExportXLS(params);return false;"),
			'url'=>'/patients/patient/exportXls',
		));
	
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new student',
			'type'=>'success',
			'icon'=>'plus white',
			'buttonType'=>'link',
			'url'=> array( '/patients/form/new' ),
		));
	?>
	</div>
</div><!-- search-form -->

<?php

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$patients->search(),
	//'filter'=>$patietnts,
	'type' => 'striped bordered condensed',
	'columns'=>array(
/*		array(
			'name' =>'id',
			'type' => 'raw',
			'header' => 'ID',
			'value' =>'"std_".$data->id'
		),*/
		'student_id',
		'lastname',
		'firstname',
		array(
			'name'=>'address',
			'type'=>'raw',
			'header'=>'Address',
			'value'=>'$data->getFullAddress()',
		),
		'gender',
		'dob',
		'service_district',
		array(
			'name'=>'created',
			'type'=>'raw',
			'header'=>'Created',
			'value' => function (Patient $data) {
				return Yii::app()->dateFormatter->formatDateTime($data->created, 'medium', '');
			},			
		),
		/*array(
			'name'=>'add',
			'type'=>'raw',
			'header'=>'',
			'value'=>array($this,'renderPatientRowButtons'),
		),*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>Yii::app()->user->role !== User::ROLE_DOCTOR ? '{event} {files} {history} {view} {pdf} {update} {mandate} {delete}' : '{files} {history} {view} {pdf} {update} {mandate} {delete}',
			'buttons'=>array(
				'history' => array(
					'label' => 'View student services history',
					'icon'=>'book',
					'url' => 'Yii::app()->createUrl("patients/patient/history/pid/".$data->id)'
				),
				'files' => array(
					'label'=>'Student\'s files',
					'icon' => 'hdd',
					'url' => 'Yii::app()->createUrl("patients/patient/files/pid/".$data->id)'
				),
				'view' =>array(
					'label'=>'View template',
					'icon' =>'eye-open',
					'url' => 'Yii::app()->createUrl("patients/form/view",array("eid"=>$data->getInitialFormID(),"pid"=>$data->id,"fid"=>Yii::app()->params["main_form_id"]))',
				),
				'pdf' =>array(
					'label'=>'Download PDF',
					'icon' =>'download-alt',
					'options'=>array(
						'target'=>'_blank',
					),
						'visible'=>'false',
					'url' => 'Yii::app()->createUrl("patients/form/renderPDF",array("eid"=>$data->getInitialFormID(),"pid"=>$data->id,"fid"=>Yii::app()->params["main_form_id"]))',
				),
				'event'=>array(
					'label'=>'Schedule event',
					'icon'=>'calendar',
					'url'=>'Yii::app()->createUrl("calendar/default/schedule",array("pid"=>$data->id))',
					'options'=>array('onclick'=>'handlers.openModal(this);return false;'),
				),
				'mandate' => array(
					'label'=>'Manage mandates',
					'icon'=>'group',
					'url'=>'Yii::app()->createUrl("patients/mandate/index/pid/".$data->id)'
				),
				
			),			
			'deleteConfirmation'=>'Are you sure you want to delete this patient? All patients sessions, forms and bills will be deleted too.',
			'updateButtonUrl'=> 'Yii::app()->createUrl( "/patients/form/edit", array( "eid" => $data->getInitialFormID(), "pid" => $data->id, "fid" => Yii::app()->params["main_form_id"] ) )'
		),
	),
)); ?>

<div id="modal_placeholder"></div>
 <script type="text/javascript">

	var handlers = {
		openModal: function(sender){
			$sender = $(sender);
			tools.showOverlay('#page');
			$('#modal_placeholder').load($sender.attr('href'),{},function(){
				tools.hideOverlay();
			}).error(function(){ alert('Error during request'); tools.hideOverlay();});
		},
		updateReccurence: function(ev){
			var time = ev.date;
			var day_of_month = time.getDate();
			var weekday = $.fn.datepicker.dates.en.days[time.getDay()];
			$('#every_week_day').text(weekday).parents('label').find('input').val('next '+weekday);
			$('#every_day_in_month').text(day_of_month).parents('label').find('input');
			$("#every_month_day").text($.fn.datepicker.dates.en.monthsShort[time.getMonth()] + " " + day_of_month ).parents('label').find('input');
		},
		submitEvent:function(sender){
			var $sender = $(sender);
			var $modal = $sender.parents("#modal_dlg").find('.modal-body');
			var $form  = $modal.find('form');
			var param = $form.serialize();
			tools.showOverlay($modal);
			$.post($form.attr('action'),param,function(data){
				$modal.html(data)
			},'html').error(function(){alert("Error during scheduling event!"); tools.hideOverlay()});			
		},
		ExportXLS:function(params){
			$.post('<?php echo $this->createUrl('exportXls')?>',params,function(response){
					if(response.status)
					{
						alert('Students has been successfully exported.');
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