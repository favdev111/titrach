<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'type'=>'inline',
)); ?>

	<div class="row">
	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'ss_id',array('class'=>'span2','maxlength'=>15,'placeholder'=>'Social Sec. ID')); ?>

	<?php echo $form->textFieldRow($model,'license',array('class'=>'span2','maxlength'=>45,'placeholder'=>'License')); ?>
	
	</div>
	<div class="row">	

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'phone',array('class'=>'span3','maxlength'=>45)); ?>
	
	<?php echo $form->textFieldRow($model,'sesis_id',array('class'=>'span2','maxlength'=>45,'placeholder'=>'SESIS ID')); ?>
	
	<?php echo $form->datePickerRow($model,'created',array('class'=>'span2')); ?>
	</div>
	

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
