<?php
$this->breadcrumbs=array(
	'Mandates for '.$patient->getFullName()=>array('index','pid'=>$patient->id),
	'Manage',
);

?>

<h1>Manage Mandates for <?php echo $patient->getFullName(); ?></h1>
<div class="clearfix">
	<div class="pull-right">
		<?php
			$this->widget('bootstrap.widgets.TbButton', array(
				'label'=>'Add new mandate',
				'type'=>'success',
				'buttonType'=>'link',
				'url'=> array( '/patients/mandate/create/pid/'.$patient->id ),
			));
		?>
	</div>
</div>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'patients-mandates-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'id',
		array(
			'name'=>'service_type',
			'value'=>'FieldsValues::model()->findByPk($data->service_type)->form_field_title',
		),
		'frequency',
		'duration',
		array(
			'name'=>'split',
			'value'=>'Common::getYesNoString($data->split)',
		),
		'type',
		'recommended_count',
		array(
			'name'=>'created',
			'value'=>'Yii::app()->dateFormatter->formatDateTime($data->created, "medium", "")',
		),
		/*'updated',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
)); ?>
