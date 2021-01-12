<?php

class RestrictForm extends CFilter
{
    protected function preFilter($filterChain)
    {
        $user = Yii::app()->user;
        $form_ids = $user->getAllowedForms();
        if(isset($filterChain->controller->actionParams['fid'])){
            if(!in_array($filterChain->controller->actionParams['fid'],$form_ids) && $user->isRestrictForm()){
                throw new CHttpException(403, 'You are not authorized to perform this action.');
            }
        }
        return true; // false — для случая, когда действие не должно быть выполнено
    }
 
    protected function postFilter($filterChain)
    {
        // код, выполняемый после выполнения действия
    }
}