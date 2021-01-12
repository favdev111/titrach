<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'type'=>'search',

)); ?>
<div class="row">

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span1','maxlength'=>10)); ?>

	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span3','maxlength'=>70)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span2','maxlength'=>70)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span2','maxlength'=>70)); ?>
</div>
<div class="row">
	<?php echo $form->dropDownListRow($model,'role',User::getRoles(), array('class'=>'span2','empty'=>'--Roles--')); ?>
	
	<?php echo $form->dropDownListRow($model,'status',User::getStatuses(), array('class'=>'span2','empty'=>'--Status--')); ?>
	
	<?php echo  $form->datepickerRow($model,'created',array('class'=>'span2 to','placeholder'=>'Created')); ?>

	<?php echo  $form->datepickerRow($model,'last_login',array('class'=>'span2 to','placeholder'=>'Last Login')); ?>
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
