<?php
$this->breadcrumbs=array(
	'Forms'=>array('index'),
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

?>

<h1>Manage Forms</h1>

<div class="search-form compacted"></div>
<div class="pull-right">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new form',
			'type'=>'success',
			'buttonType'=>'link',
			'url'=> array( '/form/forms/create' ),
		));
	?>
</div>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'forms-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'id',
		'form_name',
		'form_title',
		array(
			  'name'=>'status',
			  'value'=>'Common::getStatusString($data->status)'
			  ),
		array(
			'name'=>'billRealtion',
			'header'=>'Is Bill',
			'value'=>'Common::getYesNoString($data->billRelation)'
		),
		array(
			'name'=>'is_printable',
			'header'=>'Printable',
			'value'=>'Common::getYesNoString($data->is_printable)'
		),		
		/*'file_storage_path',
		'form_prefix',
		
		'parent',
		'save_to_directory',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'buttons'=>array(
				'view' => array(
					'label' => 'Update DB View',
					'icon'=>'refresh',
					'url' => 'Yii::app()->createUrl("form/forms/updateView/id/".$data->id)'
				),

			),
			'template'=>'{update}{delete}{view}',
		),
	),
)); ?>
