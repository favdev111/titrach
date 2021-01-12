<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'type'=>'inline',
)); ?>

	<div class="row">
<?php
        echo $form->select2Row($model,'provider_id',array('asDropDownList'=>true,'class'=>'span3','options'=>array('placeholder'=>'--Select Provider--',),'empty'=>'','data'=>array(''=>'All') + CHtml::listData(Provider::model()->findAll(),'id','FullName')));
?>        
	</div>


	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
