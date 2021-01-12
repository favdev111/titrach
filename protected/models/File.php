<?php
class File extends CModel{
    
    private $_path = null;
	private $_pid;
	
    public function __construct($pid = false){
       $this->_pid = $pid;
    }
    
    public function getUploadPath(){
		if(!$this->_path)
		{
			if(!$this->_pid)
				$pid = Yii::app()->session['patient_ID'];
			else
				$pid = $this->_pid;
			if(!$pid)
			{
				throw new CHttpException('404','Invalid request');
			}
			$this->_path = Patient::getPatientFolder($pid);
		}

		return $this->_path;
    }
    
    static function createDirIfNotExist($dir = null){
        if($dir===null)
            $dir = $this->_uploadPath;
        if( !is_dir( $dir) ) {
            mkdir( $dir, 0777, true );
            chmod ( $dir , 0777 );
            //throw new CHttpException(500, "{$this->path} does not exists.");
        }
    }   
    
       
    public function attributeNames(){
        return array(
        );
    }
    
    public function show($name){
        $path = $this->getUploadPath();
        $file = realpath($path.DIRECTORY_SEPARATOR.$name);

        if(strpos($file,realpath($path))===false){
            die('nothing here');
        }

        if (file_exists($file)) {
            if (ob_get_level()) {
              ob_end_clean();
            }
            header('Content-Description: File Transfer');
			header("Content-Type: application/force-download");
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . rawurlencode(basename($file)));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            if ($fd = fopen($file, 'rb')) {
              while (!feof($fd)) {
                print fread($fd, 1024);
            }
            fclose($fd);
            }
        }        
    }
	
	public function delete($name)
	{
		$path = $this->getUploadPath();
        $file = realpath($path.DIRECTORY_SEPARATOR.$name);

        if(strpos($file,realpath($path))===false){
            die('nothing here');
        }
		if (file_exists($file)) {
			if(unlink($file))
			{
				return true;
			}
		}else{
			return 'File not exist!';
		}
		return 'Error during file delete';
	}
	
	public function getFileList($patient = false)
	{
		if($patient){
			$this->_pid = $patient;
		}
		$dir = $this->getUploadPath();
		$files =array();
		if (is_dir($dir)){
			foreach(CFileHelper::findFiles($dir) as $file)
			{
				$files[] = array(
					'ext'=>CFileHelper::getExtension($file),
					'filename'=>basename($file),
					'url'=>Yii::app()->createUrl('patients/files/get/pid/'.$this->_pid.'/'.str_replace('\\','/',str_replace($dir.DIRECTORY_SEPARATOR,'',$file))),
					'deleteUrl'=>Yii::app()->createUrl('patients/files/delete/pid/'.$this->_pid.'/'.base64_encode(str_replace('\\','/',str_replace($dir.DIRECTORY_SEPARATOR,'',$file))))
				);
			}
			return $files;
		}
		return array();
	}
    
}
