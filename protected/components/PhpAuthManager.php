<?php 
class PhpAuthManager extends CPhpAuthManager{
    public function init(){
        if($this->authFile===null){
            $this->authFile=Yii::getPathOfAlias('application.config.auth').'.php';
	    $this->defaultRoles = array('guest');
        }
 
        parent::init();
        // ��� ������ � ��� � ��� ���� �� ��������� guest.
        if(!Yii::app()->user->isGuest){
            // ��������� ����, �������� � �� � ��������������� ������������,
            // ������������ UserIdentity.getId().
            if(!Yii::app()->user->getRole()){
	            $this->assign('user', Yii::app()->user->id);
            }else{
            	$this->assign(Yii::app()->user->role, Yii::app()->user->id);
            }
	    
        }
    }
}