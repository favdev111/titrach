<fieldset>
 
    <legend>General Settings</legend>
 
	<?php echo $form->textFieldRow($model, "main_form_id"); ?>
	<?php echo $form->textFieldRow($model, "adminEmail"); ?>
	<?php echo $form->textFieldRow($model, "file_storage"); ?>
	<?php echo $form->textFieldRow($model, "tmp_file_storage"); ?>
</fieldset>
<fieldset>
 
    <legend>View Settings</legend>
 
	<?php echo $form->textFieldRow($model, "rows_per_page"); ?>
</fieldset>