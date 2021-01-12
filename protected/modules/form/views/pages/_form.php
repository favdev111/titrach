<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'forms-pages-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->hiddenField($model,'form_id'); ?>
	
	<?php echo $form->textFieldRow($model,'name',array('class'=>'span3','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span3','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'sort_order',array('class'=>'span1','maxlength'=>10)); ?>

	<?php echo $form->dropDownListRow($model,'status',Common::getStatuses());?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'htmlOptions'=>array(
				'onclick'=>'handlers.formPageSubmit(this);return false;',
				'formaction'=>$url,					
			),
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
