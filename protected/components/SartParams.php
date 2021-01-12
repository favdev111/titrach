<?php
class SartParams extends CApplicationComponent
{
	public $filename = 'params.bin';
	public $path = null;
	
	public function init()
	{
		if(!$this->path)
			$this->path = Yii::getPathOfAlias("application.config");
		$this->_readParams();
	}
	
	
	private function _readParams()
	{
		
		$path = $this->path.DIRECTORY_SEPARATOR.$this->filename;
		if(file_exists($path))
		{
			$data = unserialize(file_get_contents($path));
			if(!empty($data))
			{
				$data = array_merge(Yii::app()->getParams()->toArray(),$data);	
				Yii::app()->setParams($data);
			}
		}
	}
	
	public function saveParams($data)
	{
		file_put_contents($this->path.DIRECTORY_SEPARATOR.$this->filename,serialize($data));
		return true;
	}
}