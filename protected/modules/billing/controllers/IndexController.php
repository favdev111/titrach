<?php
class IndexController extends Controller
{

	public $fullWidth = false;

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

    public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index','add','list','view','delete','grid'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function  actionAdd($fid = false)
	{

		$fid = $fid ? (int)$fid : Yii::app()->params['main_form_id'];

		$form = $this->_loadForm($fid,array('formFields'=>array('index'=>'name')));
		$formPages = FormsPages::model()->with(array(
							'formFields.fieldsValues'=>array(
									'condition'=>'formFields.status='.Common::STATUS_ACTIVE,
									'order'=>'t.sort_order asc, formFields.sort_order',
							),
							'formFields.formFieldsValidationRules'))
						->findAll('t.form_id=:formID AND  t.status=:tStatus',array(':formID'=>$fid,':tStatus'=>Common::STATUS_ACTIVE));
		$entity = false;

		if(isset($_POST[$form->form_name]))
		{
			$entity =$this->_save($_POST[$form->form_name],$form,$_POST['bill[bill_patient]']);
			//$tmp_pat_ID = $_POST['patient_ID'] == Yii::app()->session['patient_ID'] ? $_POST['patient_ID'] : false;
			$entity->convertFieldsIndexes();
		}


		//Yii::app()->session['patient_ID'] = $tmp_pat_ID;
		$this->render('add',array('pages'=>$formPages,'model'=>$form,'entity'=>$entity/*$entity->convertFieldsIndexes()*/));
	}

	public function actionList($type)
	{
		$fid = 3; //Hardcode of billing form id
		switch($type){
			case 'all':
				//$this->render('all');
				$title = 'all';
				break;
			case 'cse':
				$_GET['FormsEntities']['formsEntitiesFields'][32]=BilledForms::BILL_TYPE_CSE;
				$title = 'CSE';
				break;
			case 'cpse':
				$_GET['FormsEntities']['formsEntitiesFields'][32]=BilledForms::BILL_TYPE_CPSE;
				$title = 'CPSE';
				break;
			case 'setss':
				$_GET['FormsEntities']['formsEntitiesFields'][32]=BilledForms::BILL_TYPE_SETSS;
				$title = 'SETSS';
				break;
		}
		$this->fullWidth = true;
		$form = $this->_loadForm($fid);
		$entities = new FormsEntities('search');
		$entities->unsetAttributes();  // clear any default values
		$prev_search = false;
		//$prev_search = Yii::app()->session['search']['billing/index/list'];
		if($prev_search)
			$entities->formsEntitiesFields = $prev_search;

		if(isset($_GET['FormsEntities'])){
			$entities->formsEntitiesFields = null;

			if(isset($_GET['FormsEntities']['formsEntitiesFields'])){
				$entities->formsEntitiesFields=$_GET['FormsEntities']['formsEntitiesFields'];
				Common::storeSearchInSession($_GET['FormsEntities']['formsEntitiesFields'],'billing/index/list');
				//throw new Exception(print_r($prev_search,true));
			}
		}
		$entities->form_id = $fid;

		$this->render('all',array(
			'entities'=>$entities,
			'form'=>$form,
			'title'=>$title,
		));


	}


	//TODO: replace function below  with correct call of functions from other controller
	public function getTabs($pages,$model,$entity = false,$for_model_fields = array()){
		$tabs = array();
		$active  = true;
		$total = count($pages);
		foreach($pages as $i=>$page)
		{
			$tabs[] = array(
				'content'=>$this->renderPartial('_tab',array('page'=>$page,'entity'=>$entity,'steps_count'=>$total,'current'=>$i+1,'model'=>$model,'for_model_fields'=>$for_model_fields),true),
				'label' =>$page->title,
				'active' => $active,
				'linkOptions'=>array('class'=>'text-right','data-id'=>$i+1),
			);
			$active = false;
		}
		return $tabs;
	}


	public function actionGrid()
	{
		$billRows = new FormsEntities('search');
		$billRows->unsetAttributes();
		if(isset($_GET['is_search']) && $_GET['is_search']==1)
		{
			$s_t = (int)$_GET['service_type'];
			$f_s_t = FieldsValues::model()->findByPk($s_t);

			$rows = $billRows->getFormsForBill((int)$_GET['patient_id'],(int)$_GET['provider_id'],(int)$_GET['agency_id'],$f_s_t ? $f_s_t->form_field_value : false,!empty($_GET['mandate_id']) ? (int)$_GET['mandate_id'] : false);
		}else{
			$billRows->timestamp = -1;
			$rows = $billRows->search();
		}

		$this->renderPartial('_grid',array(
				'billRows'=>$rows,
		));

	}

