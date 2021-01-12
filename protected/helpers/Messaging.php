<?php
class Messaging
{
    static function FlashSuccess($msg)
    {
        Yii::app()->user->setFlash('success', $msg);
    }
    static  function FlashError($msg)
    {
        Yii::app()->user->setFlash('error', $msg);
    }
}