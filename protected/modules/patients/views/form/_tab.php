<fieldset>
	<legend> Step <?php echo $current;?> of <?php echo $steps_count;?> </legend>
        <h4><?php echo $page->title;?></h4>
        <input type="hidden" name="page_id" value="<?php echo $page->id;?>" />
<?php   foreach($page->formFields as $field){
			$fld_name= $model->form_name.'['.$field->name.']';
?>
				<div class="control-group<?php echo $field->type=='hidden' ? ' hidden' : '';?>">
                <label for="<?php echo $field->name?>" class="control-label"><?php echo $field->title?></label>
				<div class="controls <?php echo trim($field->class) ? $field->class:''?>">
<?php 			switch($field->type){
					case 'text':
                        $value = strpos($field->html_attrs,'value')===false ?  $entity ? 'value="'.(!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: '').'"': '' :'';
						$html = '<input type="text" id="id_'.$field->name.'" '.$value.' name="'.$fld_name.'" '.(trim($field->html_attrs) ? $field->html_attrs:'').' />';
						echo $html;
						break;
					case 'hidden':
						$html = '<input type="hidden" id="id_'.$field->name.'" name="'.$fld_name.'" value="'.array_shift($field->fieldsValues)->form_field_value.'"/>';
						echo $html;
						break;
					case 'model':
						
						//$value = strpos($field->html_attrs,'value')===false ?  $entity ? (!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: '') : '' :'';
						if(!class_exists($field->meta_info['class_name']))
							break;
						$class = new $field->meta_info['class_name'];
						$value = null;
						if(isset($for_model_fields[$field->meta_info['class_name']]))
						{
							$value = $for_model_fields[$field->meta_info['class_name']];
						}else{
							$value = strpos($field->html_attrs,'value')===false ?  $entity ? (!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: '') : '' :'';
						}
						switch($field->meta_info['class_field_controls'])
						{
							case 'select2':
								$this->widget('bootstrap.widgets.TbSelect2', array(
									'htmlOptions'=>array(
										'name'=>$fld_name,
										'class'=>'span9',
										'id'=>'id_'.$field->name,
									),
									'value'=>$value,
									'data'=>CHtml::listData($class->findAll(),$field->meta_info['class_field_value'],$field->meta_info['class_field_label'])								
								));
								break;
							case 'autocomplete':

								if($value)
								{
									$this->widget('bootstrap.widgets.TbSelect2', array(
									'htmlOptions'=>array(
										'name'=>$fld_name,
										'class'=>'span9',
										'id'=>'id_'.$field->name,
										'readonly'=>'readonly'
									),
									'value'=>$value,
									'asDropDownList'=>true,
									'data'=>CHtml::listData($class->findAll($field->meta_info['class_field_value'].'=:id',array(':id'=>$value)),$field->meta_info['class_field_value'],$field->meta_info['class_field_label'])								
								));
								}else{
								$this->widget('bootstrap.widgets.TbSelect2', array(
									'htmlOptions'=>array(
										'name'=>$fld_name,
										'class'=>'span9',
										'id'=>'id_'.$field->name,
									),
									'asDropDownList'=>false,
									'options'=>array(
										'minimumInputLength'=>1,
										'ajax'=>'js:
										{
											url:"'.$this->createUrl('ajaxList',array('class'=>$field->meta_info['class_name'],'val'=>$field->meta_info['class_field_value'],'label'=>$field->meta_info['class_field_label'],'sf'=>$field->meta_info['class_search_fields'])).'",
											data:function(term,page){return {q:term,page_limit:10}},
											results:function(data,page){return {results:data}}
										}
										',
									),
									'value'=>$value,
									'data'=>CHtml::listData($class->findAll(),$field->meta_info['class_field_value'],$field->meta_info['class_field_label'])								
								));
								}
								break;
							case 'dropdown':
								break;
							default:
						}
					break;
					case 'timepicker':
						$value = strpos($field->html_attrs,'value')===false ?  $entity ? (!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: '') : '' :'';
						$this->widget('bootstrap.widgets.TbTimePicker', array(
							'htmlOptions'=>array(
								'name'=>$fld_name,
								'class'=>'span6',
								'id'=>'id_'.$field->name,
								'readonly'=>Yii::app()->user->role !== User::ROLE_DOCTOR ? false : isset($_GET['eid']) ?  true : false ,
							),
							'value'=>$value,
						));
						break;
					case 'datepicker':
						$value = strpos($field->html_attrs,'value')===false ?  $entity ? (!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: '') : '' :'';
						$this->widget('bootstrap.widgets.TbDatepicker', array(
							'htmlOptions'=>array(
								'name'=>$fld_name,
								'class'=>'span6',
								'id'=>'id_'.$field->name,
								'readonly'=>Yii::app()->user->role !== User::ROLE_DOCTOR ? false : isset($_GET['eid'])  ?  true : false ,
								//'data-date-format'=>'mm/dd/yyyy'
							),
							'value'=>$value
						));
						break;					
					case 'textarea':
                        $value = strpos($field->html_attrs,'value')===false ?  $entity ?(!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: ''): '' :'';
						$html = '<textarea id="id_'.$field->name.'" name="'.$fld_name.'">'.$value.'</textarea>';
						echo $html;
						break;
					case 'select':
						$html = '';
                        $fv_a= $entity ? !empty($entity->formsEntitiesFields[$field->name]) ? (array) $entity->formsEntitiesFields[$field->name]->getValuesAsArray() : array() : array();
						foreach($field->fieldsValues as $val){
							$isSelected = in_array($val->form_field_value,$fv_a) || $val->is_default ;
							if($val->form_field_value == FieldsValues::SELECT_VALUE_TYPE_BEGING_OF_GROUP){
								$html .='<optgroup label="'.$val->form_field_title.'">';
							}elseif($val->form_field_value ==FieldsValues::SELECT_VALUE_TYPE_END_OF_GROUP){
								$html .='</optgroup>';
							}else{
								$html .= '<option value="'.$val->id.'" '.($isSelected ? 'selected="selected"':'').'>'.$val->form_field_title.'</option>';
							}
						}
						if($html)
							$html ='<select id="id_'.$field->name.'" name="'.$fld_name.'" '.(count($fv_a)>1 ? 'multiple="multiple"':'').' >'.(!$isSelected ? '<option value="">--Select--</option>' : '').$html.'</select>';
						echo $html;
						break;
					case 'radio':
						$html ='';
						$inline = '';
						if(strpos($field->class,'inline')!==false)
							$inline = 'inline';
						$fv_a= $entity ? !empty($entity->formsEntitiesFields[$field->name]) ? (array) $entity->formsEntitiesFields[$field->name]->getValuesAsArray() : array() : array();
						foreach($field->fieldsValues as $i=>$val){
							$html .= '<label class="radio '.$inline.'"><input type="radio" value="'.$val->id.'" id="id_'.$field->name.$i.'" name="'.$fld_name.'" '.($val->is_default || in_array($val->form_field_value,$fv_a)  ? 'checked="checked"':'').' /><label for="id_'.$field->name.$i.'">'.$val->form_field_title.'</label></label>';
						}
						echo $html;
						break;
					case 'checkbox':
						$html ='';
						
						if(!empty($field->related_on))
						{
							//Visible on certain value from "related on" field
							if(strpos($field->related_on,':visible_on:')!==false)
							{
								list($related_on,$on_value) = explode(':visible_on:',$field->related_on);
								$html .='<div id="'.$field->name.'_placeholder">';
								foreach($field->fieldsValues as $i=>$val){
									if($val->form_field_value == FieldsValues::SELECT_VALUE_TYPE_BEGING_OF_GROUP)
									{
										$html .= '<h5>'.$val->form_field_title.'</h5>';
										continue;
									}
									$value = false;
									$vls = array();
									if($entity){
										if(!empty($entity->formsEntitiesFields[$field->name]))
										{
											$f_vals = $entity->formsEntitiesFields[$field->name]->getValuesAsArray();
											foreach((array)$f_vals as $key=>$e_val){
												$vls[]=$e_val;
												$value = $val->form_field_value == $e_val;
												if($value) break;
											}
										}
									}
									$inline = '';
									if(strpos($field->class,'inline')!==false)
										$inline = 'inline';
									$html .= '<label class="checkbox '.$inline.'"><input type="checkbox" value="'.$val->id.'" id="id_'.$field->name.$i.'" name="'.$fld_name.'[]"  '.(($val->is_default && count($vls) == 0 ) || $value ? 'checked="checked"':'').' /><label for="id_'.$field->name.$i.'">'.$val->form_field_title.'</label></label>';
								}
								$html .='</div>'."\n";
								echo $html;
?>
<script type="text/javascript">
jQuery(function($){
	$("#id_<?php echo trim($related_on); ?>").on('change',function(){
		var $place = $('#<?php echo $field->name.'_placeholder'?>');
		//$place.find(':checked').removeAttr('checked');
		$place.parents('.control-group').hide();
		var val = $(':selected, :checked',this).text();
		if(val == <?php echo CJavaScript::encode($on_value) ?>)
		{
			$place.parents('.control-group').show(); 
		}
	});
	$("#id_<?php echo trim($related_on); ?>").trigger('change');
})	
</script>
<?php
								break;								
							}
							//End of visible on certain value
							
							//Begin of related list
							$rel_vals = array();
							foreach($field->fieldsValues as $i=>$val)
							{
								$value = false;
								$vls = array();
								if($entity){
									if(!empty($entity->formsEntitiesFields[$field->name]))
									{
										$f_vals = $entity->formsEntitiesFields[$field->name]->getValuesAsArray();
										foreach((array)$f_vals as $key=>$e_val){
											$vls[]=$e_val;
											$value = $val->form_field_value == $e_val;
											if($value) break;
										}
									}
								}
								list($index,$v)  = explode(FieldsValues::SEPARATOR, $val->form_field_value,2);
								if($index && $v)
									$rel_vals[$index][] ='<label class="checkbox "><input type="checkbox" value="'.$val->id.'" id="id_'.$field->name.$i.'" name="'.$fld_name.'[]"  '.(($val->is_default && count($vls) == 0 ) || $value ? 'checked="checked"':'').' /><label for="id_'.$field->name.$i.'">'.$val->form_field_title.'</label></label>';
							}
?>
<div id="<?php echo $field->name.'_placeholder'?>"></div>
<script type="text/javascript">
	var <?php echo $field->name?>_values = <?php echo CJavaScript::encode($rel_vals);?>;
	
jQuery(function($){
	$("#id_<?php echo $field->related_on; ?>").on('change',function(){
		var $place = $('#<?php echo $field->name.'_placeholder'?>');
		$place.empty().parents('.control-group').hide();
		var val = $(':selected, :checked',this).text();
		if(val in <?php echo $field->name?>_values)
		{
			$place.html(<?php echo $field->name?>_values[val]).parents('.control-group').show(); 
		}
	});
	$("#id_<?php echo $field->related_on; ?>").trigger('change');
})	
</script>
<?php
						//End of related list
						}else
							foreach($field->fieldsValues as $i=>$val){
								$value = false;
								$vls = array();
								if($entity){
									if(!empty($entity->formsEntitiesFields[$field->name]))
									{
										$f_vals = $entity->formsEntitiesFields[$field->name]->getValuesAsArray();
										foreach((array)$f_vals as $key=>$e_val){
											$vls[]=$e_val;
											$value = $val->form_field_value == $e_val;
											if($value) break;
										}
									}
								}
								$inline = '';
								if(strpos($field->class,'inline')!==false)
									$inline = 'inline';
								$html .= '<label class="checkbox '.$inline.'"><input type="checkbox" value="'.$val->id.'" id="id_'.$field->name.$i.'" name="'.$fld_name.'[]"  '.(($val->is_default && count($vls) == 0 ) || $value ? 'checked="checked"':'').' /><label for="id_'.$field->name.$i.'">'.$val->form_field_title.'</label></label>';
							}
						echo $html;
						break;
					case 'mandate':
						$mandate_id = $entity ? (!empty($entity->formsEntitiesFields[$field->name]) ? $entity->formsEntitiesFields[$field->name]->value: null) : null ;
						$values = array();
						if($mandate_id)
						{
							$values = CHtml::listData(PatientsMandates::model()->findAll(array(
														'condition'=>'patient_id IN (SELECT patient_id from patients_mandates where id = :id) OR patient_id = :pid',
														'params'=>array(':id'=>(int)$mandate_id,':pid'=>$entity->patient_id),
														)), 'id','Caption');
						}
				
						echo CHtml::dropDownList($fld_name,$mandate_id,$values,array('empty'=>'- Select -','id'=>'id_'.$field->name,'class'=>'span12'));
?>
<script type="text/javascript">
	jQuery(function($){
		var $mandateS = $('#id_<?php echo $field->name; ?>')
		$('#id_speech_session_student').on('change',function(){
			$.post('<?php echo $this->createUrl('/patients/mandate/getMandatesForPatient'); ?>',{pid:$(this).val()},function(data){
				if(data.indexOf('Exception')> 0){
					alert(data);
				}else{
					$mandateS.html(data);
				}
			},'html').error(function(){alert('Error during student mandates from server!')});
		})
<?php
	if(!$mandate_id): ?>
		$('#id_speech_session_student').trigger('change');
<?php
	endif;
?>		
	});
</script>
<?php
					break;					
					default:
						break;
				}
				?>
				</div>
				</div><!-- //.control-group-->
            <?}?>
				<div class="form-actions">
<?php
	$buttons = array();
	if($current>1)
	{
		$buttons[] = array(
					'label'=>'Back',
					'icon'=>'arrow-left',
					'htmlOptions'=>array(
						'onclick'=>'handlers.prevStep(this);return false;'
					));
	}
	if($current == $steps_count) //$entity || 
	{		$buttons[] = array(
					'label'=>'Save',
					'icon'=>'ok',
					'buttonType'=>'submit',
					'htmlOptions'=>array(
						//'onclick'=>'handlers.saveChanges(this);return false;'
					));
	}
    if( $current < $steps_count) //$current==1 ||
	{
		$buttons[] = array(
					'label'=>'Next',
					'icon'=>'arrow-right',
					'htmlOptions'=>array(
						'onclick'=>'handlers.nextStep(this);return false;'
					));
		
	}
?>

<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>$buttons,
)); ?>

				</div><!-- //.form_actions -->	
			
</fieldset>
