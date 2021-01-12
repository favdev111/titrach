<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

    const ERROR_ACCOUNT_NOT_ACTIVE = 3;
    
    protected $_id;
        
    public function authenticate(){
        $user = User::model()->find('LOWER(email)=? AND status="active"', array(strtolower($this->username)));
        if($user === null){
            $this->errorCode = self::ERROR_USERNAME_INVALID;
	}else if(!$user->validatePassword($this->password)){			
	    $this->errorCode = self::ERROR_PASSWORD_INVALID;			
	}else if(!($user->status == 'active')){
	    $this->errorCode = self::ERROR_ACCOUNT_NOT_ACTIVE;
        }else{
            $this->_id = $user->id;
            $this->username = $user->first_name.' '.$user->last_name;
            $this->errorCode = self::ERROR_NONE;
            $user->last_login = date("Y-m-d H:i:s");
            $user->save();
        }
        return !$this->errorCode;
    }
 
    public function getId(){
        return $this->_id;
    }
}