<?php /* @var $this Controller */
function getExistingForms(){
	$items = array();
	$items[]=array('label'=>'Students Profile','url'=>array('/patients/patient/index/'),'visible'=>Yii::app()->user->checkAccess('listPatients'));
	$forms = Forms::model()->findAll('t.status=:Status AND billRelation <> 1 ',array(':Status'=>Common::STATUS_ACTIVE));
	foreach($forms as $form)
	{
		$items[] = array('label'=>$form->form_title,'url'=>array('/patients/form/list/','fid'=>$form->id));
	}
	return $items;
}

//function getBills()
//{
//	$items = array();
//	$items[]=array('label'=>'Add new Bill','icon'=>'plus','url'=>array('/patients/form/new'));
//	$forms = Forms::model()->findAll('t.status=:Status AND billRelation = 1 ',array(':Status'=>Common::STATUS_ACTIVE));
//	foreach($forms as $form)
//	{
//		$items[] = array('label'=>$form->form_title,'url'=>array('/billing/index/list/','fid'=>$form->id));
//	}
//	return $items;
//}
?>
<?php $this->beginContent('//layouts/skeleton'); ?>

<?php $this->widget('bootstrap.widgets.TbNavbar', array(
	'type'=> null, // null or 'inverse'
	'brand'=> null,
	'brandUrl'=>array('/site/index'),
	'fluid' => true,
	'collapse'=>true, // requires bootstrap-responsive.css
	'items'=>array(
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'items'=>array(

				array('label'=>'Dashboard', 'icon' => 'home','url'=>array('/site/index')),
				array('label'=>'Students','icon'=>'group','items'=>array(
					array( 
						'label' => 'Add New Student',
						'url' => array( '/patients/form/new' ),
						'icon' => 'heart',
						'visible' => in_array( Yii::app()->user->role, array( User::ROLE_MANAGER, User::ROLE_ADMIN ) ) 
					),
					array('label'=>'Browse Student', 'url'=>array('/patients/patient/index'), 'icon'=>'eye-open','linkOptions'=>array('data-toggle'=>'dropdown'),'items'=>getExistingForms()),
				)),
				array('label'=>'Billing','icon'=>'dollar','items'=>array(
					array('label'=>'Add new Bill','icon'=>'plus','url'=>array('/billing/index/add','fid'=>3)),
					array('label'=>'All','url'=>array('/billing/index/list/type/all')),
					array('label'=>'CSE','url'=>array('/billing/index/list/type/cse')),
					array('label'=>'CPSE','url'=>array('/billing/index/list/type/cpse')),
					array('label'=>'SETSS','url'=>array('/billing/index/list/type/setss')),
				)),
				array('label'=>'Calendar','url'=>array('/calendar/default/index'),'icon'=>'calendar'),
				array('label'=>'Manage Forms','icon'=>'book','url'=>array('/form/forms/index'), 'visible'=>Yii::app()->user->role == User::ROLE_ADMIN),
				array('label'=>'Settings','icon' => 'cog', 'visible'=>Yii::app()->user->role == User::ROLE_ADMIN,'items'=>array(
					array('label'=>'Users', 'icon' => 'user', 'url'=>array('/settings/user/index')),
					array('label'=>'Providers', 'icon' => 'user-md', 'url'=>array('/settings/providers/index')),
					array('label'=>'Rates', 'icon' => 'dollar', 'url'=>array('/settings/payroll/index')),
					array('label'=>'Agency', 'icon' => 'hospital', 'url'=>array('/settings/agency/index')),
					array('label'=>'Misc', 'icon' => 'list-alt', 'url'=>array('/settings/misc/index')),
					array('label'=>'Batch Bill (v2)', 'url'=>'bill/batchbill2.php','linkOptions'=>array('target'=>'_BLANK')),
					array('label'=>'Batch Bill (v3)', 'url'=>'bill/batchbill3.php','linkOptions'=>array('target'=>'_BLANK')),
					)),				
			),
		),
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
			'items'=>array(
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>Yii::app()->user->getFullName().' ('.Yii::app()->user->role.')', 'icon'=>'th-large', 'visible'=>!Yii::app()->user->isGuest, 'items'=>array(
					array('label'=>'Profile', 'icon' => 'briefcase', 'url'=>array('/settings/user/profile')),
					'---',
					array('label'=>'Logout', 'icon'=>'off', 'url'=>array('/site/logout')),
				)),
				
			),
		),
	),
)); ?>

<div id="container">
	<div id="nav_placeholder"></div>
	<div id="page" <?php echo isset($this->fullWidth) && $this->fullWidth ? 'class="full-width"': ''?>>
<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
<?php endif;		
	echo $content; ?>
	</div>
	<div id="space"></div>

</div>
<footer>
	Copyright &copy; <?php echo date('Y'); ?> by <?php echo CHtml::encode(Yii::app()->name);?><br/>
	All Rights Reserved.<br/>
</footer>
<?php $this->endContent(); ?>
