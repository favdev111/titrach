<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'type'=>'inline',

)); ?>
<div class="row">

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span1','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'firstname',array('class'=>'span3','maxlength'=>70)); ?>

	<?php echo $form->textFieldRow($model,'lastname',array('class'=>'span3','maxlength'=>70)); ?>
	
	<?php echo $form->textFieldRow($model,'student_id',array('class'=>'span2','maxlength'=>70)); ?>
	

</div>
<div class="row" style="margin-left: 0px;">
	<?php echo $form->select2Row($model,'providers',array('asDropDownList'=>true,'class'=>'span3','options'=>array('placeholder'=>'--Select Provider--',),'empty'=>'','data'=>array(''=>'All') + CHtml::listData(Provider::model()->findAll(),'id','FullName')));?>
	
	<?php echo  $form->datepickerRow($model,'dob',array('class'=>'span2 to','placeholder'=>'DOB')); ?>	

	<?php echo  $form->datepickerRow($model,'created',array('class'=>'span2 to','placeholder'=>'Created')); ?>
</div>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'small',
			'label'=>'Search',
		)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'reset',
			'type'=>'primary',
			'size'=>'small',
			'label'=>'Reset Search',
		)); ?>		
	</div>


<?php $this->endWidget(); ?>
