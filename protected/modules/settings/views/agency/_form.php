<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'agency-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span3','maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'address',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'city',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'state',array('class'=>'span2','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'zip',array('class'=>'span1','maxlength'=>12)); ?>

	<?php echo $form->textFieldRow($model,'tax_id',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'phone',array('class'=>'span2','maxlength'=>12)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
