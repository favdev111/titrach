<?php
$fullName = Yii::app()->user->getFullName();
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$fullName=>array('view','id'=>$model->id),
	'Update Profile',
);

?>
<h2><?php echo $fullName?>: profile</h2>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    ));
?>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
                'id'=>'user-form',
                'type'=>'horizontal',
                'enableAjaxValidation'=>false,
            )); ?>
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>			
            <fieldset>
	            <?php echo $form->textFieldRow($model,'first_name',array('class'=>'span3','maxlength'=>64)); ?>
                <?php echo $form->textFieldRow($model,'last_name',array('class'=>'span3','maxlength'=>64)); ?>
                <?php echo $form->passwordFieldRow($model,'password',array('class'=>'span3','maxlength'=>64,'labelOptions'=>array('label'=>'Password'))); ?>
                <?php echo $form->passwordFieldRow($model,'new_password',array('class'=>'span3','maxlength'=>64)); ?>
                <?php echo $form->passwordFieldRow($model,'password_confirmation',array('class'=>'span3','maxlength'=>64)); ?>
                <div class="form-actions">
                    <?php $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType'=>'submit',
                        'type'=>'primary',
                        'label'=>$model->isNewRecord ? 'Create' : 'Save',
                    )); ?>
                </div>
            </fieldset>
            <?php $this->endWidget(); ?>
