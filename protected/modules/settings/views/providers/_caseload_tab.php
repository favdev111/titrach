	<div class="control-group ">
        <?php echo CHtml::label('Students:','s',array('class' => 'control-label')); ?>
        <div class="controls">
            <div class="groups-list infinity" >
                <?php
				echo $form->checkBoxList($model,'patients',CHtml::listData(Patient::model()->findAll(array('order'=>'lastname, firstname')),'id','fullName'));
				?>
            </div>
		</div>
    </div>