<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'pay-rate-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
)); ?>
<?php echo $form->errorSummary($model); ?>
<h4>Base Rate(s)</h4>
<div class="in-row clearfix">
    <?php
        echo $form->select2Row($model,'provider_id',array('asDropDownList'=>true,'class'=>'span3','options'=>array('placeholder'=>'--Select Provider--',),'empty'=>'','data'=>array(''=>'All') + CHtml::listData(Provider::model()->findAll(),'id','FullName')));
    ?>        
    <?php  echo $form->textFieldRow($model,'rate',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($model,'setss_1',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($model,'setss_2',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($model,'setss_3',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($model,'setss_4',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($model,'setss_5',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
</div>
<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
<h4>Additional Rate's rules</h4>
<?php if($model->isNewRecord):?>
<div class="alert alert-info">
    To add additional rate's rules please save base rates first.
</div>
<?php else:?>
<script type="text/javascript">
    var fields = <?php echo CJavaScript::jsonEncode($fields),"\n"; ?>;
    //fields = jQuery.parseJSON(fields);
    var handlers = {
		selected:{
			field: null,
			value:null,
		},
		submitRule: function(sender){
			tools.showOverlay('#rate_rules_wrap');
			var $form = jQuery(sender).parents('form');
			jQuery.post($form.attr('action'),$form.serialize(),function(data){
				jQuery('#rate_rules_wrap').html(data);
				tools.hideOverlay();
			},'html').error(function(){
				tools.hideOverlay();
			})
			return false;
		},
        fillFields: function(){
			var html = '<option value="" >-- Select field --</option>';
			for (i in fields){
				html +='<option value="'+fields[i]['id']+'" '+(fields[i]['id']==handlers.selected.field ? 'selected="selected"': '')+' >'+fields[i]['title']+'</option>';
			}
			jQuery('#rate_fields').html(html);
        },
		fillValues: function(){
			var id = jQuery('#rate_fields').val();
			if (!id) {
				return;
			}
			var values = fields[id]['values'];
			var html = '<option>-- Select value --</option>';
			for (i in values){
				html +='<option value="'+values[i]['id']+'" '+(values[i]['id']==handlers.selected.value ? 'selected="selected"': '')+' >'+values[i]['title']+'</option>';
			}
			jQuery('#rate_values').html(html);
		}
    }
</script>
<div id="rate_rules_wrap" style="position:relative; ">
<?php
    $this->actionRules($model);
?>
</div>
<?php endif; ?>	













