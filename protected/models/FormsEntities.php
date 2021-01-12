<?php

/**
 * This is the model class for table "forms_entities".
 *
 * The followings are the available columns in table 'forms_entities':
 * @property string $id
 * @property integer $patient_id
 * @property string $form_id
 * @property string $timestamp
 * @property string $last_update_timestamp
 * @property string $parent
 *
 * The followings are the available model relations:
 * @property Forms $form
 * @property Patients $patient
 * @property FormsEntittiesFields[] $formsEntittiesFields
 */
class FormsEntities extends CActiveRecord
{
    //Instance of Forms class, contains current form  structure
    static  $globalForm  = null;

    private $_fields = array();

    protected $_releatedForm = null;

    private $_attributes = array();

    //additional property for allow user to see only own records
    public $showOnlyRelated = false;

    /**
     *Override setAttribute to have ability to add form fields dynamically, only for search!
     */
    public function setAttribute($name,$value)
    {
        if(!parent::setAttribute($name,$value))
        {
            if(self::$globalForm)
            {
                $fields = $this->_getFields();
                if(isset($fields[$name]))
                {
                    $this->_attributes[$name] = $value;
                    return true;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }
        return true;
    }

    /**
     *Override setAttribute to have ability to add form fields dynamically, only for search!
     */

    public function hasAttribute($name)
    {
        if(!parent::hasAttribute($name))
        {
            if(self::$globalForm)
            {
                $fields = $this->_getFields();
                return isset($fields[$name]);
            }
            return false;
        }
        return true;

    }

    /**
     *Override
     */
    public function attributeNames()
    {
        $cols = parent::attributeNames();
        if(self::$globalForm)
        {
            $fields = $this->_getFields();
            foreach(array_keys($fields) as $key )
            {
                $cols[] = $key;
            }
        }
        return $cols;
    }

    private function _getFields()
    {
        if(empty($this->_fields))
        {
            if(self::$globalForm)
            {
                $this->_fields = self::$globalForm->getFieldsNames();
                return $this->_fields;
            }else{
                throw new CException(Yii::t('yii','Form should be specified to add fields dynamically'));
            }
        }else{
            return $this->_fields;
        }
    }




    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FormsEntities the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'forms_entities';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {


        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $rules =array(
            array('form_id', 'required'),
            array('timestamp, last_update_timestamp, initial','safe'),
            array('patient_id', 'numerical', 'integerOnly'=>true),
            array('form_id, timestamp, parent', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, patient_id, form_id, timestamp, parent, globalForm', 'safe', 'on'=>'search'),
        );
        //allow form fields be sortable
        if(self::$globalForm)
        {
            $rules[] = array(implode(',',array_keys($this->_getFields())),'safe','on'=>'search');
        }
        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'form' => array(self::BELONGS_TO, 'Forms', 'form_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'formsEntitiesFields' => array(self::HAS_MANY, 'FormsEntitiesFields', 'form_entity_id', 'index'=>'form_field_id'),
            'relatedCount'=>array(self::STAT,'FormsEntities','parent'),
            'billRowsCount'=>array(self::STAT,'BilledForms','bill_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'form_id' => 'Form',
            'timestamp' => 'Timestamp',
            'last_update_timestamp' => 'Last Update',
            'parent' => 'Parent',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($mode = 'history',$all = false)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);

        if($this->patient_id)
        {
            $criteria->compare('patient_id',$this->patient_id);
        }
        else
        {
            //Restrict patients
            if(Yii::app()->user->role !== User::ROLE_ADMIN)
                $criteria->addCondition("t.patient_id IN (SELECT patient_id FROM caseload as pat_check_1 WHERE provider_id IN(".(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' )."))");
        }
        $criteria->compare($this->getTableAlias().'.form_id',$this->form_id);
        $criteria->compare('timestamp',$this->timestamp);
        $criteria->compare($this->getTableAlias().'.parent',$this->parent);

        //How to sort form entities: asc or desc
        $time_sort = false;

        if($mode == 'history')
            $criteria->with = array('patient','form');
        else{
            $criteria->with = array('patient','form',
                //'formsEntitiesFields'=>array('index'=>'form_field_id'),
                //'formsEntitiesFields.formField'
            );
            $time_sort = true;
            foreach((array)$this->formsEntitiesFields as $id=>$val)
            {
                if(!empty($val) && 'undefined'!==$val)
                {
                    $alias = 'mt'.$id;
                    if(is_array($val))
                    {
                        if(key_exists('date_from',$val))
                        {
                            $date = array();
                            if(!empty($val['date_from']) && $date_from = DateTime::createFromFormat('m/d/Y',$val['date_from']))
                            {
                                $date[] = "STR_TO_DATE(value,'%m/%d/%Y') >= '{$date_from->format('Y-m-d 00:00:00')}'";
                            }

                            if(!empty($val['date_to'])&& $date_to = DateTime::createFromFormat('m/d/Y',$val['date_to']))
                            {
                                $date[] = "STR_TO_DATE(value,'%m/%d/%Y') <= '{$date_to->format('Y-m-d 23:59:59')}'";
                            }
                            $date = count($date) > 0 ? ' AND '.implode(' AND ' ,$date) : '';
                            $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as {$alias} WHERE {$alias}.form_field_id =".(int)$id." ".$date.")");
                        }else{

                            array_map(function($el){return (int)$el;},$val);
                            $vals = implode(',',$val);
                            if($vals !== '')
                            {
                                $alias2= 'v_'.$alias;

                                $criteria->addCondition("t.id IN (SELECT form_entity_id
																	FROM ".FormsEntitiesFields::model()->tableName()." as {$alias}
																	LEFT OUTER JOIN ".FormsEntitiesFieldsValues::model()->tableName() ." as {$alias2}
																		ON {$alias}.id = {$alias2}.fef_id
																	WHERE {$alias}.form_field_id =".(int)$id." AND {$alias2}.field_value_id IN (".implode(',',$val).")
																)
								");
                            }
                        }
                    }else{
                        $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as {$alias} WHERE {$alias}.form_field_id =".(int)$id." AND value LIKE :{$alias} )");
                        $criteria->params[$alias] = $val; //'%'.$val.'%';
                        //$criteria->params[$alias] = "{$val}"; //
                    }

                }
            }
            //throw new Exception(print_r($this->formsEntitiesFields,true));
        }

        //Add provider restriction(if form has such fields)
        //hardcode to allow to see more records for services form
        if((Yii::app()->user->role !== User::ROLE_ADMIN && $this->form_id!=2) || $this->showOnlyRelated )
        {
            //get form_fields_id's
            $form_fields = FormFields::model()->findAll(array(
                'condition'=>'form_id = :form_id AND meta_info LIKE :meta_info',
                'params'=>array(':form_id' =>$this->form_id,':meta_info'=>'%"class_name";s:8:"Provider"%'),
            ));
            if($form_fields)
            {
                $ff = array();
                foreach($form_fields as $fiedls)
                {
                    $ff[] = $fiedls->id;
                }
                $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as prv_check1 WHERE prv_check1.form_field_id IN (".implode(',',$ff).")  AND prv_check1.value IN (".(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' ).") )");
            }
        }


        $criteria->together = true;


        //SORT
        $sort = array();
        $sort['defaultOrder'] = array('timestamp'=>$time_sort);
        $sort['attributes'] = array();
        //Add sort field to query
        $key = get_class($this).'_sort';
        //Check if sort exist and if model has such attribute
        if(isset($_GET[$key]))
        {
            list($key,$order)  = explode('.',$_GET[$key]);
            if($this->hasAttribute($key) && array_key_exists($key,$this->_getFields()))
            {
                $field = $this->_getFields();
                $field = $field[$key];

                $select = array('*');
                if($field->is_multiple())
                {
                    $select[] = "(SELECT GROUP_CONCAT(',',field_value_id) FROM ".FormsEntitiesFields::model()->tableName()." as sort_fef INNER JOIN ".FormsEntitiesFieldsValues::model()->tableName()." as sort_fefv ON sort_fef.id = sort_fefv.fef_id WHERE sort_fef.form_entity_id = ".$this->getTableAlias().".id  AND form_field_id = ".$field->id."  GROUP BY field_value_id) as {$key}";
                }elseif($field->type == FormFields::TYPE_timepicker){
                    $select[] = "(SELECT STR_TO_DATE(value, '%h:%i %p') FROM ".FormsEntitiesFields::model()->tableName()." as sort_fef WHERE sort_fef.form_entity_id = ".$this->getTableAlias().".id  AND form_field_id = ".$field->id." ) as {$key} ";
                }elseif($field->type == FormFields::TYPE_datepicker){
                    $select[] ="(SELECT STR_TO_DATE(value,'%m/%d/%Y') FROM ".FormsEntitiesFields::model()->tableName()." as sort_fef WHERE sort_fef.form_entity_id = ".$this->getTableAlias().".id  AND form_field_id = ".$field->id." ) as {$key} ";
                }else{
                    $select[] ="(SELECT value FROM ".FormsEntitiesFields::model()->tableName()." as sort_fef WHERE sort_fef.form_entity_id = ".$this->getTableAlias().".id  AND form_field_id = ".$field->id." ) as {$key} ";
                }
                $criteria->select = $select;
                $sort['attributes'][$key]= array('asc'=>$key,'desc'=>$key.' desc');
            }
        }
        array_push($sort['attributes'],'*');

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>$all ? false : array(
                'pageSize'=>  Yii::app()->params['rows_per_page'],
            ),

            'sort'=>$sort
        ));

    }

    public function canEdit()
    {
        if(Yii::app()->user->role == User::ROLE_ADMIN)
            return true;
        //get form_fields_id's
        $form_fields = FormFields::model()->findAll(array(
            'condition'=>'form_id = :form_id AND meta_info LIKE :meta_info',
            'params'=>array(':form_id' =>$this->form_id,':meta_info'=>'%"class_name";s:8:"Provider"%'),
        ));
        $criteria = new CDbCriteria();
        if($form_fields)
        {
            $ff = array();
            foreach($form_fields as $fiedls)
            {
                $ff[] = $fiedls->id;
            }
            $criteria->compare($this->getTableAlias().'.form_id',$this->form_id);
            $criteria->compare($this->getTableAlias().'.id',$this->id);
            $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as prv_check1 WHERE prv_check1.form_field_id IN (".implode(',',$ff).")  AND prv_check1.value IN (".(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' ).") )");
        }
        if($rows = self::model()->findAll($criteria))
        {
            return true;
        }
        return false;
    }


