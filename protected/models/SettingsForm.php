<?php

class SettingsForm extends CFormModel
{
	public $main_form_id;
	public $adminEmail;
	public $file_storage;
	public $tmp_file_storage;
	public $firstname;
	public $lastname;
	public $address;
	public $city;
	public $state;
	public $zipcode;
	public $phone;
	public $dob;
	public $gender;
	public $student_id;
	public $parent_guardian;
	public $contact_person;
	public $contact_phone;
	public $rows_per_page;
	public $service_district;
	public $rates_rate;
	public $rates_setss_1;
	public $rates_setss_2;
	public $rates_setss_3;
	public $rates_setss_4;
	public $rates_setss_5;
	//public $mandated_duration;
	//public $mandated_frequency;
	
	public function rules()
	{
		return array(
			// username and password are required
			array('rates_rate, rates_setss_1, rates_setss_2, rates_setss_3, rates_setss_4, rates_setss_5','type','type'=>'float'),
			array('main_form_id, adminEmail, file_storage, file_storage,firstname, lastname,  gender, dob,rows_per_page', 'required'),
			array('firstname, lastname, address,city, state, zipcode, phone,gender, dob,student_id,parent_guardian,contact_person,contact_phone,service_district', 'exist','attributeName'=>'name','className'=>'FormFields')
		);
	}
	
	
	public function init()
	{
		parent::init();
		
		$data = Yii::app()->getParams();
		if(isset($data['main_form_id']))
			$this->main_form_id = $data['main_form_id'];
		if(isset($data['adminEmail']))
			$this->adminEmail = $data['adminEmail'];
		if(isset($data['file_storage']))
			$this->file_storage = $data['file_storage'];
		if(isset($data['tmp_file_storage']))
			$this->tmp_file_storage = $data['tmp_file_storage'];
		if(isset($data['rows_per_page']));
			$this->rows_per_page= $data['rows_per_page'];				
		
		//Relations
		if(isset($data['relations']['firstname']))
			$this->firstname = $data['relations']['firstname'];
		if(isset($data['relations']['lastname']))
			$this->lastname = $data['relations']['lastname'];
		if(isset($data['relations']['address']))
			$this->address = $data['relations']['address'];
		if(isset($data['relations']['city']))
			$this->city = $data['relations']['city'];
		if(isset($data['relations']['state']))
			$this->state= $data['relations']['state'];	
		if(isset($data['relations']['zipcode']));
			$this->zipcode= $data['relations']['zipcode'];
		if(isset($data['relations']['phone']));
			$this->phone= $data['relations']['phone'];
		if(isset($data['relations']['dob']));
			$this->dob= $data['relations']['dob'];
		if(isset($data['relations']['gender']));
			$this->gender= $data['relations']['gender'];
		if(isset($data['relations']['student_id']));
			$this->student_id= $data['relations']['student_id'];
		if(isset($data['relations']['parent_guardian']));
			$this->parent_guardian= $data['relations']['parent_guardian'];
		if(isset($data['relations']['contact_persomandated_durationn']));
			$this->contact_person= $data['relations']['contact_person'];
		if(isset($data['relations']['contact_phone']));
			$this->contact_phone= $data['relations']['contact_phone'];
		if(isset($data['relations']['service_district']));
			$this->service_district= $data['relations']['service_district'];
		
		//Default Rates
		if(isset($data['rates']['rate']));
			$this->rates_rate= $data['rates']['rate'];
		if(isset($data['rates']['setss_1']));
			$this->rates_setss_1= $data['rates']['setss_1'];
		if(isset($data['rates']['setss_2']));
			$this->rates_setss_2= $data['rates']['setss_2'];
		if(isset($data['rates']['setss_3']));
			$this->rates_setss_3= $data['rates']['setss_3'];
		if(isset($data['rates']['setss_4']));
			$this->rates_setss_4= $data['rates']['setss_4'];
		if(isset($data['rates']['setss_5']));
			$this->rates_setss_5= $data['rates']['setss_5'];			
	
	}
	
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'main_form_id'=>'Main form id',
			'adminEmail'=> 'Administrator Email address',
			'firstname' => 'Field for First Name',
			'lastname' => 'Field for Last Name',
			'address' => 'Field for address',
			'city' => 'Field for city',
			'state' => 'Field for state',
			'zipcode' => 'Field for zipcode',
			'phone' => 'Field for phone',
			'dob' => 'Field for dob',
			'gender' => 'Field for gender',
			'student_id'=>'Field for student_id',
			'parent_guardian' => 'Field for parent/guardian',
			'contact_person' => 'Field for contact person',
			'contact_phone' => 'Field for contact phone',
			'file_storage' =>'Path to students files directory',
			'tmp_file_storage' => 'Path to temporary directory for files',
			'rows_per_page' => 'Rows per Page',
			'service_district' => 'Service District',
			'rates_rate'=>'Default Rate',
			'rates_setts_1'=>'Def. Setss 1 rate',
			'rates_setts_2'=>'Def. Setss 2 rate',
			'rates_setts_3'=>'Def. Setss 3 rate',
			'rates_setts_4'=>'Def. Setss 4 rate',
			'rates_setts_5'=>'Def. Setss 5 rate',
			//'mandated_frequency'=>'Frequency',
			//'mandated_duration' =>'Duration',
 		);
	}
	
	public function save(){
		$data = array(
			'adminEmail'=>$this->adminEmail,
			'main_form_id'=>$this->main_form_id,
			'file_storage'=>$this->file_storage,
			'tmp_file_storage'=>$this->tmp_file_storage,
			'rows_per_page' =>$this->rows_per_page,
			'relations'=>array(
				'firstname'	=>	$this->firstname,
				'lastname'	=>	$this->lastname,
				'address'	=>	$this->address,
				'city'		=>	$this->city,
				'state'		=>	$this->state,
				'zipcode'	=>	$this->zipcode,
				'phone'		=>	$this->phone,
				'gender'	=>	$this->gender,
				'dob'		=>	$this->dob,
				'student_id'	=>	$this->student_id,
				'parent_guardian'	=>	$this->parent_guardian,
				'contact_person'	=>	$this->contact_person,
				'contact_phone'	=>	$this->contact_phone,
				'service_district'=> $this->service_district,
				//'mandated_duration'=>$this->mandated_duration,
				//'mandated_frequency'=>$this->mandated_frequency
			),
			'rates'=>array(
				'rate'=>$this->rates_rate,
				'setss_1'=>$this->rates_setss_1,
				'setss_2'=>$this->rates_setss_2,
				'setss_3'=>$this->rates_setss_3,
				'setss_4'=>$this->rates_setss_4,
				'setss_5'=>$this->rates_setss_5,
			)
		);
		if(Yii::app()->sartparams->saveParams($data))
			return true;
		return false;
	}
}