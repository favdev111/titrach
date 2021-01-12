<?php
class PatientFilesWidget extends CWidget{
	
	const MODE_FIRST_UPLOAD		= 1;
	const MODE_REGULAR_UPLOAD 	= 2;
	
	public $patient_ID = null;
	public $mode = 2;
	public $title = 'Student\'s Files';

	public function init()
    {
        // этот метод будет вызван внутри CBaseController::beginWidget()
		$this->registerClientScript();
		parent::init();
    }
 
	
	public function run()
    {
		$f = new File($this->patient_ID);
		$files = $f->getFileList();
        $this->render('filesBox', array('files' => $files,'title'=>$this->title));
    }

	protected function registerClientScript()
    {
		$cs=Yii::app()->clientScript;
		$cs->registerCoreScript('jquery.ui');
		Yii::import( "xupload.XUpload" );
		$xupload = new XUpload();
		$xupload->publishAssets();
    }
}