<?php
$this->breadcrumbs=array(
	'Providers'=>array('index'),
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
	$.fn.yiiGridView.update('provider-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Providers</h1>


<div class="search-form compacted">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<div class="pull-right">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new provider',
			'type'=>'success',
			'buttonType'=>'link',
			'url'=> array( '/settings/providers/create' ),
		));
	?>
</div>
</div><!-- search-form -->


<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'provider-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'id',
		'first_name',
		'last_name',
		array(
			'name'=>'ss_id',
			'header'=>'Soc.Sec. ID'
		),
		array(
			'name'=>'address',
			'header'=>'Address',
			'value'=>'$data->getFullAddress()'
		),
		'email',
		'phone',
		'license',
		'sesis_password',
		'sesis_id',
		array(
			'name'=>'dob',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{pay} {update}{delete}',
			'buttons'=>array(
				'pay'=>array(
					'icon'=>'money',
					'url'=>'Yii::app()->createUrl("/settings/payroll/calculate",array("prid"=>$data->id))',
					'label'=>'Calc payment'
				)
			),			
		),
	),
)); ?>
