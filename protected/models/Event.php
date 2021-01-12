<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property string $id
 * @property integer $patient_id
 * @property integer $provider_id
 * @property integer $start_date_timestamp
 * @property integer $end_date_timestamp
 * @property integer $event_type
 * @property string $event_meta
 * @property integer $created
 * @property integer $fulfilled
 * @property integer $updated
 * @property string $reccuring_string
 *
 * The followings are the available model relations:
 * @property Patients $patient
 * @property Provider $provider
 */
class Event extends CActiveRecord
{
	const EVENT_TYPE_NEW_FORM = 1;
		
	public $_busyEvent = null;
	
	public $_forceSave = false;
	
	public $_getAllBusy = false;
	
	public $_busyEvents = array();
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Event the static model class
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
		return 'event';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('patient_id, provider_id, start_date_timestamp, end_date_timestamp','required'),
			array('patient_id, provider_id, start_date_timestamp, end_date_timestamp, event_type, created, fulfilled, updated', 'numerical', 'integerOnly'=>true),
			array('event_meta', 'length', 'max'=>255),
			array('reccuring_string', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, patient_id, provider_id, start_date_timestamp, end_date_timestamp, event_type, event_meta, created, fulfilled, updated, reccuring_string', 'safe', 'on'=>'search'),
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
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
			'provider' => array(self::BELONGS_TO, 'Provider', 'provider_id'),
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
			'provider_id' => 'Provider',
			'start_date_timestamp' => 'Start Date Timestamp',
			'end_date_timestamp' => 'End Date Timestamp',
			'event_type' => 'Event Type',
			'event_meta' => 'Event Meta',
			'created' => 'Created',
			'fulfilled' => 'Fulfilled',
			'updated' => 'Updated',
			'reccuring_string' => 'Reccuring String',
		);
	}

	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			//Check 
			if($this->start_date_timestamp > $this->end_date_timestamp)
			{
				$this->addError('wrong_end_time','The end time cannot be earlier than the start time.  Please input the correct time.');
				return false;
			}
			
			//Check start adn end time
			if($this->start_date_timestamp == $this->end_date_timestamp)
			{
				$this->addError('cant_be_same','End time should be different than start time');
				return false;
			}
			if(!$this->isTimeFree())
			{
				//$this->addError('','This time is already scheduled' );
				if(!$this->_forceSave)
					return false;
				else
				{
					if($this->provider_id != $this->_busyEvent->provider_id || $this->patient_id == $this->_busyEvent->patient_id || ($this->start_date_timestamp !=$this->_busyEvent->start_date_timestamp || $this->end_date_timestamp != $this->_busyEvent->end_date_timestamp))
						return false;
				}
			}
			if ($this->isNewRecord)
			{
				$this->created = time();
			}else{
				$this->updated 	= time();
			}
			
			return true;
			
		}
		return false;
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
		$criteria->compare('provider_id',$this->provider_id);
		$criteria->compare('start_date_timestamp',$this->start_date_timestamp);
		$criteria->compare('end_date_timestamp',$this->end_date_timestamp);
		$criteria->compare('event_type',$this->event_type);
		$criteria->compare('event_meta',$this->event_meta,true);
		$criteria->compare('created',$this->created);
		$criteria->compare('fulfilled',$this->fulfilled);
		$criteria->compare('updated',$this->updated);
		$criteria->compare('reccuring_string',$this->reccuring_string,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function isReccuring()
	{
		return !empty($this->reccuring_string) ? true : false;
	}
	
	
	public function isTimeFree()
	{
		if($this->_getAllBusy)
		{
			$this->_busyEvents = $events = self::model()->findAll(array(
						'condition'=>'start_date_timestamp <= :sTime AND end_date_timestamp >:sTime AND (patient_id  = :pid OR provider_id = :prid)',
						'params'=>array(':sTime'=>$this->start_date_timestamp,':pid'=>$this->patient_id,':prid'=>$this->provider_id),
					));
			reset($events);
			$event = count($events) > 0 ? current($events) : null;

		}else{
		
			$event = self::model()->findAll(array(
						'condition'=>'start_date_timestamp <= :sTime AND end_date_timestamp >:sTime AND (patient_id  = :pid OR provider_id = :prid)',
						'params'=>array(':sTime'=>$this->start_date_timestamp,':pid'=>$this->patient_id,':prid'=>$this->provider_id),
					));
		}

		if(count($event) > 5)
		{
			$this->_busyEvent = $event;
			$this->addError('already_scheduled','This time is already scheduled 5 times for this patient/provider . Please select other time');
			return false;
		}
		return true;
	}
	
	
	public function fulfilled($meta = false)
	{
		
		$fields['fulfilled']= 1;
		if($meta)
			$fields['event_meta'] = $meta;
		$this->saveAttributes($fields);			
		if($this->isReccuring())
		{
			$event = new Event();
			$event->fulfilled = 0;
			$event->patient_id = $this->patient_id;
			$event->provider_id = $this->provider_id;
			$event->event_type = $this->event_type;
			$duration = $this->getDuration(); 
			$event->reccuring_string = $this->reccuring_string;
			$event->start_date_timestamp = strtotime($this->reccuring_string.' '.date('h:i A',$this->start_date_timestamp),$this->start_date_timestamp);
			$event->end_date_timestamp = $event->start_date_timestamp + $duration;
			if($event->save())
			{
				return true;
			}else{
				return false;
			}
		}
		return true;
	}
	
	public function getDuration($inMinutes = false)
	{
		return ($this->end_date_timestamp-$this->start_date_timestamp)/ ($inMinutes ? 60 : 1);
	}
	
	public function scopes()
	{
		return array(
            'onlyAllowed'=>array(
				'condition'=>Yii::app()->user->role == User::ROLE_ADMIN ? '1=1' : $this->getTableAlias() . '.provider_id IN ('.(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' ).')',
			)
			
		);
	}
	
	
	static function getEventDurations()
	{
		return array(
			'15'=>'15min',
			'30'=>'30min',
			'45'=>'45min',
			'60'=>'1hr',
			'75'=>'1hr 15min',
			'90'=>'1hr 30min',
			'105'=>'1hr 45min',
			'120'=>'2hrs'
		);
	}
}