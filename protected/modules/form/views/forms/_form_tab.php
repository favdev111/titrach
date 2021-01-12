<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'forms-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
));

echo $form->errorSummary($model);
?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>
<div class="clearfix">
	<div class="span6">
	<h3>General settings:</h3>
		<?php echo $form->textFieldRow($model,'form_name',array('class'=>'span3','maxlength'=>255)); ?>
	
		<?php echo $form->textFieldRow($model,'form_title',array('class'=>'span3','maxlength'=>255)); ?>
		
		<?php echo $form->textFieldRow($model,'form_program_title',array('class'=>'span3','maxlength'=>255)); ?>
	
		<?php //echo $form->textFieldRow($model,'file_storage_path',array('class'=>'span5','maxlength'=>255)); ?>
	
		<?php echo $form->dropDownListRow($model,'parent',array('--None--') + Forms::getForms());?>
	
		<?php echo $form->checkBoxRow($model,'save_to_directory');?>
	
		<?php echo $form->dropDownListRow($model,'status',Common::getStatuses());?>

		<?php echo $form->checkBoxRow($model,'billRelation');?>
		<?php echo $form->checkBoxRow($model,'is_printable');?>
	</div>
	<div class="span6">
		<h3>Meta settings:</h3>
	<table class="value-list table table-striped">
		<thead><tr><th>Name</th><th>Value</th><th></th></tr></thead>
		<tbody>
<?php
	$i = 0;
	foreach((array)$model->meta as $k=>$v): $i++;?>
		<tr>
			<td><input type="text" value="<?php echo $k; ?>" name="FormMeta[<?php echo $i;?>][name]" class="span3"></td>
			<td><input type="text" value="<?php echo $v; ?>" name="FormMeta[<?php echo $i;?>][value]" class="span3"></td>
			<td><a href="#" class="delete-value" onclick="handlers.deleteValueRow(this,'form_metas');"><i class="icon-trash"></i></a></td>
		</tr>
<?php
	endforeach;
?>
		</tbody>
<tfoot><tr><td colspan="3">
		<div class='pull-right'>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'button',
			'type'=>'info',
			'label'=>'Add Value',
			'htmlOptions'=>array(
				'onclick'=>'handlers.addValueRow(this,"form_meta_row","form_metas")'
			)
		)); ?></div></td></tr></tfoot>		
	</table><!-- //.options-list -->

	</div>	
</div>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	counters['form_metas'] = <?php echo $i; ?>;
	tpl["form_meta_row"] = '<tr><td><input type="text" name="FormMeta[{{index}}][name]" class="span3"></td>\
							<td><input type="text" name="FormMeta[{{index}}][value]" class="span3"></td>\
							<td><a href="#" class="delete-value" onclick="handlers.deleteValueRow(this,\'form_metas\');"><i class="icon-trash"></i></a></td></tr>';
</script>