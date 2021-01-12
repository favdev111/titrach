<?php

/**
 * This is the model class for table "patients_mandates".
 *
 * The followings are the available columns in table 'patients_mandates':
 * @property string $id
 * @property integer $patient_id
 * @property integer $service_type
 * @property integer $frequency
 * @property integer $duration
 * @property integer $split
 * @property integer $created
 * @property integer $updated
 *
 * The followings are the available model relations:
 * @property Patients $patient
 */
class PatientsMandates extends CActiveRecord
{
	const TYPE_INDIVIDUAL	= 'individual';
	const TYPE_GROUP		= 'group';
	
	static private $services = null;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PatientsMandates the static model class
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
		return 'patients_mandates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_id, service_type, frequency, duration, split','required'),
			array('patient_id, service_type, frequency, duration, split, created, updated, recommended_count', 'numerical', 'integerOnly'=>true),
			array('type','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, patient_id, service_type, frequency, duration, split, created, updated', 'safe', 'on'=>'search'),
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
			'patient' => array(self::BELONGS_TO, 'Patients', 'patient_id'),
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
			'service_type' => 'Service Type',
			'frequency' => 'Frequency',
			'duration' => 'Duration',
			'split' => 'Split',
			'created' => 'Created',
			'updated' => 'Updated',
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
		$criteria->compare('patient_id',$this->patient_id);
		$criteria->compare('service_type',$this->service_type);
		$criteria->compare('frequency',$this->frequency);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('split',$this->split);
		$criteria->compare('created',$this->created);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if ($this->isNewRecord)
			{
				$this->created = time();
			}else{
				$this->updated 	= time();
			}
			
			if(empty($this->type))
			{
				$this->type = self::TYPE_INDIVIDUAL;
				$this->recommended_count = 1;
			}
			
			return true;
			
		}
		return false;
	}
	
	/**
	 * Return mandate caption for display
	 */ 
	public function getCaption($withPName = false)
	{
		$services = $this->getServices();
		
		$service = isset($services[$this->service_type]) ? $services[$this->service_type] : 'Undefined';
		$caption = $service. '- Freq.: '.$this->frequency.'- Dur.: '.$this->duration.' - Split: '.($this->split ? 'Yes' : 'No').' - '.ucfirst($this->type).($this->type==self::TYPE_GROUP ? ':'.$this->recommended_count : '');
		
		if($withPName)
		{
			$caption = $this->patient->getFullName().' - '.$caption;
		}
		return $caption;
	}
	
	
	public function getServices()
	{
		if(!self::$services)
		{
			self::$services = CHtml::listData(FieldsValues::model()->findAll(array('condition'=>'form_field_id='.Yii::app()->params['services_field_id'])),'id','form_field_title');
		}
		
		return self::$services;
	}
	
	static public function getTypes()
	{
		return array(
			self::TYPE_INDIVIDUAL =>'Individual',
			self::TYPE_GROUP =>'Group',
		);
	}
}