<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'patients-mandates-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->hiddenField($model,'patient_id'); ?>

	<?php echo $form->dropDownListRow($model,'service_type',CHtml::listData(FieldsValues::model()->findAll(array('condition'=>'form_field_id='.Yii::app()->params['services_field_id'])),'id','form_field_title'),array('class'=>'span4'));?>

	<?php echo $form->textFieldRow($model,'frequency',array('class'=>'span2')); ?>

	<?php echo $form->textFieldRow($model,'duration',array('class'=>'span2')); ?>
	
	<?php echo $form->dropDownListRow($model,'split',Common::getYesNo(),array('class'=>'span2'));?>
	
	<?php echo $form->dropDownListRow($model,'type',PatientsMandates::getTypes(),array('class'=>'span3'));?>

	<?php echo $form->textFieldRow($model,'recommended_count',array('class'=>'span1 individual-hide')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
<script type="text/javascript">
	jQuery(function($){
		$('#<?php echo CHtml::activeId($model, 'type'); ?>').on('change',function(){
			var input = $('.individual-hide');
			if ($(this).val()=='individual') {
				input.parents('.control-group').slideUp('fast');
				input.val(0);
			}else{
				input.parents('.control-group').slideDown('fast');
			}
		}).trigger('change');
	});
</script>
