<?php

/**
 * This is the model class for table "forms_pages".
 *
 * The followings are the available columns in table 'forms_pages':
 * @property string $id
 * @property string $form_id
 * @property string $name
 * @property string $title
 * @property string $sort_order
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property FormFields[] $formFields
 * @property Forms $form
 */
class FormsPages extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormsPages the static model class
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
		return 'forms_pages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('form_id, name, title, sort_order', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('form_id, sort_order', 'length', 'max'=>10),
			array('name, title', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_id, name, title, sort_order, status', 'safe', 'on'=>'search'),
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
			'formFields' => array(self::HAS_MANY, 'FormFields', 'form_page_id'),
			'form' => array(self::BELONGS_TO, 'Forms', 'form_id'),
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
			'name' => 'Name',
			'title' => 'Title',
			'sort_order' => 'Sort Order',
			'status' => 'Status',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('sort_order',$this->sort_order,true);
		$criteria->compare('status',$this->status);
		$criteria->order = 'sort_order';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function getFieldsAsArrayDataProvider()
	{
		$fields = array();
		foreach($this->formFields as $field)
		{
			$fields[] = array(
				'id'=>$field->id,
				'name'=>$field->name,
				'title'=>$field->title,
				'type'=>$field->type,
				'status'=>Common::getStatusString($field->status),
				'is_searchable'=>Common::getYesNoString($field->is_searchable),
				'is_browsable'=>Common::getYesNoString($field->is_browsable),	
			);
		}
		return new CArrayDataProvider($fields,array('pagination'=>false));
	}
	
	static function getFormPages($fid){
		$pages = array();
		foreach(self::model()->findAll('form_id=:formID',array(':formID'=>(int)$fid)) as $page){
			$pages[$page['id']] = $page['title'];
		}
		return $pages;
	}
	
	
	//TODO implement via standard yii method. Этот велосипед был написан из-за незнания всех особенностей уии и нехватки времени
	public function validatePage($data){
		$errors = array();
		foreach($this->formFields as $field){
			foreach($field->formFieldsValidationRules as $rule){
				$method = $rule->validation_rule_id.'Validator';
				if(!FieldValidator::$method($data[$field->name],$field)){
					$errors[$field->name] = !empty($rule->custom_error_string) ?
								$rule->custom_error_string : str_replace('{{field_name}}',$field->title,Yii::app()->params['validation_rules'][$rule->validation_rule_id]);
					break;
				}
			}
		}
		return (count($errors)>0 ? $errors: true);
	}


}