    protected function beforeSave()
    {
        if(parent::beforeSave())
        {

            if($this->isNewRecord)
            {
                $this->timestamp = time();
            }
            $this->last_update_timestamp = time();
            return true;
        }
        return false;
    }


    protected function afterSave()
    {
        parent::afterSave();
        $ids = array();
        $errors = array();
        foreach($this->formsEntitiesFields as $key=>$val){
            $val->form_entity_id = $this->id;
            if($val->save())
                $ids[] = $val->id;
            $errors +=$val->getErrors();
        }

        if( !$this->isNewRecord){
            FormsEntitiesFields::model()->deleteAll('form_entity_id=:formEntityId AND id NOT IN('.implode(',',$ids).')',array(':formEntityId'=>$this->id));
        }
        $this->addErrors($errors);
    }

    protected function beforeDelete()
    {
        if(parent::beforeDelete()){
            $res = Event::model()->deleteAll('event_type = :etype AND event_meta = :meta',array(':etype'=>Event::EVENT_TYPE_NEW_FORM,':meta'=>$this->id));
            return true;
        }
        return false;
    }

    public function setReleatedForm($form)
    {
        $this->_releatedForm = $form;
    }


    public function getDataAsArray()
    {
        if($this->_releatedForm === null)
        {
            $this->_releatedForm = $this->form;
        }

        foreach($this->_releatedForm->formFields as $key=>$field){
            if(!empty($this->formsEntitiesFields[$field->id])){
                if($field->is_multiple())
                {
                    $data[$field->name] = $this->formsEntitiesFields[$field->id]->getValuesAsArray();
                    if($field->type == FormFields::TYPE_radio || $field->type ==FormFields::TYPE_select )
                    {
                        $data[$field->name] = array_shift($data[$field->name]);
                        if(empty($data[$field->name]) && !empty($this->formsEntitiesFields[$field->id]->value))
                            $data[$field->name] = $this->formsEntitiesFields[$field->id]->value;
                        //$value = FieldsValues::model()->findByPk(array_shift($data[$field->name]));
                        //	if($value)
                        //		$data[$field->name]= $value->form_field_value;
                        //	else
                        //		$data[$field->name] = ' --deleted-- ' ;
                    }
                }else{
                    $data[$field->name] = $this->formsEntitiesFields[$field->id]->value;
                }
            }else{
                $data[$field->name] = '';
            }
        }
        return $data;
    }

