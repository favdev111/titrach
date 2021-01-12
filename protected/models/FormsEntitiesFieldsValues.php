<?php

/**
 * This is the model class for table "forms_entities_fields_values".
 *
 * The followings are the available columns in table 'forms_entities_fields_values':
 * @property string $field_value_id
 * @property string $fef_id
 *
 * The followings are the available model relations:
 * @property FieldsValues $fieldValue
 * @property FormsEntittiesFields $fef
 */
class FormsEntitiesFieldsValues extends CActiveRecord
{
	
	const EMPTY_VALUE = 'empty';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormsEntitiesFieldsValues the static model class
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
		return 'forms_entities_fields_values';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('field_value_id, fef_id', 'required'),
			array('field_value_id', 'length', 'max'=>10),
			array('fef_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('field_value_id, fef_id', 'safe', 'on'=>'search'),
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
			'fieldValue' => array(self::BELONGS_TO, 'FieldsValues', 'field_value_id'),
			'fef' => array(self::BELONGS_TO, 'FormsEntittiesFields', 'fef_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'field_value_id' => 'Field Value',
			'fef_id' => 'Fef',
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

		$criteria->compare('field_value_id',$this->field_value_id,true);
		$criteria->compare('fef_id',$this->fef_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}