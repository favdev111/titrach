<?php

/**
 * This is the model class for table "forms".
 *
 * The followings are the available columns in table 'forms':
 * @property string $id
 * @property string $form_name
 * @property string $form_title
 * @property integer $status
 * @property string $file_storage_path
 * @property string $form_prefix
 * @property string $parent
 * @property integer $save_to_directory
 * @property integer $billRelation
 * @property integer $prefix	
 * @property integer $postfix
 * @property integer $current_counter
 * @property integer $meta
 * @property boolean is_printable
 *
 * The followings are the available model relations:
 * @property FormFields[] $formFields
 * @property FormsEntities[] $formsEntities
 * @property FormsPages[] $formsPages
 */
class Forms extends CActiveRecord
{
	
	private $_tmp_meta = array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Forms the static model class
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
		return 'forms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('form_name, form_title, file_storage_path, form_program_title', 'length', 'max'=>255),
            array('prefix, postfix', 'length', 'max'=>10),
			            //array('parent','exist','attributeName'=>'id','className'=>'Forms','allowEmpty'=>true),
            array('save_to_directory, billRelation,is_printable','boolean','strict'=>'false'),
			array('status, parent, meta, current_counter,','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_name, form_title, file_storage_path', 'safe', 'on'=>'search'),
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
			'formFields' => array(self::HAS_MANY, 'FormFields', 'form_id','index'=>'name'),
			'formsEntities' => array(self::HAS_MANY, 'FormsEntities', 'form_id'),
			'formsPages' => array(self::HAS_MANY, 'FormsPages', 'form_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_name' => 'Form Name',
			'form_title' => 'Form Title',
			'status' => 'Status',
			'file_storage_path' => 'File Storage Path',
			'form_prefix' => 'Form Prefix',
			'parent' => 'Parent',
			'save_to_directory' => 'Save To Directory',
			'is_printable' => 'Printable',
			'form_program_title' => 'Entity related title',
			'billRelation'=>'Is Bill',
			'prefix'=>'Prefix',
			'postfix'=>'Postfix',
			'current_counter'=>'Current count of form'
			
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
		$criteria->compare('form_name',$this->form_name,true);
		$criteria->compare('form_title',$this->form_title,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('file_storage_path',$this->file_storage_path,true);
		$criteria->compare('form_prefix',$this->form_prefix,true);
		$criteria->compare('parent',$this->parent,true);
		$criteria->compare('save_to_directory',$this->save_to_directory);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 *Update view for form in DB
	 */
	public function regenerateView()
	{
		
	}
	
	
	
	protected function afterFind()
	{
		parent::afterFind();
		if(!empty($this->meta))
			$this->_tmp_meta = $this->meta = unserialize($this->meta);
	}
		
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if(is_array($this->meta))
			{
				$this->_tmp_meta = $this->meta;
				$this->meta = serialize($this->meta);
			}
			return true;
		}
		return false;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		$this->meta = $this->_tmp_meta;
	}
	
	
	
	public function getEntityRelatedTitle($entity)
	{
		preg_match_all('/\{\{FIELD_([^\}]*)?\}\}/i',$this->form_program_title,$matches);
        $title = $this->form_program_title;
        if($matches){
            //$entity->readFromDB($this->_entity_id,$matches[1]);
            foreach($matches[1] as $key){
				//var_dump($this->formFields);
				$val = '';
				//var_dump($entity->formsEntitiesFields);
				if(isset($this->formFields[$key]) &&  !empty($entity->formsEntitiesFields[$this->formFields[$key]->id]))
				{
					$field = $entity->formsEntitiesFields[$this->formFields[$key]->id];
					if($field->multiple)
					{
						$val = implode(',',$field->getValuesAsArray());	
					}else{
						$val = $field->value;
					}
				}
                $title = str_ireplace('{{FIELD_'.$key.'}}',$val,$title);				
            }
        }
		return $title;
	}
	
	
	/**
	 *Check for meta
	 *@param string $meta
	 *@return bool
	 */
	public function hasMeta($meta)
	{
		return !empty($this->meta[$meta]);
	}
	
	/**
	 *Check for meta
	 *@param string $meta
	 *@return string | null  meta value
	 */	
	public function getMeta($meta)
	{
		if($this->hasMeta($meta))
			return $this->meta[$meta];
		else
			return null;
	}
	
	/**
	 * Return names of all fields, attached to this form type
	 * @return stdClass
	 */
	public function getFieldsNames()
	{
		$fields = array();
		foreach($this->formFields as $field)
		{
			$fields[$field['name']] = $field;
		}
		return $fields;
	}
	
	
	/**return all forms that exist at system
	 *@return array() id->title
	 */
	static function getForms($onlyActive = false,$top_level = false){
		$forms = array();
		$result= array();
		$criteria = new CDbCriteria();
		$params = array();
		if($onlyActive)
		{
			$criteria->addCondition('status=:Status');
			$params[':Status'] = Common::STATUS_ACTIVE;
		}
		if($top_level){
			$criteria->addCondition('parent = 0');
		}
		$criteria->params = $params;

		$result = self::model()->findAll();

		foreach( $result as $form){
			$forms[$form->id] = $form['form_title'];
		}
		return $forms;
	}
	
	
	/**
	 *Get Fields Values as array by form_id and field_id
	 *@param int $form_id ID of form
	 *@param mixed int|string $field Field name or ID
	 *@return array field values
	 */
	static function getFormFieldValuesAsArray($form_id,$field,$index = 'form_field_value')
	{
		$values = array();
		$form = null;
		if(is_numeric($field))
		{
			$with = array(array('formFields'=>array('index'=>'id')));

		}else{
			$with = 'formFields';
		}
		$form = self::model()->with($with)->findByPk($form_id);
		if($form && !empty($form->formFields[$field])){
			foreach((array)$form->formFields[$field]->fieldsValues as $key=>$v)
			{
				$values[$v->$index] = $v->form_field_title; 
			}
		}
		return $values;
	}
	
	/**
	 * Get list of fields with values for services forms for pay rates module
	 */
	static function getFieldsForPayRates()
	{
		$sql = 'SELECT fv.form_field_title, fv.id as fv_id, ff.id as ff_id, ff.name, ff.title
				FROM fields_values as fv
				INNER JOIN form_fields as ff ON fv.form_field_id = ff.id
				WHERE ff.form_id = 2 AND ff.status = '.Common::STATUS_ACTIVE.'
				ORDER BY ff.title';
		
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$result = array();
		foreach($rows as $row)
		{
			if(!isset($result[$row['ff_id']]))
			{
				$result[$row['ff_id']]['id']=$row['ff_id'];
				$result[$row['ff_id']]['name']=$row['name'];
				$result[$row['ff_id']]['title']=$row['title']	;
			}
			$result[$row['ff_id']]['values'][]=array('id'=>$row['fv_id'],'title'=>$row['form_field_title']);
		}
		
		return $result;
	}

}


























