<?php
$this->breadcrumbs=array(
	'Pay Rates'=>array('index'),
	'Manage',
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


Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rates-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Pay Rates</h1>


<div class="search-form compacted">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<div class="pull-right">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new rate',
			'type'=>'success',
			'buttonType'=>'link',
			'icon'=>'money',
			'url'=> array( '/settings/payroll/add' ),
		));
	?>
</div>
</div><!-- search-form -->


<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'rates-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'id',
		'provider.first_name',
		'provider.last_name',
		'rate',
		'setss_1',
		'setss_2',
		'setss_3',
		'setss_4',
		'setss_5',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{pay} {update}{delete}',
			'updateButtonUrl'=>'Yii::app()->createUrl("/settings/payroll/edit",array("id"=>$data->id))',
			'buttons'=>array(
				'pay'=>array(
					'icon'=>'money',
					'url'=>'Yii::app()->createUrl("/settings/payroll/calculate",array("prid"=>$data->provider_id))',
					'label'=>'Calc payment'
				)
			),
		),
	),
)); ?>
