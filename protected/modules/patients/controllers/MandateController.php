<?php

class MandateController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'ajaxOnly + getMandatesForPatient'
		);
	}

    public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('getMandatesForPatient'),
				'roles'=>array('doctor'),
			),
			array('allow', 
				'actions'=>array('index','update','create','delete'),
				'roles'=>array('manager'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionCreate($pid)
	{
		$patient = $this->_loadPatient($pid);
		
		$model=new PatientsMandates;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->patient_id = $patient->id;
		if(isset($_POST['PatientsMandates']))
		{
			$model->attributes=$_POST['PatientsMandates'];
			if($model->save())
				$this->redirect(array('index','pid'=>$patient->id));
		}

		$this->render('create',array(
			'model'=>$model,
			'patient'=>$patient
		));
	}

	public function actionUpdate($id)
	{
		$model=$this->_loadModel($id);
		$patient = $this->_loadPatient($model->patient_id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PatientsMandates']))
		{
			$model->attributes=$_POST['PatientsMandates'];
			if($model->save())
				$this->redirect(array('update','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
			'patient'=>$patient
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->_loadModel($id)->delete();

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
	public function actionIndex($pid)
	{
		$patient = $this->_loadPatient($pid);
		$model=new PatientsMandates('search');
		$model->unsetAttributes();  // clear any default values
		$model->patient_id = $patient->id;
		if(isset($_GET['PatientsMandates']))
			$model->attributes=$_GET['PatientsMandates'];

		$this->render('index',array(
			'model'=>$model,
			'patient'=>$patient
		));
	}

	public function actionGetMandatesForPatient()
	{
		$pid = Yii::app()->request->getPost('pid',false);
		if(!$pid)
			throw new CException('Invalid Student ID');
		
		$mandates =  CHtml::listData(PatientsMandates::model()->findAll(array(
														'condition'=>'patient_id = :id',
														'params'=>array(':id'=>(int)$pid),
														)),'id','Caption');
		if(count($mandates) > 0)
		{
			$empty = '- Select mandate -';
		}else{
			$empty = ' - No available mandate -';
		}
		$options = array('empty'=>$empty);
		echo CHtml::listOptions(null,$mandates,$options);
		Yii::app()->end();
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	private function _loadModel($id)
	{
		$model=PatientsMandates::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	private function _loadPatient($id)
	{
		$model=Patient::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='patients-mandates-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

}
