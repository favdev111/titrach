<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
?>



<div id="login">
	
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array('class'=>'form-signin'),
	//'type'=>'horizontal',
)); ?>

	<fieldset class="row-fluid">
		<legend><h2>Login</h2></legend>
		<?php echo $form->textFieldRow($model,'username', array('class'=>'span12')); ?>
		<?php echo $form->passwordFieldRow($model,'password', array('class'=>'span12')); ?>
		<?php echo $form->checkboxRow($model,'rememberMe'); ?>

		<?php $this->widget(	'bootstrap.widgets.TbButton', 
								array(
									'buttonType'=>'submit', 
									'label'=>'Login'
								)); ?>
	</fieldset>
<?php $this->endWidget(); ?>
</div><!-- form -->
