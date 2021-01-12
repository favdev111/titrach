<?php

class PatientController extends Controller
{
	public $fullWidth = true;
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
            array('application.filters.RestrictForm + view'),
		);
	}
        
    public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index','view','history','files','exportXls','delete'),
				'roles'=>array('manager'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionIndex()
	{
		//workaround to have timepicker at popup
		Yii::app()->bootstrap->registerAssetCss('bootstrap-timepicker.css');
		Yii::app()->bootstrap->registerAssetJs('bootstrap.timepicker.js');

		$patients = new Patient('search');
		$patients->unsetAttributes();
		if(isset($_GET['Patient']))
			$patients->attributes=$_GET['Patient'];
		$this->render('index',array('patients'=>$patients));
	}

	
	public function actionHistory($pid)
	{
		$patient = $this->_loadPatient($pid);
		
		$entities = new FormsEntities('search');
		$entities->unsetAttributes();  // clear any default values
		if(isset($_GET['FormsEntities']))
			$entities->attributes=$_GET['FormsEntities'];
		$entities->patient_id = (int) $pid;
		$this->render('history',array(
			'entities'=>$entities,
			'patient'=>$patient
			
		));
	}
	
	public function actionFiles($pid)
	{
		$patient = $this->_loadPatient($pid);
		Yii::app()->session['patient_ID'] = $patient->id;
		$this->render('files',array(
			'patient'=>$patient
			
		));
	}
	
	public function actionDelete($id,$entity=false)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			if(!$entity)
				$this->_loadPatient($id)->delete();
			else{
				$entity = FormsEntities::model()->findByPk($id);
				if(!$entity)
				{
					throw new CHttpException(404,'Invalid record ID.');
				}
				if($entity->initial)
				{
					echo '<script type="text/javascript">alert("This record is initial record! You can delete it only from Student Profile page");</script>';
				}elseif($bill = BilledForms::model()->find(array('condition'=>'entity_id=:eid','params'=>array(':eid'=>$entity->id)))){
					echo '<script type="text/javascript">alert("This record is attached to bill. Please remove corresponding bill first");</script>';
				}else{
					$entity->delete();
				}
			}
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : $entity ? array('history','pid'=>$entity->patient_id) :  array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	
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
	
	protected function renderPatientRowButtons($data,$row)
	{
		$forms = Forms::getForms(true,true);
		$items = array();
		foreach($forms as $id=>$title)
		{
			$items[] = array('label'=>$title,'url'=>'');
		}
		$html = '<div class="btn-toolbar">';
		$html .= $this->widget('bootstrap.widgets.TbButtonGroup', array(
			'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			'size'=>'mini',
			'buttons'=>array(
				array('label'=>'Add', 'items'=>array(
					array('label'=>'Action', 'url'=>'#'),
					array('label'=>'Another action', 'url'=>'#'),
					array('label'=>'Something else', 'url'=>'#'),
					'---',
					array('label'=>'Separate link', 'url'=>'#'),
				)),
			),
			),true); 
		$html .='</div>';
		return $html;
	}
	
	
	protected function renderPatientHistoryRowButtons($data,$row)
	{
		
		//$forms = Forms::getForms(true,true);
		$items = array();
		//$items[] = array('label'=>'Follow Up','url'=>$this->createUrl('form/followUp',array('pid'=>$data->patient_id,'fid'=>$data->form_id,'peid'=>$data->id)));
		$related = Forms::model()->findAll('parent = :parent',array(':parent'=>$data->form_id));
		foreach($related as $r)
		{
			$items[] = array('label'=>$r->form_title,'url'=>$this->createUrl('form/add',array('fid'=>$r->id,'pid'=>$data->patient_id,'peid'=>$data->id)));
		}
		$html = '<div class="btn-toolbar">';
		$html .= $this->widget('bootstrap.widgets.TbButtonGroup', array(
			'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			'size'=>'mini',
			'buttons'=>array(
				array('label'=>'Add', 'items'=>$items),
			)),true); 
		$html .='</div>';
		return $html;
	}
	
	public function actionExportXls()
	{
		$patients = new Patient('search');
		$patients->unsetAttributes();
		if(isset($_POST['Patient']))
			$patients->attributes=$_POST['Patient'];		
		
		$data = $patients->search()->getData();
		if(count($data)==0)
		{
			echo CJSON::encode(array('status'=>0,'message'=>'No results found'));
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
			->setTitle("Students")
			->setSubject("Students")
			->setDescription("Students");
		$objPHPExcel->getActiveSheet()->setTitle('Students lits');

		$colsTitle = array(
			'Firstname',
			'Lastname',
			'Gender',
			'DOB',
			'Student ID',
			'Address',
			'City',
			'State',
			'Zip',
			'Phone',
			'Parent/Guardian',
			'Contact Person',
		);

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
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $line, $row->firstname);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $line, $row->lastname);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $line, $row->gender);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $line, $row->dob);			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $line, $row->student_id);			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $line, $row->address);						
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $line, $row->city);									
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $line, $row->state);												
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $line, $row->zipcode);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $line, $row->contact_phone);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $line, $row->parent_guardian);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $line, $row->contact_person);
			$line++;
		}

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		$fileName = 'Patients_List_U_'.Yii::app()->user->ID.'_'.date("Y-m-d-H-i-s");
		$result['filename'] = $fileName.'.xls';

		$objWriter->save('public/export/'.$fileName.'.xls');

		$result['status'] = true;

		echo CJSON::encode($result);

	}
	
}