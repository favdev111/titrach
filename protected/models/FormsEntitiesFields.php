<?php

/**
 * This is the model class for table "forms_entitties_fields".
 *
 * The followings are the available columns in table 'forms_entitties_fields':
 * @property string $id
 * @property string $form_entity_id
 * @property string $form_field_id
 * @property integer $multiple
 * @property string $value
 *
 * The followings are the available model relations:
 * @property FormsEntitiesFieldsValues[] $formsEntitiesFieldsValues
 * @property FormsEntities $formEntity
 * @property FormFields $formField
 */
class FormsEntitiesFields extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormsEntittiesFields the static model class
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
		return 'forms_entities_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			array('value','safe'),
			array('form_entity_id', 'length', 'max'=>10),
			array('form_field_id', 'length', 'max'=>11),
			array('multiple', 'boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, form_entity_id, form_field_id, multiple, value', 'safe', 'on'=>'search'),
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
			'formsEntitiesFieldsValues' => array(self::HAS_MANY, 'FormsEntitiesFieldsValues', 'fef_id'),
			'formEntity' => array(self::BELONGS_TO, 'FormsEntities', 'form_entity_id'),
			'formField' => array(self::BELONGS_TO, 'FormFields', 'form_field_id'),
			'fieldValues'=>array(self::HAS_MANY,'FieldsValues',array('field_value_id'=>'id'),'through'=>'formsEntitiesFieldsValues')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'form_entity_id' => 'Form Entity',
			'form_field_id' => 'Form Field',
			'multiple' => 'Multiple',
			'value' => 'Value',
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
		$criteria->compare('form_entity_id',$this->form_entity_id,true);
		$criteria->compare('form_field_id',$this->form_field_id,true);
		$criteria->compare('multiple',$this->multiple);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getValuesAsArray($label = false)
	{
		$vals = array();
		if(!empty($this->fieldValues)){
			foreach((array)$this->fieldValues as $val)
			{
				if($label)
					$vals[$val->id] = $val->form_field_title;
				else
					$vals[$val->id] = $val->form_field_value;
			}			
		}elseif(!empty($this->formsEntitiesFieldsValues)){
			foreach((array)$this->formsEntitiesFieldsValues as $val)
			{
				$vals[$val->field_value_id] = $val->field_value_id;
			}			
		}
		return $vals;
	}
	
	public function getSingleValue()
	{
		$vals = $this->getValuesAsArray();
		return array_shift($vals);
	}
	
	protected function afterSave()
	{
		$ids = array();
		$errors = array();
		if(!empty($this->formsEntitiesFieldsValues))
		{
			foreach($this->formsEntitiesFieldsValues as $key=>$val){
				$val->fef_id = $this->id;
				if($val->save())
					$ids[] = $val->field_value_id;
				$errors +=$val->getErrors();
			}
			if( !$this->isNewRecord){
				FormsEntitiesFieldsValues::model()->deleteAll('fef_id=:formEntityFieldId AND field_value_id NOT IN('.implode(',',$ids).')',array(':formEntityFieldId'=>$this->id));
			}
		}
		$this->addErrors($errors);
	}
}