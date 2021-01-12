		<div class="form">
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'form-fields-form',
		'enableAjaxValidation'=>false,
		'type'=>'horizontal',
		'action'=>$model->isNewRecord ? $this->createUrl('create',array('form'=>$model->form_id,'page'=>$model->form_page_id)) : $this->createUrl('update',array('id'=>$model->id)) 
	)); ?>			
			<?php echo $form->errorSummary($model); 
			$this->widget('bootstrap.widgets.TbAlert', array(
					'block'=>true, // display a larger alert block?
					'fade'=>true, // use transitions?
					'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
					'alerts'=>array( // configurations per alert type
						'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
						'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
					),
			));
?>
			<ul class="nav nav-tabs" id="tabs"> 
				<li class="active"><a href="#field" data-toggle="tab">Field</a></li>
				<li><a href="#values" data-toggle="tab">Values</a></li>
				<li><a href="#rules" data-toggle="tab">Validation Rules</a></li>
			</ul>
			<div class="tab-content">
				<div id="field" class="tab-pane active">
					<p class="note">Fields with <span class="required">*</span> are required.</p>					
						<?php echo $form->dropDownListRow($model,'form_id',Forms::getForms(),array('empty'=>'-Select Form-'));?>
			
						<?php echo $form->dropDownListRow($model,'form_page_id',FormsPages::getFormPages($model->form_id),array('empty'=>'-Select Form Page-'));?>
						
						<?php echo $form->textFieldRow($model,'name',array('maxlength'=>255, 'class'=>'span4')); ?>
			
						<?php echo $form->textFieldRow($model,'title',array('maxlength'=>255, 'class'=>'span4')); ?>
					
						<?php echo $form->textFieldRow($model,'sort_order',array('maxlength'=>10,'class'=>'span2')); ?>
				
						<?php echo $form->dropDownListRow($model,'type',FormFields::getFieldsType());?>

						<?php echo $form->textFieldRow($model,'class',array('maxlength'=>40)); ?>

						<div id="model_meta" style="<?php echo $model->type !== FormFields::TYPE_model ? 'display: none;' : ''; ?>" class="control-group">
							<div class="control-label">Model's meta data:</div>
							<div class="controls">
								<?php echo $form->textFieldRow($model,'meta_info[class_name]',array('maxlength'=>25, 'class'=>'span2'))?>
								<?php echo $form->textFieldRow($model,'meta_info[class_field_value]',array('maxlength'=>25, 'class'=>'span2'))?>
								<?php echo $form->textFieldRow($model,'meta_info[class_field_label]',array('maxlength'=>25, 'class'=>'span2'))?>	
								<?php echo $form->dropDownListRow($model,'meta_info[class_field_controls]',array('autocomplete'=>'autocomplete','dropdown'=>'dropdown','select2'=>'select2'),array('class'=>'span2'))?>	
								<?php echo $form->textFieldRow($model,'meta_info[class_search_fields]',array('maxlength'=>75, 'class'=>'span2'))?>	
							<p class="clearfix">
								<small><em><strong>Tip:</strong> you can input several fields for search, just separate it via coma</em></small>
							</p>
							</div>
						</div>
						<?php echo $form->textAreaRow($model,'html_attrs',array('rows'=>3,'class'=>'span5')); ?>
						
						<?php echo $form->textFieldRow($model, "related_on", array('class'=>'typeahead','autocomplete'=>'off')); ?>
						
						<?php echo $form->dropDownListRow($model,'status',Common::getStatuses());?>
						
						<?php echo $form->dropDownListRow($model,'is_searchable',Common::getYesNo(),array('class'=>'span2'));?>
						
						<?php echo $form->dropDownListRow($model,'is_browsable',Common::getYesNo(), array('class'=>'span2'));?>
						
						<?php echo $form->textFieldRow($model,'browse_order',array('maxlength'=>10,'class'=>'span2')); ?>						
				</div><!--#field-->
			
				<div id="values" class="tab-pane">
		<?php
				$val_cnt = count($model->fieldsValues);
				$i = 0;
				foreach((array)$model->fieldsValues as $key=>$val){
					$i++;
		?>			<div class="form-row">
						<i class="icon-resize-vertical"></i>
						<input type="hidden" value="<?php echo $val->id?>" name="FieldsValues[<?php echo $i ?>][id]" />
						<b>Value:</b><input type="text" name="FieldsValues[<?php echo $i ?>][value]" value="<?php echo $val->form_field_value?>" />
						<b>Title:</b><input type="text" name="FieldsValues[<?php echo $i ?>][title]" value="<?php echo $val->form_field_title?>" />
						<b>Default:</b><input type="radio" name="FieldsValues[default]" value="<?php echo $i?>" <?php echo $val->is_default ? 'checked="checked"' : ''?>/>
						<?php if($i<$val_cnt):?>
							<a class="delete-value" href="#" title="Delete" rel="tooltip" ><i class="icon-trash"></i></a>
						<?php else:?>
							<a class="add-value" href="#" title="Add" rel="tooltip"><i class="icon-plus"></i></a>
						<?php endif;?>
					</div>
		<?php
				}
		?>
				</div><!--#values-->
				<div id="rules" class="tab-pane">
		<?php $rules = $model->getRulesIdAsArray();?>                    
					<div class="form-row">
						<label for="rule_required"><input type="checkbox" name="Rules[]" value="required" id="rule_required" <?php echo (in_array('required',$rules) ? 'checked="checked"' :'')?> /> Required</label>
					</div>
					<div class="form-row">
						<label for="rule_alphanumeric"><input type="checkbox" name="Rules[]" value="alphanumeric" id="rule_alphanumeric"  <?php echo (in_array('alphanumeric',$rules) ? 'checked="checked"' :'')?> /> Alphanumeric</label>
					</div>
					<div class="form-row">
						<label for="rule_numeric"><input type="checkbox" name="Rules[]" value="numeric" id="rule_numeric" <?php echo (in_array('numeric',$rules) ? 'checked="checked"' :'')?> /> Numeric</label>
					</div>
					<div class="form-row">
						<label for="rule_range"><input type="checkbox" name="Rules[]" value="range" id="rule_range" <?php echo (in_array('range',$rules) ? 'checked="checked"' :'')?> /> In range</label>
					</div>                    
				</div><!--#rules-->
			</div><!-- //.tab-content -->
	<?php $this->endWidget(); ?>			
		</div><!-- form -->


