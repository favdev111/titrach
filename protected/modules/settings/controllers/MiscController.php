<?php

class MiscController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index'),
				'roles'=>array('administrator'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),			
		);
	}
	
	public function actionIndex()
	{
		$model = new SettingsForm;
		
		if(isset($_POST['SettingsForm']))
		{
			$model->attributes=$_POST['SettingsForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->save())
				Yii::app()->user->setFlash('success', 'Settings has been updated!');
		}
		$this->render('index', array('model'=>$model));
	}
}