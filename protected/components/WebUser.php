<?php
class WebUser extends CWebUser {

    const SYSTEM_USER_ID    =   1;

    private $_model = null;
 
    public function getRole() {
        if($user = $this->_getModel()){
            return $user->role;
        }
    }
    public function getDefaultForm(){
        if($user = $this->_getModel()){
            return  $user->default_form;
        }
    }
    public function getAllowedForms(){
        if($user = $this->_getModel()){
            return  (array) $user->allowed_forms;
        }        
    }
	
	public function getRelatedProviders(){
		if($user = $this->_getModel())
		{
			return (array) $user->related_providers;	
		}
	}
	
	public function getFullName()
	{
		if($user = $this->_getModel()){
            return  $user->getFullName();
        }
	}
 
    private function _getModel(){
        if (!$this->isGuest && $this->_model === null){
            $this->_model = User::model()->findByPk($this->id, array('select' => 'role, default_form, related_providers,allowed_forms,first_name,last_name'));
        }
        return $this->_model;
    }
 
 	public function updateModel($model = false)
	{
		$this->_model = $model ? $model : null;
		return $this;
	}

    
    public function isRestrictForm(){
        if($user = $this->_getModel()){
            if(in_array($user->role,array('doctor'))){
                return true;
            }
        }
        return false;
    }
}
