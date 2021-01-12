<?php

class ProvidersController extends Controller
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','delete','index'),
				'roles'=>array('manager'),
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
		$model=new Provider;

		if($this->_save($model))
		{
			Messaging::FlashSuccess('Provider were succesefully created');
			$this->redirect(array('index'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$model->patients = array_keys($model->patients);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if($this->_save($model))
		{
				Messaging::FlashSuccess('Provider were succesefully updated');
		}

		$this->render('update',array(
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
		$model=new Provider('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Provider']))
			$model->attributes=$_GET['Provider'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	
	private function _save(&$model)
	{
		if(isset($_POST['Provider']))
		{
			$trans = Yii::app()->db->beginTransaction();
			try{
			$model->attributes=$_POST['Provider'];
			$model->saveWith = array('patients');
			if($model->save())
			{
				$trans->commit();
				return true;
			}
			Messaging::FlashError('Error during save data!');
			$trans->rollback();
			}catch(Exception $e){
				$trans->rollback();
				throw $e;
			}	
		}
		return false;
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Provider::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='provider-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
