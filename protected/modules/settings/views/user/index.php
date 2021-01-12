<?php
$this->breadcrumbs=array(
	'Settings'=>'#',
	'Users',
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
$('.btn[type=\"reset\"]').click(function(){
	setTimeout(function(){\$('.search-form form').submit() },100);
});

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Users</h1>

<div class="search-form compacted">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div class="pull-right">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Add new user',
			'type'=>'primary',
			'size'=>'small', 
			'buttonType'=>'link',
			'url'=> array( '/settings/user/create' ),
		));
	?>
</div>
<?php


$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'type' => 'striped bordered condensed',
	'columns'=>array(
		'id',
		'first_name',
		'last_name',
		'email',
		array('name' => 'role',
		      'filter' => User::getRoles(),
		      ),
		array('name' => 'status',
		      'filter' => User::getStatuses(),
		      ),
		array('name'=>'last_login',
				'filter'=>$this->widget('bootstrap.widgets.TbDatepicker', array(
					'name'=>'User[last_login]',
					'value'=>isset($model->last_login) ? $model->last_login : '',
					'htmlOptions'=>array('class'=>'span1')
				),true)
		),
		/*'created',
		/*,
		'default_form',
		'allowed_forms',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
)); ?>
