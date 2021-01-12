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
	$newPage = new FormsPages();
	$newPage->form_id  = $model->id;
	$tabs = array();
	$tabs[] = array(
		'label' =>'Add new',
		'id'=>'page-0',
		'content'=>$this->renderPartial('/pages/create',array('model'=>$newPage),true),
		'active'=>true
	);
	foreach((array)$model->formsPages as $page)
	{
		$tabs[]= array(
			'label' =>$page->title,
			'id'=>'page-'.$page->id,
		);
	}

	$this->widget('bootstrap.widgets.TbTabs', array(
		'type'=>'tabs',
		'id'=>'pages-tab',
		'placement'=>'left', // 'above', 'right', 'below' or 'left'
		'tabs'=>$tabs,
		'htmlOptions'=>array('class'=>'clearfix'),
		'events'=>array('shown'=>'js:loadPageContent')
	));
?>