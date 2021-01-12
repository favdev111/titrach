<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'ss_id',array('class'=>'span3','maxlength'=>15)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'phone',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'address',array('class'=>'span3','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'city',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->textFieldRow($model,'state',array('class'=>'span2','maxlength'=>45)); ?>

	<?php echo $form->datepickerRow($model,'dob',array('class'=>'span2 date')); ?>

	<?php echo $form->textFieldRow($model,'zip',array('class'=>'span1 zip','maxlength'=>12)); ?>
    
    <?php echo $form->textFieldRow($model,'license',array('class'=>'span3','maxlength'=>45)); ?>

    <?php echo $form->textFieldRow($model,'sesis_id',array('class'=>'span3','maxlength'=>45)); ?>

    <?php echo $form->textFieldRow($model,'sesis_password',array('class'=>'span3','maxlength'=>128)); ?>    