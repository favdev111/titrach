<fieldset>
        <h4><?php echo $page->title;?></h4>
        <input type="hidden" name="page_id" value="<?php echo $page->id;?>" />
<?php   foreach($page->formFields as $field){
			$fld_name= $model->form_name.'['.$field->name.']';
?>
				<div class="control-group<?php echo $field->type=='hidden' ? ' hide' : '';?>">
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
								if($field->name === "bill_agency") {
									$dataField = array("3" => "Tifrach Learning Inc.");					
													
								} else {
									$dataField = CHtml::listData($class->findAll(),$field->meta_info['class_field_value'],$field->meta_info['class_field_label']);
								}
								$this->widget('bootstrap.widgets.TbSelect2', array(
									'htmlOptions'=>array(
										'name'=>$fld_name,
										'class'=>'span9',
										'id'=>'id_'.$field->name,
									),
									'value'=>$value,
									'data'=>$dataField,	
									//'data'=>array("3" => "Tifrach Learning Inc."),

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
								'id'=>'id_'.$field->name
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
														'condition'=>'patient_id IN (SELECT patient_id from patients_mandates where id = :id)',
														'params'=>array(':id'=>(int)$mandate_id),
														)), 'id','Caption');
						}
						
						echo CHtml::dropDownList($fld_name,$mandate_id,$values,array('empty'=>'- Select -','id'=>'id_'.$field->name,'class'=>'span12'));
?>
<script type="text/javascript">
	jQuery(function($){
		var $mandateS = $('#id_<?php echo $field->name; ?>')
		$('#id_bill_patient').on('change',function(){
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
		$('#id_bill_patient').trigger('change');
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
            <?php } ?>

</fieldset>
