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
echo $this->renderPartial('_form',array('model'=>$model,'url'=>'/form/pages/update/id/'.$model->id));
?>

<h3>
<?php $this->widget('bootstrap.widgets.TbButton', array(
		'label'=>'Add Field',
		'type'=>'primary', 
		'size'=>'small',
		'url'=>'/form/fields/create/form/'.$model->form_id.'/page/'.$model->id,
		'htmlOptions'=>array(
			'onClick'=>'handlers.openModal(this); return false;',
			'class'=>'pull-right'
		),
	)); ?>
	Form Fields</h3>
<?php
if(count($model->formFields)==0):?>
<p class="text-warning" style="text-align: center">No field exist yet. Use button above to add new field.</p>
<?php
else:?>
<?php 
	$this->widget('bootstrap.widgets.TbGridView', array(
		'type'=>'striped bordered condensed',
		'id'=>'fields-grid',
		'dataProvider'=>$model->getFieldsAsArrayDataProvider(),
		'template'=>"{items}",
		'columns'=>array(
		    array('name'=>'id', 'header'=>'#'),
		    array('name'=>'name', 'header'=>'Name'),
		    array('name'=>'title', 'header'=>'Title'),
		    array('name'=>'type', 'header'=>'Type'),
			array('name'=>'status', 'header'=>'Status'),
			array('name'=>'is_searchable', 'header'=>'Search.','htmlOptions'=>array('class'=>'text-center')),
			array('name'=>'is_browsable', 'header'=>'Brows.','htmlOptions'=>array('class'=>'text-center')),
		    array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'htmlOptions'=>array('style'=>'width: 62px','class'=>'text-center button-column'),
				'buttons'=>array(
					'update'=>array(
						'url'=>'Yii::app()->createUrl("form/fields/update/id/".$data["id"])',
						'options'=>array('onclick'=>'handlers.openModal(this);return false;')
					),
					//Workaround for old php version.
					'delete'=>array(
						'url'=>'Yii::app()->createUrl("form/fields/delete/id/".$data["id"])',
					),
				),
				'template'=>'{update} {delete}',

			),
		),
	));
?>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
	var $active_tab = $('#page-tab .tab-pane.active');
	jQuery('a[rel="tooltip"]',$active_tab).tooltip();
	jQuery('a[rel="popover"]',$active_tab).popover();
});
/*]]>*/
</script>	
<?php endif;?>
