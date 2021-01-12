<fieldset>
 
    <legend>Relation between patient info and form fields</legend>
 	
<div class="row-fluid">
	<div class="span6">
	<?php echo $form->textFieldRow($model, "firstname", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "lastname", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "address", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "city", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "state", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "zipcode", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "phone", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php // echo $form->textFieldRow($model, "mandated_duration", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	</div>
	<div class="span6">
	<?php echo $form->textFieldRow($model, "dob", array('class'=>'typeahead','autocomplete'=>'off')); ?>	
	<?php echo $form->textFieldRow($model, "gender", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "student_id", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "parent_guardian", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "contact_person", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "contact_phone", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php echo $form->textFieldRow($model, "service_district", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	<?php // echo $form->textFieldRow($model, "mandated_frequency", array('class'=>'typeahead','autocomplete'=>'off')); ?>
	</div>
</div>

	
<script type="text/javascript">
	var handlers = {
		interval: null,
		process:null,
		query:null,
		getFields:function(){
			return $.getJSON("/form/fields/getFields", { q: handlers.query }, function (data) {
					return handlers.process(data);
				});
		}
	}
	jQuery('.typeahead').typeahead({
		source:	
	<?php if(empty(Yii::app()->params['main_form_name'])) : ?>
			function (query, process) {
					clearTimeout(handlers.interval);
					handlers.process = process;
					handlers.query = query;
					handlers.interval = setTimeout(handlers.getFields,800);
				}
	<?php else :
		echo FormFields::getFieldsAsJsArray(Yii::app()->params['main_form_id']);
		
		endif;?>,
		items: 8,
		minLength:3
	});
</script>
</fieldset>