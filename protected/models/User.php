<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $role
 * @property string $status
 * @property string $created
 * @property string $last_login
 * @property array $related_providers
 */
class User extends CActiveRecord
{
	
	const STATUS_ACTIVE		= 'active';
	const STATUS_DISABLED	= 'disabled';
	const STATUS_BLOCKED	= 'blocked';
	const STATUS_DELETED	= 'deleted';
	
	const ROLE_GUEST 		= 'guest';
	const ROLE_MANAGER		= 'manager';
	const ROLE_ADMIN		= 'administrator';
	const ROLE_DOCTOR		= 'doctor';
	
	
	private $_tmp_providers = null;
	
	public $new_password;
	public $password_confirmation;

	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Users the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('first_name,last_name,email,status', 'required'),
			array('first_name, last_name, email', 'length', 'max'=>70),
        	array('password','required','on'=>'create'),
            array('default_form','type','type'=>'integer'),
			array('allowed_forms, related_providers','type','type'=>'array'),
			array('password','safe','on'=>'edit'),
            array('email', 'email'),
			array('role', 'length', 'max'=>50),
			array('status', 'length', 'max'=>8),
			array('last_login', 'safe'),
			array('password_confirmation, new_password', 'length', 'max' => 64),
			array('password','required','on'=>'profile'),
			array('email,status','safe', 'on'=>'profile'),
			array('password_confirmation','compare','compareAttribute'=>'new_password','on'=>'profile'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, first_name, last_name, email, password, role, status, created, last_login', 'safe', 'on'=>'search'),
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
			'email' => 'Email',
			'password' => 'Password',
			'role' => 'Role',
			'status' => 'Status',
			'created' => 'Created',
			'last_login' => 'Last Login',
			'default_form' =>'Default Form',
            'allowed_forms'=>'Allowed Forms',
			'related_providers'=>'Related Providers'
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
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('last_login',$this->last_login,true);
		$criteria->compare('default_form',$this->default_form,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
	public function validatePassword($password)	
	{
                return $this->hashPassword($password,$this->salt) === $this->password;
	}
 
	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}
	
	public function getFullName()
	{
		return $this->first_name.' '.$this->last_name;	
	}
	
	
	
	protected function beforeSave()
	{
		parent::beforeSave();
        if(is_array($this->allowed_forms))
			$this->allowed_forms = serialize($this->allowed_forms);
        if(is_array($this->related_providers))
		{
			$this->_tmp_providers = $this->related_providers;
			$this->related_providers = serialize($this->related_providers);
		}	
		if(isset($this->created)) unset($this->created);
		if($this->isNewRecord)
		{			
			$this->salt = $this->email.$this->role;
			$this->password = $this->hashPassword($this->password, $this->salt);
			$this->created = date("Y-m-d H:i:s");
			$this->status = 'active';
		}
		return true;		
	}
	
	protected function afterSave()
	{
		if($this->_tmp_providers)
		{
			$this->related_providers = $this->_tmp_providers;
		}
	}
	
	protected function afterFind(){
		parent::afterFind();
		$this->allowed_forms = unserialize($this->allowed_forms);
		$this->related_providers = unserialize($this->related_providers);
	}
	
	static function getStatuses()
	{
		return array(
			self::STATUS_ACTIVE    => 'Active',
			self::STATUS_DISABLED  => 'Disabled',
			self::STATUS_BLOCKED   => 'Blocked',
			self::STATUS_DELETED   => 'Deleted',
		);
	}
	static function getRoles()
	{
		return array(
			self::ROLE_MANAGER => 'Manager',
			self::ROLE_DOCTOR => 'Doctor',
			self::ROLE_ADMIN  => 'Administrator'
		);
	}
	
        
}