	/**
	 *Show html view of form template
	 */
	public function actionView($eid,$pid,$fid){
		Yii::import('application.modules.patients.controllers.FormController');
		$c  = new FormController('form',$this->getModule());
		$c->actionView($eid,$pid,$fid);
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$entity = FormsEntities::model()->findByPk($id);
			if(!$entity)
			{
				throw new CHttpException(404,'Invalid bill ID.');
			}

			$entity->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	protected function getFormBrowsableColumns($form)
	{
		$cols = array();
		foreach($form->formFields as $field)
		{
			if($field->is_browsable)
			{
				$value = '';
				if(in_array($field->type,array(FormFields::TYPE_checkbox,FormFields::TYPE_radio,FormFields::TYPE_select)))
				{
					$value = 'implode(";",(isset($data->formsEntitiesFields['.$field->id.']) ? $data->formsEntitiesFields['.$field->id.']->getValuesAsArray(true) : array() ))';
				}elseif($field->type == FormFields::TYPE_model){
					if(is_array($field->meta_info) && isset($field->meta_info['class_name'])&& isset($field->meta_info['class_field_label']))
					{
						$class =  new $field->meta_info['class_name'];
						$label = $field->meta_info['class_field_label'];
						if(method_exists($class,$label))
						{
							$label = $label;
						}elseif(method_exists($class,'get'.$label))
						{
							$label = 'get'.$label;
						}

						$value = function ($data,$row) use ($field,$label)
						{
							$class =  new $field->meta_info['class_name'];
							$val = false;
							if(isset($data->formsEntitiesFields[$field->id]))
								$val = $class->findByPk($data->formsEntitiesFields[$field->id]->value);
							if($val)
								$val = $val->$label();
							return $val;
						};
					}
				}elseif($field->type = FormFields::TYPE_mandate){
					$value  = function ($data, $row) use ($field)
					{
						if(!empty($data->formsEntitiesFields[$field->id]))
						{
							$mandate = PatientsMandates::model()->findByPk($data->formsEntitiesFields[$field->id]->value);
							if($mandate)
								return $mandate->getCaption();
						}
						return '';
					};
				}else{
					$value = 'isset($data->formsEntitiesFields['.$field->id.']) ? $data->formsEntitiesFields['.$field->id.']->value: ""';
				}
				$cols[] = array(
					'name'=>$field->name,
					'header'=>$field->title,
					'value'=>$value,
					'type'=>'html',
				);
			}
		}
		return $cols;
	}


	/**
	 *Save data
	 */
	private function _save($data,$form,$pid=false,$eid=false,$parent_id = false)
	{
		if(!$pid && isset($data['bill_patient'])){
			$pid = $data['bill_patient'];
		}
		if($pid)
		{
			$patient = Patient::model()->findByPk($pid);
			if($patient)
			{
				$bill_type = BilledForms::getBillTypeByDates($patient->dob,$data['bill_date']);
				if($bill_type==BilledForms::BILL_TYPE_CSE && (!empty($data['bill_service_type']) ))
				{
					$bill_type = BilledForms::BILL_TYPE_SETSS;
				}
			}
			$data['bill_type'] = $bill_type;
		}

		$trans = Yii::app()->db->beginTransaction();
		$initial = false;

		if(!$eid)
		{
			$entity = FormsEntities::saveNew($data,$pid,$form->id,$parent_id,$initial);
			$form->saveAttributes(array('current_counter'=>$form->current_counter++));
		}else{
			if($eid instanceof FormsEntities)
			{
				$entity = $eid;
			}else{
				$entity = $this->_loadEntity($eid,$patient->id,$form->id);
			}

			$entity = FormsEntities::mapAndSave($data,$entity,$form);
			//$eid = $eid
		}

		if($entity->hasErrors())
		{
			$trans->rollback();
			Yii::app()->user->setFlash('error', 'Error during saving Bill information!');
			return $entity;
		}else{
			if(!empty($_POST['entities_id'])){
				$time = time();
				foreach($_POST['entities_id'] as $entity_id)
				{
					$addToBill = new BilledForms();
					$addToBill->bill_id = $entity->id;
					$addToBill->entity_id = (int)$entity_id;
					$addToBill->created = $time;
					$addToBill->agency = 1; //Hardcode
					if(!$addToBill->save())
					{
						$trans->rollback();
						Yii::app()->user->setFlash('error','Error during link sessions to bill');
						$entity->addErrors($addToBill->getErrors());
						return $entity;
					};
				}
			}else{
				$trans->rollback();
				Yii::app()->user->setFlash('error','You can\'t save bill with empty session list!');
				return $entity;
			};
		}
		$eid  = $entity->id;

		$trans->commit();
		Yii::app()->user->setFlash('success', ' Bill saved successfully!');
		$this->redirect(array('list','type'=>'all'));
	}

	/**
	 * Load Form
	 */
	private function _loadForm($id,$with=null)
	{
		if($with){
                    $model = Forms::model()->with($with)->findByPk($id,'t.status=:Status',array(':Status'=>Common::STATUS_ACTIVE));
                }else{
                    $model=Forms::model()->findByPk($id,'status=:Status',array(':Status'=>Common::STATUS_ACTIVE));
                }
		if($model===null)
			throw new CHttpException(404,'Invalid form ID.');
		return $model;
	}


}