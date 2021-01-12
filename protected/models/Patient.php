<?php

/**
 * This is the model class for table "patients".
 *
 * The followings are the available columns in table 'patients':
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $phone
 * @property string $dob
 * @property string $gender
 * @property string $student_id
 * @property string $parent_guardian
 * @property string $contact_person
 * @property string $contact_phone
 * @property string $created
 * @property string $service_district
 * @property string $mandated_frequency
 * @property string $mandated_duration
 *
 * The followings are the available model relations:
 * @property FormsEntities[] $formsEntities
 */
class Patient extends CActiveRecord
{
	public static $disableDefaultScope = false;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Patients the static model class
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
		return 'patients';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lastname', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, address, city', 'length', 'max'=>255),
			array('state, dob, gender,student_id', 'length', 'max'=>45),
			array('contact_person,contact_phone', 'length', 'max'=>50),
			array('parent_guardian', 'length', 'max'=>100),
			array('service_district','length','max'=>64),
			array('zipcode', 'length', 'max'=>10),
			array('created','safe'),
			array('phone', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, firstname, providers, lastname, address, city, service_district, state, zipcode, phone, dob, gender, student_id,parent_guardian,contact_person,contact_phone,created', 'safe', 'on'=>'search'),
			array('firstname, lastname','filter','filter'=>'trim')
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
			'formsEntities' => array(self::HAS_MANY, 'FormsEntities', 'patients_id'),
			'mandates'=>array(self::HAS_MANY , 'PatientsMandates','patient_id','order'=>'created ASC','index'=>'service_type'),
			'caseload'=>array(self::HAS_MANY,'Caseload','patient_id'),
			'providers'=>array(self::HAS_MANY,'Provider',array('provider_id'=>'id'),'through'=>'caseload'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'zipcode' => 'Zipcode',
			'phone' => 'Phone',
			'dob' => 'Dob',
			'gender' => 'Gender',
			'student_id' => 'Student ID',
			'parent_guardian' => 'Parent/Guardian',
			'created' => 'Created',
			'contact_person' => 'Contact Person',
			'contact_phone' => 'Contact Phone',
			'service_district' =>'Service District',
			//'mandated_frequency'=>'Frequency',
			//'mandated_duration'=>'Duration'
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
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('dob',$this->dob,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('parent_guardian',$this->parent_guardian);
		$criteria->compare('contact_person',$this->contact_person);
		$criteria->compare('contact_phone',$this->contact_phone);
		$criteria->compare('service_district',$this->service_district);
		if(is_numeric($this->providers))
		{
			$criteria->compare('provider.id',$this->providers);
			$criteria->with = array('providers');
			$criteria->together = true;
		}
		
		if(!empty($this->created))
			$criteria->compare('created',DateTime::createFromFormat('m/d/Y',$this->created)->getTimestamp());
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['rows_per_page'],
			),
		));
	}
	
	public function beforeSave(){
		if(parent::beforeSave()){
			if($this->isNewRecord)
			{
				$this->created = time();
			}
			return true;
		}
		return false;
	}
	
	public function getFullName($with_DOB = false,$with_sid = false){
		$fn = $this->lastname.', '.$this->firstname.($with_DOB ? '('.$this->dob.')':'');
		if($with_sid)
		{
			$fn .= '-'.$this->student_id;
		}
		return $fn;
	}
	
	//Alias for function getFullName with student_id, required for search
	public function getFullNameWithSid()
	{
		return $this->getFullName(false,true);
	}
	
	
	public function getFullAddress()
	{
		return $this->address.','.$this->city.','.$this->state.','.$this->zipcode;
	}
	
	
	public function getInitialFormID()
	{
		$entity  = FormsEntities::model()->find(array('order'=>'timestamp','condition'=>'patient_id =:pid AND parent IS NULL AND form_id=:fid','params'=>array(':pid'=>$this->id,':fid'=>Yii::app()->params['main_form_id'])));
		return $entity ? $entity->id : 0;
	}
	
	static function add($data,$tmp_pat_id = false,$form)
	{
		$attrs = array();
		foreach(Yii::app()->params['relations'] as $key=>$rel) 
		{
			if(!empty($data[$rel]))
			{
				$field = $form->formFields[$rel];
				if($field->is_multiple()){
					$vv = array();
					foreach((array)$data[$rel] as $d)
					{
						$vv[] = FieldsValues::model()->findByPk($d)->form_field_value;
					}
					$val = implode(';',$vv);
				}else{
					$val = $data[$rel];
				}
				//if(is_array($data[$rel]))
				//{
				//	$val = implode(';',$data[$rel]);
				//}else{
				//	
				//}
				$attrs[$key] = $val;
			}
		}
		$patient = new Patient();
		$patient->unsetAttributes();
		$patient->attributes = $attrs;
		if($patient->save()){
			$tmp_dir = Patient::getPatientFolder($tmp_pat_id);
			if(is_dir($tmp_dir))
			{
				CFileHelper::copyDirectory($tmp_dir,Patient::getPatientFolder($patient));
			}
		}
		return $patient;
	}
	
	public function defaultScope()
	{
		return !self::$disableDefaultScope ? array(
			'alias' => $this->tableName(),
			'condition' =>Yii::app()->user->role== User::ROLE_ADMIN ? '1=1' : $this->tableName() . '.id IN ( SELECT patient_id FROM caseload as cs_check_1 WHERE provider_id IN ( ' .(Yii::app()->user->getRelatedProviders() ? implode(',',Yii::app()->user->getRelatedProviders()) : '0' ).'))',			
		) : array('alias'=>$this->tableName());
	}	
	
	
	
	static function updateFromInitial($patient,$entity,$form)
	{
		foreach(Yii::app()->params['relations'] as $key=>$val)
		{
			if(empty($val))
				continue;
			$field = $form->formFields[$val];
			if(!$field)
				continue;
			if($field->is_multiple()){
				$vv = array();
				foreach((array)$entity->formsEntitiesFields[$field->id]->fieldValues as $v)
				{
					$vv[] = $v->form_field_value;
				}
				$val = implode(';',$vv);
			}else{
				$val = $entity->formsEntitiesFields[$field->id]->value;
			}
			$attrs[$key] = $val;
		}
		$folder = Patient::getPatientFolder($patient);
		$patient->attributes = $attrs;
		if($patient->save())
		{
			$new_dir  = Patient::getPatientFolder($patient);
			if($folder != $new_dir && is_dir($folder)){
				CFileHelper::copyDirectory($folder,Patient::getPatientFolder($patient));
			}
		}
		return $patient;
	}
	
	static function getPatientFolder($id){
		$model = null;
		$path = '';
		if($id instanceof Patient){
			$model = $id;
		}elseif(is_numeric($id)){
			$model = Patient::model()->findByPk((int)$id);
			if(!$model){
				throw new CHttpException(404,'The requested student does not exist.');
			}
			
		}else{
			$path  = remove_trailing_slash(Yii::app()->params['tmp_file_storage']).DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
		}
		
		if($model){
			$dir = sanitize_file_name('std_'.$model->id.'_'.$model->firstname.'_'.$model->lastname.'_'.$model->dob);
			$path = remove_trailing_slash(Yii::app()->params['file_storage']).DIRECTORY_SEPARATOR.$dir;
		}
		return str_ireplace(array('{{CONST_APP_PATH}}','\\','/'),array(realpath(Yii::app( )->getBasePath( ).'/..'),DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR),$path);
	}
	
	
	static function disableDefaultScope($disable = true)
	{
		self::$disableDefaultScope = $disable;
	}
	
}