<?php

/**
 * This is the model class for table "provider".
 *
 * The followings are the available columns in table 'provider':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $ss_id
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $created
 * @property string $sesis_id
 * @property string $sesis_password
 * @property string $dob
 * @property string $license
 * @property integer $deleted
 */
class Provider extends CActiveRecord
{
	private $_tmp = array();
	static public $disableRestrictions = false;
	public $saveWith = null;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Provider the static model class
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
		return 'provider';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('first_name,last_name,ss_id,','required'),
			array('deleted', 'numerical', 'integerOnly'=>true),
			array('patients','type','type'=>'array'),
			array('first_name, last_name, email, phone, address, city, state, license, sesis_id', 'length', 'max'=>45),
			array('ss_id', 'length', 'max'=>15),
			array('ss_id','numerical'),
			array('sesis_id', 'match' ,
					'pattern'=> '/^[A-Za-z0-9_]+$/u',
					'message'=> 'SESIS ID must be alphanumeric.'
			),
			array('dob','date'),
			array('sesis_password', 'length', 'max'=>128),
			array('zip', 'length', 'max'=>12),
			array('created,saveWith', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, first_name, last_name, ss_id, email, phone, address, city, state, zip, created, deleted', 'safe', 'on'=>'search'),
			array('first_name, last_name','filter','filter'=>'trim'),
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
			'events'=>array(self::HAS_MANY, 'Event', 'id'),
			'patients'=>array(self::HAS_MANY,'Caseload','provider_id','index'=>'patient_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'ss_id' => 'Social Sec. ID',
			'email' => 'Email',
			'phone' => 'Phone',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'zip' => 'Zip',
			'sesis_id'=>'SESIS ID',
			'sesis_password'=>'SESIS Password',
			'license'=>'License',
			'created' => 'Created',
			'deleted' => 'Deleted',
			'dob'		=> 'DOB',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('ss_id',$this->ss_id,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('deleted',$this->deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeSave()
    {
        if (parent::beforeSave()) {
			//Process date
			$this->_tmp['dob'] = $this->dob;
			$this->dob = date('Y-m-d', CDateTimeParser::parse($this->dob,'MM/dd/yyyy'));
            if ($this->isNewRecord) {
				$this->created = date("Y-m-d H:i:s");
            }
            return true;
        }
        return false;
    }

	public function behaviors()
    {
        return array(
            'SoftDeleteBehavior' => array(
                'class' => 'application.behaviors.SoftDeleteBehavior',
            ),
        );
    }

	public function afterSave()
	{
		//restore string representation
		$this->dob = $this->_tmp['dob'];
		
		parent::afterSave();
		if(is_array($this->saveWith))
		{
			if(in_array( 'patients',$this->saveWith))
			{
				Caseload::model()->deleteAll('provider_id=:prid',array(':prid'=>$this->id));

				if(is_array($this->patients))
					foreach($this->patients as $patient)
					{
	
						$case = new Caseload();
						$case->patient_id = (int)$patient;
						$case->provider_id = $this->id;
						if(!$case->save()){
							//var_dump($case->getError());
							//die('here');
							return false;
						}
					}
			}
		}
		return true;
	}
	
	public function afterFind()
	{
		parent::afterFind();
		$this->dob = date('m/d/Y',CDateTimeParser::parse($this->dob,'yyyy-MM-dd'));
	}
	
		
	public function getFullName()
	{
		return $this->first_name.' '.$this->last_name;
	}
	
	public function getFullAddress()
	{
		return $this->address.', '.$this->city.', '.$this->state.', '.$this->zip;
	}
	
	public function scopes()
	{
		return array(
            //restrict all providers for only allowed
        );		
	}
	
	public function defaultScope()
	{
		return array(
			'alias' => $this->tableName(),
			'condition' =>Yii::app()->user->role== User::ROLE_ADMIN || self::$disableRestrictions ? '1=1' : $this->tableName() . '.id IN ('.(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' ).')',			
		);
	}
	
}