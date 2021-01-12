<?php
$this->breadcrumbs=array(
	'Agencies'=>array('index'),
	'Manage',
);


Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('agency-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Agencies</h1>


<div class="search-form compacted" style="">
<?php

/*$this->renderPartial('_search',array(
	'model'=>$model,
));*/ ?>
<div class="pull-right">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new agency',
			'type'=>'success',
			'buttonType'=>'link',
			'url'=> array( '/settings/agency/create' ),
		));
	?>
</div>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'agency-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'name',
		array(
			'name'=>'address',
			'header'=>'Address',
			'value'=>'$data->getFullAddress()',
		),
		'tax_id',
		'email',
		'phone',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
)); ?>