<script type="text/javascript">
var fieldValues_count = <?php echo $model->isNewRecord ? 0 : $val_cnt?>;
var onlyOneValue = false;
$(function(){
	var handlers = {
		show_values:function(){
			tools.enableTab($('#tabs'),1);
			if($.trim($("#values").html()) ==''){
				$("#values").html(tpl.parse(tpl.row,{index:fieldValues_count}));
				if(onlyOneValue){
						$("#values .add-value").remove();
				}
			}
			this.changeRangeRuleState(true);
		},
		hide_values:function(){
			tools.disableTab($('#tabs'),1);
			fieldValues_count = 0;
			onlyOneValue = false;
			$("#values").empty();
            this.changeRangeRuleState(false);
		},
		add_value_row:function(sender){
			fieldValues_count++;
			var row = $(sender).parents('div.form-row').after(tpl.parse(tpl.row,{index:fieldValues_count}));
			$(sender).removeClass('add-value').addClass('delete-value').find('i').attr('class','icon-trash');
            $('#values').sortable('refresh');
		},
		updatePages:function(){
			var p_s = $('#FormFields_form_page_id');
			p_s.parents('.row').removeClass('hidden');
			$('<img class="loader" src="<?php echo Yii::app()->request->getBaseUrl(true)?>/images/loader.gif" />').insertAfter(p_s);
			$.getJSON('<?php echo Yii::app()->createUrl('/formsPages/getPages')?>',{fid:$('#FormFields_form_id').val()},function(data){
				if($.isPlainObject(data)){
					var html = '';
					for(i in data){
						html +='<option value="'+i+'">'+data[i]+'</option>';
					}
					p_s.html(html);
				}
			p_s.next('img.loader').remove();
			});
		},
		checkValue:function(sender){
			var val = $(sender).val();
			if(val=="checkbox" || val=="radio" || val=="select" ){
				this.show_values();
			}else if(val=="model"){
				this.show_model(true);
				return false;
			}
			else if(val=="hidden"){
				onlyOneValue = true;
				this.show_values();
			}else{
				this.hide_values();
			}
			this.show_model(false);
		},
		changeRangeRuleState:function(active){
			if(active){
				$("#rule_range").attr("disabled",false);                                                
			}else{
				 $("#rule_range").attr("disabled",true);                                                
			}
		},
		updateAddButton:function(){
			var list = $('#values');
			$('a.add-value',list).removeClass('add-value').addClass('delete-value').find('i').attr('class','icon-trash').attr('title','Delete');
			$('.form-row:last a.delete-value',list).replaceWith(tpl.add_value);
		},
		show_model:function(show){
			if(show)
				$('#model_meta').show();
			else
				$('#model_meta').hide();
		}
		
	}
	tpl['row'] = '<div class="form-row">\
				  <i class="icon-resize-vertical"></i>\
				  <input type="hidden" value="" name="FieldsValues[{{index}}][id]" /><b>Value:</b><input type="text" name="FieldsValues[{{index}}][value]"> <b>Title:</b><input type="text" name="FieldsValues[{{index}}][title]"> <b>Default:</b><input type="radio" name="FieldsValues[default]" value="{{index}}">\
				  <a class="add-value" title="Add Value" href="#"><i class="icon-plus"></i></a>\
			</div>';
	tpl['add_value'] = '<a class="add-value" title="Add" href="#"><i class="icon-plus"></i></a>'
	
        
	//Rules checkbox behavior
	$('#rule_alphanumeric').change(function(){
		if($(this).is(':checked')) $('#rule_numeric').removeAttr('checked');
	});
	$('#rule_numeric').change(function(){
		if($(this).is(':checked')) $('#rule_alphanumeric').removeAttr('checked');
	});
        
	$('#FormFields_form_id').change(function(){
		handlers.updatePages();
	});
	$('#FormFields_type').change(function(){
		handlers.checkValue(this);
	});
	
	if(fieldValues_count>0){
		$("#tabs").tab();
        handlers.changeRangeRuleState(true);
    }
	else{
		$("#tabs").tab();
		tools.disableTab("#tabs",1);
        handlers.changeRangeRuleState(false);
	}
	//Make field values sortable
	jQuery('#values').sortable({
		items:'div.form-row',
		stop:function(event,ui){
			handlers.updateAddButton();
		}
	});
        
	$('.modal-body .form').delegate('a.add-value','click',function(){
		handlers.add_value_row(this);
                return false;
	});
	$('.modal-body .form').delegate('a.delete-value','click',function(){
		fieldValues_count--;
		var row = $(this).parents('div.form-row:first').remove();
		$('#values').sortable('refresh');                    
		return false;
	});
	
	jQuery('a[rel="tooltip"]',jQuery('#modal_dlg')).tooltip().on('show', function(e) {e.stopPropagation();}).on('hidden', function(e) {e.stopPropagation();});

	
	jQuery('.typeahead').typeahead({
		source:	
		<?php
			echo FormFields::getFieldsAsJsArray($model->form->id);
		?>,
		items: 8,
		minLength:3
	});	
})


</script>

