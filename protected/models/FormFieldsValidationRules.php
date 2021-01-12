<?php

/**
 * This is the model class for table "form_fields_validation_rules".
 *
 * The followings are the available columns in table 'form_fields_validation_rules':
 * @property string $form_field_id
 * @property string $validation_rule_id
 * @property string $custom_error_string
 *
 * The followings are the available model relations:
 * @property FormFields $formField
 */
class FormFieldsValidationRules extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormFieldsValidationRules the static model class
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
		return 'form_fields_validation_rules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('custom_error_string', 'required'),
			array('form_field_id', 'length', 'max'=>10),
			array('validation_rule_id', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('form_field_id, validation_rule_id, custom_error_string', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'form_field_id' => 'Form Field',
			'validation_rule_id' => 'Validation Rule',
			'custom_error_string' => 'Custom Error String',
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

		$criteria->compare('form_field_id',$this->form_field_id,true);
		$criteria->compare('validation_rule_id',$this->validation_rule_id,true);
		$criteria->compare('custom_error_string',$this->custom_error_string,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}