<?php
$this->breadcrumbs=array(
	'Pay Rates'=>array('index'),
	'Add New',
);

?>

<h2>Add new Pay Rate</h2>

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
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>