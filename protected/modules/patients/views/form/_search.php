<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'type'=>'search',

)); ?>
<div class="row">
	
	<?php
		foreach($forms->formFields as $field)
		{
			if($field->is_searchable)
			{
				switch($field->type)
				{
					case FormFields::TYPE_checkbox:
					case FormFields::TYPE_radio:
					case FormFields::TYPE_select:?>
				<div style="display:inline-block; float: none; margin-left: 0; vertical-align: top" class="span3">
			<?php
				echo $form->select2Row($entity,'formsEntitiesFields['.$field->id.']',array('asDropDownList'=>true,'class'=>'span3','options'=>array('placeholder'=>'--Select '.$field->title.'--',),'empty'=>'','multiple'=>'true','data'=>Forms::getFormFieldValuesAsArray($forms->id,$field->name,'id')));
			?>
				</div>					
<?php
					break;
					case FormFields::TYPE_datepicker:
						echo $form->datepickerRow($entity,'formsEntitiesFields['.$field->id.'][date_from]',array('class'=>'span2','placeholder'=>$field->title.' from','labelOptions'=>array('label'=>false)));
						echo $form->datepickerRow($entity,'formsEntitiesFields['.$field->id.'][date_to]',array('class'=>'span2','placeholder'=>$field->title.' to','labelOptions'=>array('label'=>false)));
					break;
					case FormFields::TYPE_timepicker:
						echo $form->timepickerRow($entity,'formsEntitiesFields['.$field->id.']',array('class'=>'span2','placeholder'=>$field->title,'options'=>array('defaultTime'=>false),'labelOptions'=>array('label'=>false)));
					break;
					case FormFields::TYPE_model:
						$class = new $field->meta_info['class_name'];
						echo $form->select2Row($entity,
							'formsEntitiesFields['.$field->id.']',				   
							array('asDropDownList'=>true,'class'=>'span3','options'=>array('placeholder'=>'--Select '.$field->title.'--',),'empty'=>'',
								  'data'=>array(''=>'All') + CHtml::listData($class->findAll(),$field->meta_info['class_field_value'],$field->meta_info['class_field_label'])	
						));
					break;
					default:
						echo CHtml::textField(
						'FormsEntities[formsEntitiesFields]['.$field->id.']',
						isset($entity->formsEntitiesFields[$field->id]) ? $entity->formsEntitiesFields[$field->id] : '',
						array('class'=>'span3 '.$field->class,'placeholder'=>$field->title)
					);	
				}
			}
		}
	?>
	<div class="span3" style="display:inline-block; float: none; margin-left: 0; vertical-align: top">
	<?php echo CHtml::checkBox('show_only_related',$show_only_related); ?> <label for="show_only_related" class="checkbox" style="display: inline"> Show only own records</label>
	</div>
</div>
<!--<div class="row">
	<?php //echo  $form->datepickerRow($model,'dob',array('class'=>'span2 to','placeholder'=>'DOB')); ?>	

	<?php //echo  $form->datepickerRow($model,'created',array('class'=>'span2 to','placeholder'=>'Created')); ?>
</div>-->

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'small',
			'label'=>'Search',
		)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'reset',
			'type'=>'primary',
			'size'=>'small',
			'label'=>'Reset Search',
		)); ?>		
	</div>


<?php $this->endWidget(); ?>

<script type="text/javascript">
	$('#show_only_related').change(function(){
		if($(this).is(':checked')){
			$.cookie('show_only_related', 1);
		}else{
			$.cookie('show_only_related', 0);
		};
	});
</script>






