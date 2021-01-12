<?php

class FilesController extends Controller
{
	public function actions() {
	    return array(
                'uploadFile' => array(
                    'class' => 'TXUploadAction',
                    'subfolderVar' => true,
					'publicPath' =>Yii::app()->createUrl('patients/files/get'),
					'path'=>Patient::getPatientFolder(Yii::app()->session['patient_ID']),
					'secureFileNames'=>false
                )
            );
	}

	
	public function filters()
	{
		return array(
			'ajaxOnly + upload',
			'accessControl', // perform access control for CRUD operations
		);
	}
        
    public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index','upload','uploadFile','get','list','delete'),
				'roles'=>array('doctor'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	
	public function actionUpload($pid)
	{
		if(is_string($pid)){
			$patient = new Patient();
		}else{
			$patient = Patient::model()->findByPk((int)$pid);
		}
		
		Yii::import("xupload.models.XUploadForm");
            $upload_form = new XUploadForm;

		$this->renderPartial('upload',array('patient'=>$patient,'upload_form'=>$upload_form));
	}

	
	public function actionGet($pid,$file)
	{
		$f = new File($pid);
		
		$f->show($file);
        Yii::app()->end();

	}
	
	public function actionDelete($pid,$file)
	{
		if(!Yii::app()->request->isAjaxRequest)
		{
			throw new CHttpException(403,'You are not authorized to this type of request.');
		}
		
		$f = new File($pid);
		
		$result = $f->delete(base64_decode($file));
		
		if($result !== true)
		{
			echo $result;
		};
		
        Yii::app()->end();
	}	
	
	
	public function actionList($pid)
	{
		$f = new File($pid);
		$files = $f->getFileList();
		if(Yii::app()->request->isAjaxRequest)
		{
			$this->renderPartial('async/list',array('files'=>$files));
			Yii::app()->end();
		}else{
			$this->render('list',array('files'=>$files));
		}
		
	}
	

}