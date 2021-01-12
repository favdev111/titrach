<?php

/**
 * This is the model class for table "form_fields".
 *
 * The followings are the available columns in table 'form_fields':
 * @property string $id
 * @property string $form_id
 * @property string $form_page_id
 * @property string $name
 * @property string $title
 * @property string $type
 * @property string $sort_order
 * @property string $class
 * @property string $html_attrs
 * @property integer $status
 * @property integer $is_searchable
 * @property integer $is_browsable
 * @property string $meta_info
 * @property integer $related_on
 * @property integer $browse_order
 *
 * The followings are the available model relations:
 * @property FieldsValues[] $fieldsValues
 * @property Forms $form
 * @property FormsPages $formPage
 * @property FormFieldsValidationRules[] $formFieldsValidationRules
 * @property FormsEntittiesValues[] $formsEntittiesValues
 */
class FormFields extends CActiveRecord
{
	
	const TYPE_text="text";
	const TYPE_datepicker = "datepicker";
	const TYPE_timepicker = "timepicker";
	const TYPE_textarea="textarea";
	const TYPE_checkbox="checkbox";
	const TYPE_radio="radio";
	const TYPE_select="select";
	const TYPE_button="button";
	const TYPE_submit="submit";
	const TYPE_reset="reset";
	const TYPE_hidden="hidden";
	const TYPE_file="file";
	const TYPE_image="image";
	const TYPE_model = "model";
	const TYPE_mandate = "mandate";
	
	
	protected $_save_with = array();
	
	
	public $field_values;
    public $valid_rules;
	

	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormFields the static model class
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
		return 'form_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('form_id, form_page_id, name, title, is_searchable, is_browsable', 'required'),
			array('status, is_searchable, is_browsable, browse_order', 'numerical', 'integerOnly'=>true),
			array('form_id, form_page_id', 'length', 'max'=>10),
			array('name, title, class,related_on', 'length', 'max'=>255),
			array('type', 'length', 'max'=>12),
			array('sort_order', 'length', 'max'=>11),
			array('html_attrs','length', 'max'=>1024),
			array('meta_info','checkMeta'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_id, form_page_id, name, title, type, sort_order, class, html_attrs, status, is_searchable, is_browsable', 'safe', 'on'=>'search'),
		);
	}

	
	public function checkMeta($attribute,$params)
	{
		$meta = $this->$attribute;
		foreach((array)$meta as $key=>$val)
		{
			//TODO: add more strong check
			$meta[$key] =strip_tags(trim($val));
		}
		$this->$attribute  = $meta;
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'fieldsValues' => array(self::HAS_MANY, 'FieldsValues', 'form_field_id'),
			'form' => array(self::BELONGS_TO, 'Forms', 'form_id'),
			'formPage' => array(self::BELONGS_TO, 'FormsPages', 'form_page_id'),
			'formFieldsValidationRules' => array(self::HAS_MANY, 'FormFieldsValidationRules', 'form_field_id'),
			'formsEntittiesValues' => array(self::HAS_MANY, 'FormsEntittiesValues', 'form_field_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_id' => 'Form',
			'form_page_id' => 'Form Page',
			'name' => 'Name',
			'title' => 'Title',
			'type' => 'Type',
			'sort_order'=>'Sort Order',
			'class'=>'Class',
			'html_attrs'=>'Html Attributes',
			'status'=>'Status',
            'is_searchable'=>'Searchable',
            'is_browsable'=>'Browsable',
			'related_on'=>'Related on field',
			'browse_order'=>'Browse order',
			'meta_info[class_name]'=>'Class Name',
			'meta_info[class_field_value]'=>'Value field name',
			'meta_info[class_field_label]'=>'Label field name',
			'meta_info[class_field_controls]'=>'Controls for element',
			'meta_info[class_search_fields]'=>'Fields for search',
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
		$criteria->compare('form_id',$this->form_id,true);
		$criteria->compare('form_page_id',$this->form_page_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('sort_order',$this->sort_order,true);
		$criteria->compare('class',$this->class,true);
		$criteria->compare('html_attrs',$this->html_attrs,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('is_searchable',$this->is_searchable);
		$criteria->compare('is_browsable',$this->is_browsable);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>false
		));
	}
	
	public function setSaveWith($arr){
		$this->_save_with = array_merge($this->_save_with,$arr);
	}
	
	public function clearSaveWith($arr){
		$this->_save_with = array();
	}
	
    public function getRulesIdAsArray(){
		$result = array();
		foreach((array)$this->formFieldsValidationRules as $rule){
			$result[] = $rule->validation_rule_id ;
		}
		return $result;
	}
        
    /**
	 *Get instance of FieldsValues class from related records
	 *@param int $id 
	 *@return FieldsValues 
	 */
	public function getValuesObjByID($id)
	{
		if(!empty($this->fieldsValues))
		{
			foreach($this->fieldsValues as $val)
			{
				if($val->id == $id)
					return $val;
			}
		}
		
		return false;
	}
	
	
	public function is_multiple()
	{
		if(in_array($this->type,array(self::TYPE_checkbox,self::TYPE_select,self::TYPE_radio)))
			return true;
		return false;
	}
	
	protected function afterFind()
	{
		if(is_string($this->meta_info))
			$this->meta_info = unserialize($this->meta_info);
	}
	
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if(is_array($this->meta_info))
			{
				$this->meta_info = serialize($this->meta_info);
			}
			return true;
		}
		return false;
	}
	
	protected function afterSave(){
        parent::afterSave();
 
 		$errors = array();
		if(in_array('field_values',$this->_save_with)){
			$ids = array();
			foreach($this->field_values as $key=>$val){
				$val->form_field_id = $this->id;
				if($val->save())
					$ids[] = $val->id;
				$errors +=$val->getErrors();
			}
			if( !$this->isNewRecord){
				FieldsValues::model()->deleteAll('form_field_id=:formFieldId AND id NOT IN('.implode(',',$ids).')',array(':formFieldId'=>$this->id));
			}			
		}
		if(in_array('valid_rules',$this->_save_with)){
			if( !$this->isNewRecord){
				FormFieldsValidationRules::model()->deleteAll('form_field_id=:formFieldId',array(':formFieldId'=>$this->id));
			}
			foreach((array)$this->valid_rules as $val){

				$val->form_field_id = $this->id;
				$val->save();
				$errors +=$val->getErrors();
			}
		}                
		$this->addErrors($errors);
		
		if(is_string($this->meta_info))
		{
			$this->meta_info = unserialize($this->meta_info);
		}
    }	
	
	
	static function getFieldsType(){
		return array (
			self::TYPE_text=>'text',
			self::TYPE_textarea=>'textarea',
			self::TYPE_checkbox=>'checkbox',
			self::TYPE_radio=>'radio',
			self::TYPE_select=>'select',
			self::TYPE_button=>'button',
			self::TYPE_submit=>'submit',
			self::TYPE_reset=>'reset',
			self::TYPE_hidden=>'hidden',
			self::TYPE_file=>'file',
			self::TYPE_image=>'image',
			self::TYPE_model=>'model',
			self::TYPE_datepicker =>'datepicker',
			self::TYPE_timepicker=>'timepicker',
			self::TYPE_mandate =>'mandate',
		);
	}
	
	static function getFieldsAsJsArray($form_id = false)
	{
		$fields = $form_id ?
						self::model()->findAll('form_id = :fid',array(':fid'=>$form_id)) : self::model()->findAll();
		$js = array();
		foreach($fields as $f)
		{
			$js[] = '"'.$f->name.'"';
		}
		$js = '['.implode(',',$js).']';
		return $js;
	}
	
}