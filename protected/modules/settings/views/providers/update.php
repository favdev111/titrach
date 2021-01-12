<?php
$this->breadcrumbs=array(
	'Providers'=>array('index'),
	$model->getFullName()=>array('update','id'=>$model->id),
	'Update',
);

?>

<h2>Update Provider <?php echo $model->getFullName(); ?></h2>

<?php
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
<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>