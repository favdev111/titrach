<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'provider-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
));

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.maskedinput-1.0.js');

?>
<?php 
$this->widget('bootstrap.widgets.TbTabs', array(
	'type'=>'tabs',
	'placement'=>'above', // 'above', 'right', 'below' or 'left',
	'id'=>'formTabs',
	'tabs'=>array(
		array('label'=>'Provider Settings', 'content' => $this->renderPartial('_form_tab', array('model' => $model,'form'=>$form), true), 'id' => 'form-tab', 'active' => true),
		array('label'=>'Caseload', 'id' => 'caseload-tab','content'=> $this->renderPartial('_caseload_tab', array('model' => $model,'form'=>$form), true)),
	),
));
?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(".phone input").mask("(999) 999-9999");
		$(".date input").mask("99/99/9999").click(function(){
			tools.setCursor(this,0);
		});
		
	   //Workaround for datepicker bug
		$(".date").focusout(function(){
		  $(this).data('date',$('input',this).val());
		  $(this).datepicker('update');
		});
	});
</script>
<?php $this->endWidget(); ?>
