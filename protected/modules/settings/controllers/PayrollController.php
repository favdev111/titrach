<?php

class PayrollController extends Controller
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
				'actions'=>array('index','add','edit','calculate','rules','deleteRule','delete','exportXls'),
				'roles'=>array(User::ROLE_ADMIN),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new PayRate('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PayRate']))
			$model->attributes=$_GET['PayRate'];

		$this->render('index',array(
			'model'=>$model,
		));
	}
    
    
    public function actionAdd()
    {
        $model = new PayRate();
        if($this->_save($model))
		{
			Messaging::FlashSuccess('Pay Rate were succesefully added');
			$this->redirect(array('edit','id'=>$model->id));
		}

		$this->render('add',array(
			'model'=>$model,
		));
    }
	
	public function actionEdit($id)
	{
		$model = $this->_loadPayRate($id);
		
		//Load form fields values
		
		$fields=Forms::getFieldsForPayRates();
		
		if($this->_save($model))
		{
			Messaging::FlashSuccess('Pay Rate were succesefully updated');
		}
		
		$this->render('edit',array(
			'model'=>$model,
			'fields'=>$fields,
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->_loadPayRate($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
    
	public function actionRules($model = false)
	{
		if(is_numeric($model))
		{
			$model = $this->_loadPayRate($model);
		}
		$rateRule = new PayRateRules;
		$rateRule->pay_rate_id = $model->id;
		if(isset($_POST['PayRateRules']))
		{
			$rateRule->attributes = $_POST['PayRateRules'];
			if($rateRule->save())
			{
				Messaging::FlashSuccess('Pay rate has been added');
				$rateRule->unsetAttributes();
				$rateRule->pay_rate_id = $model->id;
			}
		}
		
		$this->renderPartial('_form_rate_rules',array('model'=>$model,'rateRule'=>$rateRule));
	}
	
    
	public function actionDeleteRule($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->_loadRateRule($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	
	public function actionCalculate($prid)
	{
		$provider = $this->_loadProvider($prid);
		//Load services form. HARDCODE. fid = 2
		$form = $this->_loadForm(2,array('formFields'=>array('order'=>'browse_order = 0, browse_order')));
		$entities = new FormsEntities('search');
		FormsEntities::$globalForm = $form;
		$entities->unsetAttributes();  // clear any default values
		$data = null;
		if(isset($_GET['FormsEntities'])){
			$entities->formsEntitiesFields = null;
			
			if(isset($_GET['FormsEntities']['formsEntitiesFields'])){
				$entities->formsEntitiesFields=$_GET['FormsEntities']['formsEntitiesFields'];
			}
			$data = PayRate::calculate($entities->search('search',true)->getData(),$provider);
		}
		
		$this->render('calculate',array('provider'=>$provider, 'data'=>$data,'entities'=>$entities));
	}
	
	//Export data to excel file
	public function actionExportXls($prid)
	{
		$provider = $this->_loadProvider($prid);
		$fullName = $provider->getFullName();
		//Load services form. HARDCODE. fid = 2
		$form = $this->_loadForm(2,array('formFields'=>array('order'=>'browse_order = 0, browse_order')));
		$entities = new FormsEntities('search');
		FormsEntities::$globalForm = $form;
		$entities->unsetAttributes();  // clear any default values
		$data = array();
		$date_from = '';
		$date_to = '';
		if(isset($_POST['FormsEntities'])){
			$entities->formsEntitiesFields = null;
			
			if(isset($_POST['FormsEntities']['formsEntitiesFields'])){
				$entities->formsEntitiesFields=$_POST['FormsEntities']['formsEntitiesFields'];
				$date_from = $_POST['FormsEntities']['formsEntitiesFields'][15]['date_from'];
				$date_to = $_POST['FormsEntities']['formsEntitiesFields'][15]['date_to'];
			}
			if($date_from && $date_to)
				$data = PayRate::calculate($entities->search('search',true)->getData(),$provider);
		}
		
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
			->setTitle($fullName.' payroll')
			->setSubject($fullName.' payroll')
			->setDescription($fullName.' payroll');
		$objPHPExcel->getActiveSheet()->setTitle(sanitize_file_name($fullName));

		
		$colsTitle = array('Rate','Interval','Date','Student','Group size');
		
		$objPHPExcel->setActiveSheetIndex(0);

		$c = 0;
		foreach($colsTitle as $col)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, 1, $col);
			$c++;
		}

		$line = 2;
		$total = 0;
		foreach($data as $date=>$row)
		{
			$d = new DateTime; $d->setTimestamp($date);
			$objRichText = new PHPExcel_RichText();
			$objBold = $objRichText->createTextRun($d->format('l F j Y'));
			$objBold->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $line, $objRichText);
			$services = $row['services'];
			ksort($services,SORT_NUMERIC);
			foreach($services as $service){
				foreach($service as $serv){
					$line++;	
					$total +=$serv['rate'];
					$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$line)->setValueExplicit(sprintf('%01.2f',$serv['rate']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $line, $serv['interval']);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $line, $serv['begin_date']->format('h:i A').' - '.$serv['end_date']->format('h:i A'));
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $line, $serv['patient']->getFullName());
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $line, $serv['g_size'] ? 'Group size:'.$serv['g_size'] : '');
				}
			}
			$line++;
		}
		$line++;
		$objRichText = new PHPExcel_RichText();
		$objBold = $objRichText->createTextRun('Total:'.sprintf('%01.2f',$total));
		$objBold->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $line, $objRichText);

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

		$fileName = sanitize_file_name('Payroll for '.$fullName.' '.$date_from.' '.$date_to).'_U_'.Yii::app()->user->ID.'_'.date("Y-m-d-H-i-s");
		$result['filename'] = $fileName.'.xls';

		$objWriter->save('public/export/'.$fileName.'.xls');

		$result['status'] = true;

		echo CJSON::encode($result);

	}
	
	/**
	 * General save
	 */
    private function _save(&$model)
    {
        if(isset($_POST['PayRate']))
		{
			$trans = Yii::app()->db->beginTransaction();
			try{
                $model->attributes=$_POST['PayRate'];
               
                //$model->saveWith = array('patients');
                if($model->save())
                {
                    $trans->commit();
                    return true;
                }
                Messaging::FlashError('Error during save Pay Rate!');
                $trans->rollback();
            }catch(Exception $e){
                $trans->rollback();
                throw $e;
            }	
		}
		return false;    
    }
    
    
    private function _loadPayRate($id,$with = false)
    {
		if($with)
		{
			$model = PayRate::model()->with($with)->findByPk($id);
		}else{
			$model=PayRate::model()->findByPk($id);				
		}

		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model; 
    }
    
    private function _loadRateRule($id)
	{
		$model=PayRateRules::model()->findByPk($id);
		
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model; 
	}
	
	private function _loadProvider($id)
	{
		$model=Provider::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
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