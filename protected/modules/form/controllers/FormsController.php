<?php

class FormsController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'ajaxOnly + pagesTab'
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
				'actions'=>array('index','create','update','pagesTab'),
				'roles'=>array('manager'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
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
		$model=new Forms;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Forms']))
		{
			$model->attributes=$_POST['Forms'];
			if(isset($_POST['FormMeta']))
			{
				$metas = array();
				foreach($_POST['FormMeta'] as $m)
				{
					if(!empty($m['name']) && !empty($m['value']))
						$metas[$m['name']] = $m['value'];
				}
				$model->meta = $metas;
			}
			if($model->save()){
				Yii::app()->user->setFlash('success', 'Form '.$model->form_title.' has been added!');	
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
		$model=$this->loadModel($id,array('formsPages.formFields'=>array('order'=>'formsPages.sort_order')));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Forms']))
		{
			$model->attributes=$_POST['Forms'];
			if(isset($_POST['FormMeta']))
			{
				$metas = array();
				foreach($_POST['FormMeta'] as $m)
				{
					if(!empty($m['name']) && !empty($m['value']))
						$metas[$m['name']] = $m['value'];
				}
				$model->meta = $metas;
			}		
			if($model->save()){
				Messaging::FlashSuccess('Form '.$model->form_title.' has been updated!');				
				$this->redirect(array('index'));
			}
		}
	
		//$this->widget('bootstrap.widgets.TbGridView')->registerClientScript();
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
		$model=new Forms('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Forms']))
			$model->attributes=$_GET['Forms'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	public function actionUpdateView($id)
	{
		$model=$this->loadModel($id,array('formsPages.formFields'=>array('order'=>'formsPages.sort_order')));
		$model->regenerateView();
	}
	/**
	 * Async functions
	 */
	
	
	public function actionPagesTab($id)
	{
		$model=$this->loadModel($id,array('formsPages.formFields'));
		
		$this->renderPartial('_pages_tab',array('model'=>$model));
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
			$model=Forms::model()->with($with)->findByPk($id);
		}else
			$model=Forms::model()->findByPk($id);	
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='forms-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
