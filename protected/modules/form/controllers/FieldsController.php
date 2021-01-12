<?php

class FieldsController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'ajaxOnly',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('index','view','create','update','getFields'),
				'roles'=>array('doctor'),
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
	public function actionCreate($form = false,$page = false)
	{
		$model=new FormFields;
		if($form) $model->form_id = (int)$form;
		if($page) $model->form_page_id = (int)$page;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$this->_save($model);
		
		$this->renderPartial('create',array(
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
		$model=$this->loadModel($id,array('fieldsValues'=>array('order'=>'fieldsValues.sort_order ASC '),'formFieldsValidationRules'));

		if(!$model->related_on)
		{
			$model->related_on = '';
		}
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);


		$this->_save($model);

		$this->renderPartial('update',array(
				'model'=>$model,
		));			
	}

	protected function _save($model){
		if(isset($_POST['FormFields'])){
			$model->attributes=$_POST['FormFields'];
			if(isset($_POST['FieldsValues'])){
				$def = isset($_POST['FieldsValues']['default']) ?  (int)$_POST['FieldsValues']['default'] : null;
				unset($_POST['FieldsValues']['default']);
				
				$order=0;
				foreach($_POST['FieldsValues'] as $key=>$val){
					$order++;
					if(!$val['id'])
					{
						$val_obj = new FieldsValues();
					}else{
						if(!$val_obj = $model->getValuesObjByID($val['id']))
						{
							$val_obj = new FieldsValues();
						}
					}
					$val_obj->sort_order = $order;
					$val_obj->form_field_value = trim($val['value']);
					$val_obj->form_field_title = trim($val['title']);
					$val_obj->is_default = $def==$key;
					$model->field_values[] = $val_obj;
				}
				$model->fieldsValues = $model->field_values;

				$model->setSaveWith(array('field_values'));
			}
			if(count($_POST['Rules'])>0){
							foreach($_POST['Rules'] as $r){
								$rule = new FormFieldsValidationRules();
								$rule->validation_rule_id  = $r;
								$model->valid_rules[] = $rule;
							}
						}
						$model->setSaveWith(array('valid_rules'));
			$trans = Yii::app()->db->beginTransaction();
			$status = 'error';
			if($model->save()){
				if(!$model->hasErrors()){
					$trans->commit();
					Yii::app()->user->setFlash('success', 'Field has been saved!');
					$status = 'success';
				}
				else{
					$trans->rollback();
				}
			}
			$html = $this->renderPartial('_form',array('model'=>$model),true);
			echo CJSON::encode(array('html'=>$html,'status'=>$status));
			Yii::app()->end();
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		echo CJSON::encode(array('status'=>'success'));
		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id,$with = array())
	{
		$model=FormFields::model()->with(array_merge(array('formPage'),$with))->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='form-fields-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionGetFields()
	{
		if(isset($_GET['q']))
			$result = FormFields::model()->findAll('name LIKE :q',array(':q'=>$_GET['q'].'%'));
		$items = array();
		foreach($result as $r)
		{
			$items[] = $r->name;
		}
		
		echo CJSON::encode($items);
		Yii::app()->end();
	}
}
