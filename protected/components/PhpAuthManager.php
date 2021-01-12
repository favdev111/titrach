<?php 
class PhpAuthManager extends CPhpAuthManager{
    public function init(){
        if($this->authFile===null){
            $this->authFile=Yii::getPathOfAlias('application.config.auth').'.php';
	    $this->defaultRoles = array('guest');
        }
 
        parent::init();
        // Äëÿ ãîñòåé ó íàñ è òàê ğîëü ïî óìîë÷àíèş guest.
        if(!Yii::app()->user->isGuest){
            // Ñâÿçûâàåì ğîëü, çàäàííóş â ÁÄ ñ èäåíòèôèêàòîğîì ïîëüçîâàòåëÿ,
            // âîçâğàùàåìûì UserIdentity.getId().
            if(!Yii::app()->user->getRole()){
	            $this->assign('user', Yii::app()->user->id);
            }else{
            	$this->assign(Yii::app()->user->role, Yii::app()->user->id);
            }
	    
        }
    }
}