    /**
     *Return Entity object, filled by self data
     */
    public function fillRelated($form)
    {
        $entity = new FormsEntities;
        $fields = array();
        foreach($this->formsEntitiesFields as $id=>$field)
        {
            if(array_key_exists($field->formField->name,$form->formFields))
            {
                $fields[$form->formFields[$field->formField->name]->id] = $field;
            }
        }
        $entity->formsEntitiesFields = $fields;
        return $entity;
    }


    public function convertFieldsIndexes($replace = false)
    {
        if(!empty($this->formsEntitiesFields))
        {
            $fields = array();
            foreach($this->formsEntitiesFields as $f)
            {
                $fields[$f->formField->name] = $f;
            }
            if($replace)
                $this->formsEntitiesFields = $fields;
            else
                $this->formsEntitiesFields = array_merge($this->formsEntitiesFields,$fields);
        }
        return $this;
    }

    /**
     * Check mandate for service record
     * selected_mandate  36
     */
    public function checkMandate(&$info = null)
    {
        if(isset($this->formsEntitiesFields[36]))
        {
            $mId = $this->formsEntitiesFields[36]->value;
            $mandate = PatientsMandates::model()->findByPk($mId);
        }else{

            $field = $this->formsEntitiesFields[Yii::app()->params['services_field_id']];
            if(!$field)
            {
                return true;
            }

            $val = $field->fieldValues[0]->id;
            $mandate = PatientsMandates::model()->find(array(
                'condition'=>'patient_id = :pid AND service_type = :st ',
                'params'=>array(':pid'=>$this->patient_id,':st'=>$val),
            ));
        }
        if(!$mandate)
            return true;

        if ($info!==null) $info = $mandate;

        if(!$this->_checkMandateDuration($mandate))
        {
            return false;
        }

        if(!$this->_checkMandateGroupCount($mandate))
        {
            throw new CException('Selected group count is more than selected mandate allowed!('.($mandate->type ==PatientsMandates::TYPE_INDIVIDUAL ? 1 : $mandate->recommended_count).')');
        }

        if(!$this->_checkMandateFrequency($mandate))
        {
            throw new CException('This mandate already fullfiled for this student at this week!( allowed frequency: '.$mandate->frequency.')');
        }

        return true;
    }


