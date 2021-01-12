<?php

class FormController extends Controller
{
	public $fullWidth = false;
	
	private $_render_pdf = true;
	
	public function filters()
	{
		return array(
			'ajaxOnly + validate, ajaxList',
			'accessControl', // perform access control for CRUD operations
            array('application.filters.RestrictForm + form,add,new'),
		);
	}
        
    public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index','renderPDF','new','add','followUp','validate','delete','edit','view','list','ajaxList','exportXls'),
				'roles'=>array('doctor'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 *Add new 
	 */	
	public function actionNew($fid = false)
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
			$entity =$this->_save($_POST[$form->form_name],$form);
			//= $_POST[$form->form_name];
			//$entity = (object) $entity;
			$tmp_pat_ID = $_POST['patient_ID'] == Yii::app()->session['patient_ID'] ? $_POST['patient_ID'] : false;
			$entity->convertFieldsIndexes();
		}
		
		$tmp_pat_ID = !empty($tmp_pat_ID) ? $tmp_pat_ID : Common::generate_uid(16);
		
		Yii::app()->session['patient_ID'] = $tmp_pat_ID;
		$this->render('new',array('pages'=>$formPages,'model'=>$form,'patient_ID'=>$tmp_pat_ID,'entity'=>$entity/*$entity->convertFieldsIndexes()*/));
		
	}	
	
	/**
	 *Add related forms
	 */
	public function actionAdd($fid,$pid,$peid = false)
	{
		$form = $this->_loadForm($fid,array('formFields'=>array('index'=>'name')));
		$patient = $this->_loadPatient($pid);

		$formPages = FormsPages::model()->with(array(
							'formFields.fieldsValues'=>array(
									'condition'=>'formFields.status='.Common::STATUS_ACTIVE,
									'order'=>'t.sort_order asc, formFields.sort_order',
							),
							'formFields.formFieldsValidationRules'))
						->findAll('t.form_id=:formID AND  t.status=:tStatus',array(':formID'=>$fid,':tStatus'=>Common::STATUS_ACTIVE));
		$entity = false;
		$p_entity = false;
		if($peid)
		{
			$p_entity = $this->_loadEntity($peid,$pid,$form->parent);
		}
		
		if(isset($_POST[$form->form_name]))
		{
			$entity =$this->_save($_POST[$form->form_name],$form,$patient->id,false,$p_entity ? $p_entity->id : false);
			//= $_POST[$form->form_name];
			//$entity = (object) $entity;
		}elseif($p_entity){
			$entity  = $p_entity->fillRelated($form);
		}
		
		Yii::app()->session['patient_ID'] = $patient->id;
		$this->render('add',array('pages'=>$formPages,'form'=>$form,'model'=>$form,'patient_ID'=>$patient->id,'entity'=>$entity->convertFieldsIndexes(),'patient'=>$patient));
		
	}
	
	/**
	 * Add follow up report
	 */
	public function actionFollowUp($pid,$fid,$peid){
		$form = $this->_loadForm($fid,array('formFields'=>array('index'=>'name')));
		$patient = $this->_loadPatient($pid);

		$formPages = FormsPages::model()->with(array(
							'formFields.fieldsValues'=>array(
									'condition'=>'formFields.status='.Common::STATUS_ACTIVE,
									'order'=>'t.sort_order asc, formFields.sort_order',
							),
							'formFields.formFieldsValidationRules'))
						->findAll('t.form_id=:formID AND  t.status=:tStatus',array(':formID'=>$fid,':tStatus'=>Common::STATUS_ACTIVE));
		$entity = false;
		$p_entity = $this->_loadEntity($peid,$pid,$form->id);
		
		if(isset($_POST[$form->form_name]))
		{
			$entity =$this->_save($_POST[$form->form_name],$form,$patient->id,false,$p_entity->id);
			//= $_POST[$form->form_name];
			//$entity = (object) $entity;
		}else{
			$entity = $p_entity;
		}
		
		Yii::app()->session['patient_ID'] = $patient->id;
		$this->render('followUp',array('pages'=>$formPages,'form'=>$form,'model'=>$form,'patient_ID'=>$patient->id,'p_entity'=>$p_entity,'entity'=>$entity->convertFieldsIndexes(),'patient'=>$patient));		
	}
	
	/**
	 *
	 */
	public function actionEdit($pid,$fid,$eid)
	{
		$patient  = $this->_loadPatient($pid);
		Yii::app()->session['patient_ID'] = $patient->id;
		$form = $this->_loadForm($fid,array('formFields'=>array('index'=>'name')));
		$entity = $this->_loadEntity($eid,$pid,$fid);
		if(!$entity->canEdit())
		{
			throw new CHttpException(403,'Forbidden');
		}
		
		
		$formPages = FormsPages::model()->with(array(
					'formFields.fieldsValues'=>array(
							'condition'=>'formFields.status='.Common::STATUS_ACTIVE,
							'order'=>'t.sort_order asc, formFields.sort_order'
					),
					'formFields.formFieldsValidationRules'))
				->findAll('t.form_id=:formID AND  t.status=:tStatus',array(':formID'=>$fid,':tStatus'=>Common::STATUS_ACTIVE));

		if(isset($_POST[$form->form_name]))
		{
			$this->_save($_POST[$form->form_name],$form,$patient->id,$entity);
			
			//$entity = $_POST[$form->form_name];
			//$entity = (object) $entity;
		}
		
		Yii::app()->session['patient_ID'] = $patient->id;
		if($entity->initial)
		{
			Yii::app()->user->setFlash('info', '<strong>Note:</strong> This form is initial. Changes, made here, will affect student\'s record.');
		}
		$this->render('edit',array('pages'=>$formPages,'form'=>$form,'patient'=>$patient,'entity'=>($entity instanceof FormsEntities) ?  $entity->convertFieldsIndexes() : $entity));				
	}
	
	public function actionDelete($id)
	{
		
		if(Yii::app()->request->isPostRequest)
		{

			$entity = FormsEntities::model()->findByPk($id);
			if(!$entity)
			{
				throw new CHttpException(404,'Invalid record ID.');
			}
			if(!$entity->canEdit())
			{
				throw new CHttpException(403,'Forbidden');
			}
			if($entity->initial)
			{
				echo '<script type="text/javascript">alert("This record is initial record! You can delete it only from Student Profile page");</script>';
			}elseif($bill = BilledForms::model()->find(array('condition'=>'entity_id=:eid','params'=>array(':eid'=>$entity->id)))){
				echo '<script type="text/javascript">alert("This record is attached to bill. Please remove corresponding bill first");</script>';
			}else{
				$entity->delete();
			}
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : $entity ? array('history','pid'=>$entity->patient_id) :  array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');		

	}
	
	/**
	 *Save data 
	 */
	private function _save($data,$form,$pid=false,$eid=false,$parent_id = false)
	{
		$trans = Yii::app()->db->beginTransaction();
		$initial = false;
		//Add new patient
		if(!$pid)
		{
			if($form->id == Yii::app()->params['main_form_id'])
			{
				$patient = Patient::add($data,Yii::app()->session['patient_ID'],$form);
				if($patient->hasErrors())
				{
					$trans->rollback();
					Yii::app()->user->setFlash('error', '<b>Error during add new patient!</b><br>'.CHtml::errorSummary($patient));
					return false;
				}
				$pid = $patient->id;
				$initial = true;
			}
		}else{
			if($pid instanceof Patient)
			{
				$patient = $pid;
			}else{
				$patient = $this->_loadPatient($pid);	
			}
		}
		
		if(!$eid)
		{
			$entity = FormsEntities::saveNew($data,$pid,$form->id,$parent_id,$initial);
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
			Yii::app()->user->setFlash('error', 'Error during saving form information!');
			return $entity;
		}
		$eid  = $entity->id;
		
		/**
		 * Hardcode add of Event for services session
		 */
		if($form->id ==2)
		{
			//Additional check for mandate time, not good approach to check here, but most easy
			$mandate = '';
			try{
				if(!$entity->checkMandate($mandate))
				{
					Yii::app()->user->setFlash('error', 'Session time more than mandate allowed! ('.$mandate->duration.')');
					$trans->rollback();
					return $entity;
				}
			}catch(CException $e)
			{
				Yii::app()->user->setFlash('error', $e->getMessage());
				$trans->rollback();
				return $entity;
			}
			//Check if we already add event for this entity before, and if yes, then uncomplete them
			if($eid)
			{
				$event = Event::model()->find(array('condition'=>'event_meta = '.$eid.'  AND event_type = '.Event::EVENT_TYPE_NEW_FORM));
				if($event)
				{
					//Uncomplete
					//$event->saveAttributes(array('fulfilled'=>0,'event_meta'=>''));
					
					//Delete instead uncomplete. Added 01/10/2014 cause corresponding request
					$event->delete();
				}
			}

			$event = new Event;
			$event->event_type = Event::EVENT_TYPE_NEW_FORM;
			$event->patient_id = $data['speech_session_student'];
			$event->provider_id = $data['speech_session_provider'];
			$event->fulfilled = 1;
			$event->event_meta = $eid;
			$time_str = $data['speech_session_start_date'].' '.$data['speech_session_start_time'];
			$time = DateTime::createFromFormat('m/d/Y h:i A',$time_str);
			if($time)
				$event->start_date_timestamp = $time->getTimestamp();
			$time_str = $data['speech_session_start_date'].' '.$data['speech_session_end_time'];
			$time = DateTime::createFromFormat('m/d/Y h:i A',$time_str);
			
			if($time)
				$event->end_date_timestamp = $time->getTimestamp();

			if( $mandate->recommended_count > 1)
			{
				$event->_forceSave = true;
			}
			if($mandate->recommended_count > 1)
			{
				$event->_getAllBusy = true;
			}
			
			$result = false;
			if(!($result = $event->save()))
			{

				if($event->_busyEvent && $event->_busyEvent->provider_id == $event->provider_id
					&& $event->_busyEvent->patient_id == $event->patient_id
					&& $event->_busyEvent->start_date_timestamp == $event->start_date_timestamp
					&& $event->_busyEvent->end_date_timestamp == $event->end_date_timestamp
					&& ($event->_busyEvent->event_meta == $event->event_meta || empty($event->_busyEvent->event_meta))
				)
				{
					$event->_busyEvent->fulfilled($eid);
					Yii::app()->user->setFlash('info','<b>Info:</b> Event, scheduled for this session in calendar, had been marked as completed.');
				}else{	
					Yii::app()->user->setFlash('error', CHtml::errorSummary($event));
					$trans->rollback();
					return $entity;
				}
			}
			
			if($event->_forceSave && $result)
			{
				if($event->_getAllBusy)
				{
					$eids = array();
					foreach($event->_busyEvents as $bev)
					{
						$eids[] = $bev->event_meta;
					}
					
					if(count($eids) > $mandate->recommended_count)
					{
						Yii::app()->user->setFlash('error','Count of already existing sessions for this time is more, than selected mandate allowed. Please check info or select other mandate');	
						$trans->rollback();
						return $entity;
					}
					
				}else{
					Yii::app()->user->setFlash('info','<b>Info:</b>This is group session. Event for this provider for this time already exist in calendar.');
				}
			}
		}
		//End of adding Event
		
		
		$trans->commit();
		if($form->save_to_directory)
		{
			$path = Patient::getPatientFolder($patient);
			File::createDirIfNotExist($path);
			$this->_generatePDF($entity->id,$patient->id, $form->id,$path.'/'.sanitize_file_name($patient->getFullName(false).'_'.$form->getEntityRelatedTitle($entity)).'.pdf','F');
		}
		if($entity->initial)
		{
			$patient = Patient::updateFromInitial($patient,$entity,$form);
			if($patient->hasErrors())
			{
				Yii::app()->user->setFlash('error', '<b>Error during update patient info! </b><br>'.CHtml::errorSummary($patient));
			}
		}
		Yii::app()->user->setFlash('success',$form->getEntityRelatedTitle($entity). ' for patient ' .$patient->getFullName().' saved successfully!');
		
		//Commented due to request to redirect to session's list
		//$this->redirect(array('edit','pid'=>(int)$pid,'fid'=>$form->id,'eid'=>$eid));
		
		$this->redirect(array('list','fid'=>$form->id));
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
	
	/**
	 * Load patient
	 */
	private function _loadPatient($id,$with = null)
	{
		if($with){
                    $model = Patient::model()->with($with)->findByPk($id);
                }else{
                    $model=Patient::model()->findByPk($id);
                }
		if($model===null)
			throw new CHttpException(404,'Invalid Patient ID');
		return $model;		
	}
	
	/**
	 * Load form entity
	 */
	private function _loadEntity($eid,$pid,$fid, $with = null)
	{
		if($with){
                    $model = FormsEntities::model()->with($with)->findByPk($eid,'patient_id=:pid AND form_id=:fid',array(':pid'=>$pid,':fid'=>$fid));
                }else{
                    $model= FormsEntities::model()
						->with(array(
							'formsEntitiesFields'=>array('index'=>'form_field_id'),
							'formsEntitiesFields.formField'	
						))
					->find('t.id = :eid  AND t.patient_id=:pid AND t.form_id=:fid',array(':eid'=>$eid,':pid'=>$pid,':fid'=>$fid));;
                }
		if($model===null)
			throw new CHttpException(404,'Invalid Entity ID');
		return $model;		
	}	
	
	
	/**
	 *Valdiate one page of form
	 */
	public function actionValidate(){
		$formPage = null;
		if(isset($_GET['page_id'])){
			$formPage = FormsPages::model()->with(array(
					'form',
					'formFields.fieldsValues'=>array(
							'condition'=>'formFields.status='.Common::STATUS_ACTIVE,
							'order'=>'t.sort_order asc, formFields.sort_order'),
					'formFields.formFieldsValidationRules'))
				->findByPk((int)$_GET['page_id'],'t.status=:tStatus',array('tStatus'=>Common::STATUS_ACTIVE));                
		}
		if($formPage == null){
			throw new CHttpException(404,'Invalid page ID');
		}
		unset($_GET['page_id']);
		$res = $formPage->validatePage($_GET[$formPage->form->form_name]);
		echo $res !==true ? json_encode(array('status'=>'error','errors'=>$res)) : json_encode(array('status'=>'success'));
		Yii::app()->end();
	}

	
	/**
	 *Download PDF file
	 */
	public function actionRenderPDF($eid,$pid,$fid){
		
		//$this->render('//site/common/under_construction');
		$this->_generatePDF($eid,$pid,$fid);
	}
	
	/**
	 *Show html view of form template
	 */
	public function actionView($eid,$pid,$fid){
		$this->_render_pdf = false;
		$this->_generatePDF($eid,$pid,$fid);
	}

	/**
	 * List form's entities 
	 */
	public function actionList($fid,$peid = false,$pid = false)
	{
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.cookie.js');
		
		$this->fullWidth = true;
		$form = $this->_loadForm($fid,array('formFields'=>array('order'=>'browse_order = 0, browse_order')));
		$entities = new FormsEntities('search');
		FormsEntities::$globalForm = $form;
		$entities->unsetAttributes();  // clear any default values
		$prev_search = Yii::app()->session['search']['patients/form/list'];
		if($prev_search)
			$entities->formsEntitiesFields = $prev_search;
	
		
		$show_only_related = isset(Yii::app()->request->cookies['show_only_related']) &&  Yii::app()->request->cookies['show_only_related']->value ? true : false;
		
		if(isset($_GET['FormsEntities'])){
			$entities->formsEntitiesFields = null;
			
			if(isset($_GET['FormsEntities']['formsEntitiesFields'])){
				$entities->formsEntitiesFields=$_GET['FormsEntities']['formsEntitiesFields'];
				//	Common::storeSearchInSession($_GET['FormsEntities']['formsEntitiesFields'],'patients/form/list');
				//throw new Exception(print_r($prev_search,true));				
			}
		}
		$entities->showOnlyRelated = $show_only_related;
		$entities->form_id = (int) $fid;
		if($peid)
		{
			$p_entity = $this->_loadEntity($peid,$pid,$form->parent);
			$entities->parent = (int)$peid;
		}
		$this->render('list',array(
			'entities'=>$entities,
			'form'=>$form,
			'p_entity'=>$peid ? $p_entity : null,
			'pid'=>$pid,
			'show_only_related' =>$show_only_related
			
		));
	}
	
	
	/**
	 *Provide list of requested values as json array via ajax request
	 */
	public function actionAjaxList()
	{
		$class = isset($_GET['class']) ? trim($_GET['class']): '';
		$label =  isset($_GET['label']) ? trim($_GET['label']): '';
		$val =  isset($_GET['val']) ? trim($_GET['val']): '';
		$q =  isset($_GET['q']) ? trim($_GET['q']): '';
		$sf = isset($_GET['sf']) ? trim($_GET['sf']): '';
		$results  = array();
		if(class_exists($class))
		{
			$class = new $class;
			$val_sf= true;
			if(empty($sf))
			{
				echo json_encode($results);
				Yii::app()->end();
			}
			$sf = (array)explode(',',$sf);
			foreach($sf as $k=>$f)
			{
				$sf[$k]  = $f = trim($f);
				if(!$class->hasAttribute($f))
				{
					$val_sf = false;
					break;
				}

			}
			
			$data = array();
			
			if($class->hasAttribute($val) && $val_sf && ($class->hasAttribute($label) || method_exists($class,$label) || method_exists($class,'get'.$label)))
			{
				$criteria  = new CDbCriteria;
				foreach($sf as $f)
				{
					$criteria->compare($f,$q,true,'OR');
				}
				$data = $class->findAll($criteria);
			}
			if(count($data)>0)
			{
				$is_f = false;
				if(method_exists($class,$label))
				{
					$label =$label;
					$is_f = true;
				}elseif(method_exists($class,'get'.$label))
				{
					$label = $class->{'get'.$label}();
					$is_f = true;
				}

				foreach($data as $row)
				{
					$results[] = array('id'=>$row->$val,'text'=>$is_f ?  $row->$label(): $row->$label);
				}
			}

		}
		echo json_encode($results);
		Yii::app()->end();		
	}
	
	
	/**
	 *Generate and display html or PDF of form template
	 */
	protected function  _generatePDF($eid,$pid,$fid,$fn=false,$mode = 'I')
	{
		
		$form = $this->_loadForm($fid,array('formFields'=>array('index'=>'id')));
		$entity = $this->_loadEntity($eid,$pid,$fid);
		$entity->setReleatedForm($form);
		$data = $entity->getDataAsArray();
		$formFields =$form->formFields ;//FormsPages::model()->with(array('formFields.fieldsValues'=>array('order'=>'formFields.sort_order asc','condition'=>'formFields.status='.Common::STATUS_ACTIVE)))->findAll('t.form_id=:formID AND  t.status=:tStatus',array(':formID'=>$model->id,':tStatus'=>Common::STATUS_ACTIVE),array('order'=>'t.sort_order asc, t.id'));		
		$html = $this->renderPartial('//form_templates/'.$form->form_name,array('data'=>$data,'form'=>$form,'entity'=>$entity,'formFields'=>$formFields),true);
		$fn = $fn ? $fn : 'form-'.$form->form_name;
		if($this->_render_pdf){
			// PDF::render($fn.'.pdf',$html,array('outputMode'=>$mode));
			$customSize = array();
			$customSize['customPageSize'] = array(21, 60);
			PDF::render($fn.'.pdf',$html,$customSize);
		}
		else{
			$this->render('view',array('html'=>$html,'form'=>$form,'patient'=>$this->_loadPatient($pid),'entity'=>$entity));
		}
	}
	
	//Get Tabs array for Bootstrap Tabs
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

	//Export data to excel file
	public function actionExportXls($fid,$peid = false,$pid = false)
	{
		$form = $this->_loadForm($fid,array('formFields'=>array('order'=>'browse_order = 0, browse_order')));
		$entities = new FormsEntities('search');
		FormsEntities::$globalForm = $form;
		$entities->unsetAttributes();  // clear any default values
		$prev_search = Yii::app()->session['search']['patients/form/list'];
		if($prev_search)
			$entities->formsEntitiesFields = $prev_search;
			
		if(isset($_POST['FormsEntities'])){
			$entities->formsEntitiesFields = null;
			
			if(isset($_POST['FormsEntities']['formsEntitiesFields'])){
				$entities->formsEntitiesFields=$_POST['FormsEntities']['formsEntitiesFields'];
			}
		}
		$entities->form_id = (int) $fid;
		if($peid)
		{
			$p_entity = $this->_loadEntity($peid,$pid,$form->parent);
			$entities->parent = (int)$peid;
		}
	
	
		$data = $entities->search('search',true)->getData();
		
		if(count($data)==0)
		{
			echo CJSON::encode(array('status'=>0,'message'=>'No records for export!'));
			Yii::app()->end();
		}		
		
		
		$phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
		$result = array();
		spl_autoload_unregister(array('YiiBase','autoload'));
		include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		spl_autoload_register(array('YiiBase','autoload'));

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator(Yii::app()->name)
			->setLastModifiedBy(Yii::app()->name)
			->setTitle($form->form_title)
			->setSubject($form->form_title)
			->setDescription($form->form_title);
		$objPHPExcel->getActiveSheet()->setTitle($form->form_title.' list');

		
		$cols = $this->getFormBrowsableColumns($form);
		$colsTitle = array('Pat.ID','F.ID','Form');
		foreach($cols as $col)
		{
			$colsTitle[] = $col['header'];
		}
		$colsTitle[] = 'Timestamp';
		
		$objPHPExcel->setActiveSheetIndex(0);

		$c = 0;
		foreach($colsTitle as $col)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, 1, $col);
			$c++;
		}

		$line = 2;
		foreach($data as $row)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $line, "std_".$row->patient_id);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $line, "f_".$row->id);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $line, $row->form->getEntityRelatedTitle($row));
			
			$i = 3;
			foreach($cols as $col)
			{
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $line, $this->evaluateExpression($col['value'],array('data'=>$row)));
				$i++;
			}
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $line, Yii::app()->dateFormatter->formatDateTime($row->timestamp, 'medium', ''));
			$line++;
		}

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		$fileName = $form->form_name.'_U_'.Yii::app()->user->ID.'_'.date("Y-m-d-H-i-s");
		$result['filename'] = $fileName.'.xls';

		$objWriter->save('public/export/'.$fileName.'.xls');

		$result['status'] = true;

		echo CJSON::encode($result);

	}
	
	protected function renderFormEntitiesListRowButtons($data,$row)
	{
		
		//$forms = Forms::getForms(true,true);
		$items = array();
		$items2 = array();
		if($data->form_id !== Yii::app()->params['main_form_id'])
			$items[] = array('label'=>'Follow Up','url'=>$this->createUrl('form/followUp',array('pid'=>$data->patient_id,'fid'=>$data->form_id,'peid'=>$data->id)));
		$related = Forms::model()->findAll('parent = :parent',array(':parent'=>$data->form_id));
		$relECount = FormsEntities::model()->with('relatedCount')->findByPk($data->id);
		foreach($related as $r)
		{
			$items[] = array('label'=>$r->form_title,'url'=>$this->createUrl('form/add',array('fid'=>$r->id,'pid'=>$data->patient_id,'peid'=>$data->id)));
			$items2[] = array('label'=>$r->form_title,'url'=>$this->createUrl('form/list',array('fid'=>$r->id,'peid'=>$data->id,'pid'=>$data->patient_id)));
		}
		$html = '<div class="btn-toolbar pull-left ">';
		$html .= $this->widget('bootstrap.widgets.TbButtonGroup', array(
			'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			'size'=>'mini',
			'buttons'=>array(
				array('label'=>'Add', 'items'=>$items),
			)),true); 
		$html .='</div>';
		
		if(!empty($items2) && $relECount->relatedCount > 0)
		{
			$html .= '<div class="btn-toolbar">';
			$html .= $this->widget('bootstrap.widgets.TbButtonGroup', array(
				'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
				'size'=>'mini',
				'buttons'=>array(
					array('label'=>'Browse', 'items'=>$items2),
				)),true);
			$html .='</div>';
		}
		return $html;
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
					$value = 'implode(";",(isset($data->formsEntitiesFields['.$field->id.']) ? $data->formsEntitiesFields['.$field->id.']->getValuesAsArray() : array() ))';
				}elseif($field->type == FormFields::TYPE_textarea)
				{
					$value = 'string_limit_words(isset($data->formsEntitiesFields['.$field->id.']) ? $data->formsEntitiesFields['.$field->id.']->value: "",20)';	
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
				}else{
					$value = 'isset($data->formsEntitiesFields['.$field->id.']) ? $data->formsEntitiesFields['.$field->id.']->value: ""';
				}
				$cols[] = array(
					'name'=>$field->name,
					'header'=>$field->title,
					'value'=>$value,
					'type'=>'html',
					'htmlOptions'=>array('class'=>$field->type)
				);
			}
		}

		return $cols;
	}
	
}