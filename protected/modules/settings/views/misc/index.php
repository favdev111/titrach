<?php
$this->breadcrumbs=array(
	'Settings',
);
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

<h1>Settings</h1>

<?php 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'setings',
    'type'=>'horizontal',
)); ?>
 
<?php $this->widget('bootstrap.widgets.TbTabs', array(
    'tabs'=>array(
		array('label'=>'General','active'=>true,'id'=>'general-tab','content'=>$this->renderPartial('_generalTab',array('model'=>$model,'form'=>$form),true)),
		array('label'=>'Relations with form fields','id'=>'relation-tab','content'=>$this->renderPartial('_relationTab',array('model'=>$model,'form'=>$form),true)),
		array('label'=>'Default Rates','id'=>'rates-tab','content'=>$this->renderPartial('_ratesTab',array('model'=>$model,'form'=>$form),true))
	),
)); ?>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Save')); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>