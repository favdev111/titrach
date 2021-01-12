<?php

/**
 * This is the model class for table "billed_forms".
 *
 * The followings are the available columns in table 'billed_forms':
 * @property string $id
 * @property string $bill_id
 * @property string $entity_id
 * @property integer $agency_id
 * @property integer $created
 *
 * The followings are the available model relations:
 * @property FormsEntities $entity
 * @property FormsEntities $bill
 * @property Agency $agency
 */
class BilledForms extends CActiveRecord
{
	
	const BILL_TYPE_CSE = 'CSE';
	const BILL_TYPE_CPSE = 'CPSE';
	const BILL_TYPE_SETSS = 'SETSS';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BilledForms the static model class
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
		return 'billed_forms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agency_id, created', 'numerical', 'integerOnly'=>true),
			array('id', 'length', 'max'=>20),
			array('bill_id, entity_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bill_id, entity_id, agency_id, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'entity' => array(self::BELONGS_TO, 'FormsEntities', 'entity_id'),
			'bill' => array(self::BELONGS_TO, 'FormsEntities', 'bill_id'),
			'agency' => array(self::BELONGS_TO, 'Agency', 'agency_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'bill_id' => 'Bill',
			'entity_id' => 'Entity',
			'agency_id' => 'Agency',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bill_id',$this->bill_id,true);
		$criteria->compare('entity_id',$this->entity_id,true);
		$criteria->compare('agency_id',$this->agency_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	

	/**
	 * Get bill type(CSE,CPSE,SETTS) by patient info and bill date
	 * @param int|array|FormsEntities  $entity,
	 */
	static function getBillType($entity)
	{
		if(!is_object($entity))
		{
			
		}elseif(is_array)
		$pat_dob  = $entity->patient->dob;
		$pat_dob = DateTime::createFromFormat('m/d/Y',$pat_dob);
		
		//!!!!! HARDCODE !!!!! bill date id hardcoded
		//!!!!! TODO: Set bill_date to the begin of school year
		$bill_date = isset($entity->formsEntitiesFields[29]) ? $entity->formsEntitiesFields[29]->value : false;
		$bill_date = DateTime::createFromFormat('m/d/Y',$bill_date);

		if(!$pat_dob || !$bill_date)
			return 'Undefined';
		$diff = $bill_date->diff($pat_dob);
		if($diff->y >=5)
		{
			return self::BILL_TYPE_CSE;
		}else{
			return self::BILL_TYPE_CPSE;
		}
	}
	
	
	static function getBillTypeByDates($pat_dob,$bill_date)
	{
		$pat_dob = DateTime::createFromFormat('m/d/Y',$pat_dob);
		$bill_date = DateTime::createFromFormat('m/d/Y',$bill_date);
		if(!$pat_dob || !$bill_date)
			return 'Undefined';
		$diff = $bill_date->diff($pat_dob);
		if($diff->y >=5)
		{
			return self::BILL_TYPE_CSE;
		}else{
			return self::BILL_TYPE_CPSE;
		}		
	}
}