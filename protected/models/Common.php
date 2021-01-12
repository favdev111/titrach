<?php
class Common{

	const STATUS_ACTIVE		= 99;
	const STATUS_DISABLED	= 2;
        
        const YES   = 1;
        const NO    = 0;

	/**
     *Get array of statuses
     *@return array id=>string
     */
    static function getStatuses(){
		return array(
			self::STATUS_ACTIVE     =>'Active',
            self::STATUS_DISABLED   =>'Disabled'
		);
	}
    
    /**
     *Get string representation of status code
     *@param int $status
     *@return string
     */
    static function getStatusString($status){
        $arr = self::getStatuses();
        return isset($arr[$status]) ? $arr[$status] : 'UNDEFINED';
    }
    
    static function getYesNo(){
        return array(
            self::YES=>'Yes',
            self::NO=>'No'
        );
    }
    
	static function getYesNoString($id){
		$yn = self::getYesNo();
		return $yn[$id];
	}
	
	static function getStringById($data,$id)
	{
		return isset($data[$id]) ? $data[$id] : 'UNDEFINED';
	}
	
	
    static function encode($String, $Password)
    {
        $Salt='BGuxLWQtKweKEMV4';
        $StrLen = strlen($String);
        $Seq = $Password;
        $Gamma = '';
        while (strlen($Gamma)<$StrLen)
        {
            $Seq = pack("H*",sha1($Gamma.$Seq.$Salt));
            $Gamma.=substr($Seq,0,8);
        }
       
        return $String^$Gamma;
    }
	
	static function generate_uid($length=32){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
			$clen = strlen($chars) - 1;  
			while (strlen($code) < $length) {
			$code .= $chars[mt_rand(0,$clen)];  
			}	
		return $code;
	}
	
	static function storeSearchInSession($params,$path)
	{
		$cur = Yii::app()->session['search'];
		if($cur)
			Yii::app()->session['search'] = array_merge($cur,array($path=>$params));
		else
			Yii::app()->session['search'] = array($path=>$params);
	}

	static function getVersion()
	{
		
	}
}