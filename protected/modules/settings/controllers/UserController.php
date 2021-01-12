<?php

class UserController extends Controller
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
			array('allow',
				  'actions'=>array('profile'),
				  'users'=>array('@')),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','view','create','update','delete'),
				'roles'=>array('administrator'),
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
		$model=new User('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				Yii::app()->user->setFlash('success', 'New user has been added!');
				$this->redirect(array('index'));
			}
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
		$model->scenario  = 'edit';
        $tmp_pswd = $model->password;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
        {
			$model->attributes=$_POST['User'];
            if($model->validate())
            {
                if(trim($model->password)!=''){
                    $model->password = $model->hashPassword($model->password,$model->salt);
                }else{
                    $model->password = $tmp_pswd;
                }
                if($model->save(false)){
					Yii::app()->user->setFlash('success', 'User '.$model->getFullName().' has been updated!');
                    $this->redirect(array('index'));
	            }
            }
        }
        $model->password= null;
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
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	

	public function actionProfile()
	{
		$model=$this->loadModel(Yii::app()->user->id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->scenario  = 'profile';
		
        $tmp_pswd = $model->password;

		if(isset($_POST['User']))
        {
			$model->attributes=$_POST['User'];
			if($model->validate())
            {
                if($model->hashPassword($model->password,$model->salt) !== $tmp_pswd){
					$model->addError('password','Invalid old password!');
				}else{
					if(!empty($model->new_password))
						$model->password = $model->hashPassword(trim($model->new_password),$model->salt);
					else
						$model->password = $tmp_pswd;
					if($model->save(false))
					{
						Yii::app()->user->updateModel();
						Messaging::FlashSuccess('User profile has been updated!');
					}else{
						Messaging::FlashError('Error during update user profile!');
					}
				}
			}
        }
        $model->password= null;
		$model->new_password  = null;
		$model->password_confirmation = null;
		$this->render('profile',array(
			'model'=>$model,
		));
		
	}	
	

	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
