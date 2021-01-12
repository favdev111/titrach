<?php

class PagesController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'ajaxOnly',
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','create','update'),
				'roles'=>array('manager'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$result = array();
		$model=new FormsPages;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['FormsPages']))
		{
			$model->attributes=$_POST['FormsPages'];
			if($model->save()){
				Yii::app()->user->setFlash('success', 'Form page '.$model->name.' had been added!');				
				echo CJSON::encode(array('status'=>'success'));
				Yii::app()->end();	
			}
		}
		$html = $this->renderPartial('create',array(
					'model'=>$model,
				),true);
		$result = array('status'=>'invalid', 'html'=>$html);
		echo CJSON::encode($result);
		Yii::app()->end();	
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id,array('formFields'=>array('order'=>'formFields.sort_order')));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['FormsPages']))
		{
			$result = array();
			$model->attributes=$_POST['FormsPages'];
			if($model->save()){
				Yii::app()->user->setFlash('success', 'Form page '.$model->name.' had been updated!');
				$result['status'] = 'success';
			}else{
				$result['status'] = 'invalid';
			}
			$result['html'] = $this->renderPartial('update',array(
						'model'=>$model,
					),true);
			$result['mode']='update';
			echo CJSON::encode($result);
			Yii::app()->end();				
		}

		$this->renderPartial('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new FormsPages('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['FormsPages']))
			$model->attributes=$_GET['FormsPages'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id,$with = false)
	{
		if($with)
		{
			$model=FormsPages::model()->with($with)->findByPk($id);		
		}else{
			$model=FormsPages::model()->findByPk($id);	
		}
		
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='forms-pages-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
