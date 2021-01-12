<?php

/**
 * This is the model class for table "fields_values".
 *
 * The followings are the available columns in table 'fields_values':
 * @property string $id
 * @property string $form_field_id
 * @property string $form_field_value
 * @property string $form_field_title
 * @property integer $is_default
 *
 * The followings are the available model relations:
 * @property FormFields $formField
 * @property FormsEntittiesValues[] $formsEntittiesValues
 */
class FieldsValues extends CActiveRecord
{
	
	const SELECT_VALUE_TYPE_BEGING_OF_GROUP = 'begin_of_group';
    const SELECT_VALUE_TYPE_END_OF_GROUP = 'end_of_group';

	
	const SEPARATOR = '|||';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FieldsValues the static model class
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
		return 'fields_values';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('form_field_id, form_field_value, form_field_title', 'required'),
			array('sort_order', 'numerical', 'integerOnly'=>true),
			array('form_field_id', 'length', 'max'=>10),
			array('form_field_title', 'length', 'max'=>255),
			array('is_default','boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_field_id, form_field_value, form_field_title, is_default', 'safe', 'on'=>'search'),
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
			'formField' => array(self::BELONGS_TO, 'FormFields', 'form_field_id'),
			'formsEntittiesValues' => array(self::HAS_MANY, 'FormsEntittiesValues', 'fields_values_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_field_id' => 'Form Field',
			'form_field_value' => 'Form Field Value',
			'form_field_title' => 'Form Field Title',
			'is_default' => 'Is Default',
			'sort_order' => 'Sort Order',
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
		$criteria->compare('form_field_id',$this->form_field_id,true);
		$criteria->compare('form_field_value',$this->form_field_value,true);
		$criteria->compare('form_field_title',$this->form_field_title,true);
		$criteria->compare('is_default',$this->is_default);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
}