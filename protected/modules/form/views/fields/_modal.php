<?php
/* @var $this FormFieldsController */
/* @var $model FormFields */
/* @var $form CActiveForm */
$heading = $model->isNewRecord ? 'Create Field' : 'Update Field '.$model->title;
?>
<div id="modal_dlg" class="modal fade wide">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo $heading ?></h4>
	</div>
 
	<div class="modal-body">
		<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>		
	</div><!-- //.modal-body -->
	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'button',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
			'htmlOptions'=>array(
				'onclick'=>'handlers.formFieldSubmit(this);return false;',
			),
		)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Close',
			'url'=>'#',
			'htmlOptions'=>array('data-dismiss'=>'modal'),
		)); ?>
	</div>
 
<script type="text/javascript">
	var $dlg = jQuery('#modal_dlg');
	$dlg.modal({show:true});
	$dlg.on('hidden',function(){
		jQuery('#modal_dlg').remove();
		if(system.need_page_refresh){
			tools.reloadCurrentTab('#pages-tab');
			system.need_page_refresh = false;
		}
		delete $dlg;
	});
</script> 
</div>		