    /**
     * Check mandate duration
     * DBID for start_time and end_time are hardcoded
     * start_time = 16, end_time = 17
     */
    private function _checkMandateDuration($mandate)
    {
        $start_time = isset($this->formsEntitiesFields[16]) ? $this->formsEntitiesFields[16]->value : false;
        $end_time = isset($this->formsEntitiesFields[17]) ? $this->formsEntitiesFields[17]->value : false;
        if(!$start_time || !$end_time)
            return false;
        $diff = getTimeDiff($start_time,$end_time);

        return $diff <= $mandate->duration;
    }

    /**
     * Check mandate group count
     * DBID for group count field  are hardcoded
     * group_size = 22
     * @var $mandate PatientsMandates
     */
    private function _checkMandateGroupCount(PatientsMandates $mandate)
    {
        $group_size = isset($this->formsEntitiesFields[22]) ? ($this->formsEntitiesFields[22]->value == ""  ? 1: $this->formsEntitiesFields[22]->value ) : 0;
        $mandateCount  = $mandate->type ==PatientsMandates::TYPE_INDIVIDUAL ? 1 : $mandate->recommended_count;
        return $group_size <= $mandateCount;
    }

    /**
     * Check how many sessions with mandate exist at seesion current week
     * DBID for start date field  are hardcoded
     * start_date = 15
     * selected_mandate = 36
     * @var $mandate PatientsMandates
     */
    private function _checkMandateFrequency(PatientsMandates $mandate)
    {
        $start_date = isset($this->formsEntitiesFields[15]) ? $this->formsEntitiesFields[15]->value : false;
        list($sOfWeek,$eOfWeek) = x_week_range($start_date);

        $criteria = new CDbCriteria;

        $criteria->compare('patient_id',$this->patient_id);

        $criteria->compare($this->getTableAlias().'.form_id',$this->form_id);
        $criteria->with = array('patient','form');

        $date_from = new DateTime();
        $date_from->setTimestamp($sOfWeek);
        $date_to = new DateTime();
        $date_to->setTimestamp($eOfWeek);

        $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as mtdate WHERE mtdate.form_field_id =15  AND STR_TO_DATE(value,'%m/%d/%Y') >= '{$date_from->format('Y-m-d 00:00:00')}' AND STR_TO_DATE(value,'%m/%d/%Y') <= '{$date_to->format('Y-m-d 23:59:59')}')");
        $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as mtmand WHERE mtmand.form_field_id =36  AND mtmand.value = {$mandate->id})");

        $entities = FormsEntities::model()->findAll($criteria);

        return count($entities) <= $mandate->frequency;
    }


    /**
     *Save new entity in DB
     *@param array $data POST with form's data
     *@param int $pid patient  id
     *@param mixed int|Forms $fid or form object
     *@param int $parent_id Id of parent entity if exist
     *@return FormsEntities
     */
    static function saveNew($data,$pid,$fid,$parent_id = false,$initial = false)
    {
        $entity = new FormsEntities;
        $entity->patient_id = $pid;
        $entity->form_id = $fid;
        $entity->initial = $initial;
        if($parent_id){
            $entity->parent = $parent_id;
        }
        if(!is_object($fid)){
            $fid = Forms::model()->with(array('formFields'=>
                array('index'=>'name','condition'=>'formFields.status='.Common::STATUS_ACTIVE),
                'formFields.fieldsValues'
            ))->findByPk($fid);
        }

        if(!isset($fid->formFields))
        {
            $fid->formFields;
        }
        $fields = array();
        foreach($data as $key=>$val)
        {
            if(!empty($fid->formFields[$key]))
            {
                $form_field = $fid->formFields[$key];
                $field = new FormsEntitiesFields();
                $field->form_field_id = $form_field->id;
                if($form_field->is_multiple())
                {
                    $field->multiple = true;
                    $f_vs = array();
                    foreach((array)$val as $vv)
                    {
                        //HARDCODE!!!
                        if(strpos($vv,'-')===0){
                            continue;
                        }
                        $f_v = new FormsEntitiesFieldsValues();
                        $f_v->field_value_id = $vv;
                        $f_vs[] = $f_v;
                    }
                    $field->formsEntitiesFieldsValues = $f_vs;
                }else{
                    $field->multiple = false;
                    $field->value = $val;
                }
                $fields[$form_field->id]= $field;
            }
        }
        $entity->formsEntitiesFields = $fields;
        $entity->save();
        return $entity;
    }

    /**
     *Update existing entity
     *@param array $data POST with form's data
     *@param FormsEntities $entity
     *@param Forms $fid
     *@return FormsEntities
     */
    static function mapAndSave($data,$entity,$fid = false)
    {
        if(!is_object($fid)){
            $fid = Forms::model()->with(array('formFields'=>
                array('index'=>'name','condition'=>'formFields.status='.Common::STATUS_ACTIVE),
                'formFields.fieldsValues'
            ))->findByPk($fid);
        }

        if(!isset($fid->formFields))
        {
            $fid->formFields;
        }
        $fields = array();
        foreach($data as $key=>$val)
        {
            //TODO: make optimization, maybe merge saveNew and mapAndSave
            if(!empty($fid->formFields[$key]))
            {
                $form_field = $fid->formFields[$key];
                if(!empty($entity->formsEntitiesFields[$form_field->id]))
                {
                    $field = $entity->formsEntitiesFields[$form_field->id];

                }else{
                    //if we doesn't have this field in DB, then add new object for it
                    $field = new FormsEntitiesFields();
                    $field->form_field_id = $form_field->id;
                }
                if($form_field->is_multiple())
                {
                    $field->multiple = true;
                    $f_vs = array();
                    foreach((array)$val as $vv)
                    {
                        //HARDCODE!!!
                        if(strpos($vv,'-')===0){
                            continue;
                        }
                        $f_v = new FormsEntitiesFieldsValues();
                        $f_v->field_value_id = $vv;
                        $f_vs[] = $f_v;
                    }
                    $field->formsEntitiesFieldsValues = $f_vs;
                }else{
                    $field->multiple = false;
                    $field->value = $val;
                }
                $fields[$form_field->id]= $field;
            }
        }
        if(count($fields)>0)
            $entity->formsEntitiesFields = $fields;

        $entity->save();
        return $entity;
    }

    /**
     * Get list of not billed session for specific patient\provder\agency
     * @param int $pid patient id
     * @param int $prid provider id
     * @param int $aid agency id
     * @param int $serviceType value id
     * @param int $mandate_id value mandate id
     *
     **/
    public function getFormsForBill($pid,$prid,$aid=false,$serviceType=false,$mandate_id = false)
    {
        //!!!!! HARDCODE !!!!!

        $criteria=new CDbCriteria;
        //For patient
        $criteria->compare('patient_id',$pid);
        //Only form type
        $criteria->compare($this->getTableAlias().'.form_id',2);
        //!!!!! HARD HARDCODE !!!!!
        //For provider
        $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as fef_1 WHERE fef_1.form_field_id =14 AND value=".(int)$prid." )");
        //Only entities, that was not previously include
        $criteria->addCondition("t.id NOT IN (SELECT entity_id FROM ".BilledForms::model()->tableName()." as bf_1)");

        if($serviceType)
        {

            //!!!!! HARDCODE !!!!!
            //Select only required services type
            //Only special education records, 18 is code of services_record_type, 34 - code of special education value
            //$criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as fef_1 INNER JOIN ".FormsEntitiesFieldsValues::model()->tableName()." as fefv_1 WHERE  fefv_1.field_value_id = 33  )");//

            if($serviceType == 35){
                $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as fef_1 INNER JOIN ".FormsEntitiesFieldsValues::model()->tableName()." as fefv_1 WHERE  fefv_1.field_value_id = 35  )");
            }else{
                $criteria->addCondition("t.id  IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as fef_1 INNER JOIN ".FormsEntitiesFieldsValues::model()->tableName()." as fefv_1 ON fef_1.id = fefv_1.fef_id  WHERE fefv_1.field_value_id = ".$serviceType."  )");
            }
       

        }

		 if($mandate_id)
            {
                //!!!!! HARDCODE !!!!!
                //Select only sessions, that has mandate selected
                // 36 - is code of mandate field from session form
                $criteria->addCondition("t.id IN (SELECT form_entity_id FROM ".FormsEntitiesFields::model()->tableName()." as fef_2 WHERE  value=".(int)$mandate_id." )");

            }


            $criteria->with = array('patient','form');
            $criteria->together = true;



        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>false,
            'sort'=>array(
                'defaultOrder'=>array(
                    'timestamp'=>'timestamp Desc',
                )
            )
        ));

    }

    /**
     * Check if field exist for specific entities.
     */
    public static function checkField($entity_id,$field_id)
    {
        $field = FormsEntitiesFields::model()->find('form_entity_id =:feid AND form_field_id =:field_id',array(':feid'=>$entity_id,':field_id'=>$field_id));
        if($field)
            return $field->value;
        else
            return false;
    }


}























