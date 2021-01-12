<?php
    
class SoftDeleteBehavior extends CActiveRecordBehavior
{
    static $globalDisable = null;
    
    private $_softDeleteColumn = 'deleted'; // Column name

    public function beforeDelete($oEvent)
    {
        parent::beforeDelete($oEvent);

        if(self::$globalDisable == get_class($oEvent->sender))
        {
            self::$globalDisable = null;
            return;
        }
        
        if ($this->_softDeleteColumn != null and $oEvent->isValid and !$oEvent->handled)
        {
            if ($oEvent->sender->hasAttribute($this->_softDeleteColumn))
            {
                $oEvent->isValid = false;
                $oEvent->handled = true;
                $oEvent->sender->setAttribute($this->_softDeleteColumn, 1);
                $oEvent->sender->saveAttributes(array($this->_softDeleteColumn));
            }
        }
    }

    public function beforeFind($oEvent)
    {
        parent::beforeFind($oEvent);
        
        if(self::$globalDisable == get_class($oEvent->sender))
        {
            //self::$globalDisable = null;
            return;
        }
        if ($this->_softDeleteColumn != null and $oEvent->isValid and !$oEvent->handled)
        {
            if ($oEvent->sender->hasAttribute($this->_softDeleteColumn))
            {
                $criteria = new CDbCriteria();

                $criteria->addCondition( $oEvent->sender->getTableAlias().".". $this->_softDeleteColumn . "=0 OR ".$oEvent->sender->getTableAlias().".". $this->_softDeleteColumn." IS NULL");

                $oEvent->isValid = false;
                $oEvent->handled = true;
                if(!empty($oEvent->sender->dbCriteria))
                {
                    $oEvent->sender->dbCriteria->mergeWith($criteria);
                }
                else
                    $oEvent->sender->dbCriteria = $criteria;
            }
        }
    }

    static function globalDisable($class)
    {
        self::$globalDisable = $class;
    }
}