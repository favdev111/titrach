<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
));

$forms = Forms::getForms();
?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span4','maxlength'=>70)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span4','maxlength'=>70)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span4','maxlength'=>70)); ?>

	<?php echo $form->passwordFieldRow($model,'password',array('class'=>'span3','maxlength'=>64)); ?>

	<?php echo $form->dropDownListRow($model,'role',User::getRoles());?>

	<?php echo $form->dropDownListRow($model,'default_form',array_merge(array('0'=>'--None--') ,$forms));?>

	<?php echo $form->checkBoxListRow($model,'allowed_forms',$forms); ?>
		
	<div class="control-group ">
        <?php echo CHtml::label('Related Providers:','related_providers',array('class' => 'control-label','required'=>true)); ?>
        <div class="controls">
            <div class="groups-list" >
                <?php				
				echo $form->checkBoxList($model,'related_providers',CHtml::listData(Provider::model()->findAll(),'id','fullName'));
				?>
            </div>
		</div>
    </div>


	<?php echo $form->dropDownListRow($model,'status',User::getStatuses(), array('class'=>'span2' )); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
