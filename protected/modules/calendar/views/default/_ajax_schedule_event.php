<?php if(!$error): ?>
<div id="modal_dlg" class="modal fade wide">
<style>
	.datepicker{
		z-index: 9999;
	}
</style>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4>Schedule new event for <?php echo $patient->getFullName(); ?> </h4>
	</div>
 
	<div class="modal-body">
<?php endif;?>		
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'visit-physician-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->dropDownListRow($model,'provider_id',CHtml::listData(Provider::model()->findAll(),'id','FullName'),array('empty'=>'-Select provider-','class'=>'span3')); ?>

	<div class="control-group">
<?php 		$err = $model->hasErrors('event-date') ? ' error' : ''; ?>
		<label class="control-label<?php echo $err;?>">Event Date</label>
		<div class="controls">
		<?php
		$this->widget('bootstrap.widgets.TbDatepicker', array(
								'htmlOptions'=>array(
									'name'=>'event-date',
									'class'=>'span2'.$err,
									'id'=>'id_event_date',
								),
								'value'=>isset($_POST['event-date']) ? $_POST['event-date'] : '',
							));
		if($err)
			echo '<span class="help-inline error">Event date cannot be blank.</span>';
		?>
		</div>
	</div>
	<div class="control-group">
<?php 		$err = $model->hasErrors('event-time') ? ' error' : ''; ?>
		<label class="control-label<?php echo $err;?>">Event Time</label>
		<div class="controls">
		<?php $this->widget('bootstrap.widgets.TbTimePicker', array(
								'htmlOptions'=>array(
									'name'=>'event-time',
									'class'=>'span2'.$err,
									'id'=>'id_event_time',
								),
								'value'=>isset($_POST['event-time']) ? $_POST['event-time'] : '',
							));
		if($err)
			echo '<span class="help-inline error">Event time cannot be blank.</span>';
		?>
		</div>
	</div>

	<div class="control-group">
<?php 		$err = $model->hasErrors('event-duration') ? ' error' : ''; ?>		
		<label class="control-label<?php echo $err;?>">Event Duration</label>
		<div class="controls">
		<?php echo CHtml::dropDownList('event-duration',( isset($_POST['event-duration']) ? $_POST['event-duration'] : '' ),Event::getEventDurations(),array('class'=>$err));
		if($err)
			echo '<span class="help-inline error">Event duration cannot be blank.</span>';		
		?>
		</div>
	</div>	
	
	<div class="control-group">
		<label class="control-label">
		<input type="checkbox" id="enable_rec" name="enable-reccurence" value="1" <?php echo isset($_POST['enable-reccurence']) ? 'checked="checked"' : ''?>>
			Enable Reccurence
		</label>
		<div class="reccurance_options controls" style="display: none;">
			<label><input name="rec" value="next day" checked="" type="radio"> every day</label>
			<label><input name="rec" value="next weekday" type="radio"> every weekday</label>
			<label><input name="rec" value="next <?php echo date('l');?>" type="radio"> every <span id="every_week_day"><?php echo date('l');?></span></label>
			<label><input name="rec" value="+1 year" type="radio"> every <span id="every_month_day"><?php echo date('j M');?></span></label>
			<label><input name="rec" value="+1 month" type="radio"> every <span id="every_day_in_month"> <?php echo date('j');?></span>&nbsp;of the month</label>
		</div>
	</div>
	
	<?php echo $form->hiddenField($model,'patient_id');?>
	<?php
		if(!$model->isNewRecord){
			echo $form->hiddenField($model,'id');
		}
	?>

	

<?php $this->endWidget(); ?>
<script type="text/javascript">
	jQuery(function ($){
		jQuery('#id_event_date + .add-on').click(function(){
			$('#id_event_date').trigger('click');
			return false;
		})
		jQuery('#id_event_date').click(function(){
			$(this).datepicker({'format':'mm/dd/yyyy','autoclose':'true','weekStart':0})
			.on('changeDate',function(ev){handlers.updateReccurence(ev)});
			$(this).datepicker("show");
			});
		jQuery('#id_event_time').click(function(){
			$(this).timepicker();
			$(this).timepicker("show");
			});
		$('#enable_rec').on('change', function(){
			$('.reccurance_options').slideToggle();
		})
	});
	

</script> 	
<?php if(!$error):?>

	</div><!-- //.modal-body -->
	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'button',
			'type'=>'primary',
			'label'=>'Schedule',
			'htmlOptions'=>array(
				'onclick'=>'handlers.submitEvent(this);return false;',
			),
		)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Close',
			'url'=>'#',
			'htmlOptions'=>array('data-dismiss'=>'modal'),
		)); ?>
	
	</div>
 
<script  type="text/javascript">
	//jQuery('#modal_scheduled_date').datepicker("show");
		var $dlg = jQuery('#modal_dlg');
		$dlg.modal({show:true});
		$dlg.on('hidden',function(){
			jQuery('#modal_dlg').remove();
			delete $dlg;
		});	
</script>

</div>
<?php endif;?>


