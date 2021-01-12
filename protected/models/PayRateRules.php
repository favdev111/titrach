<?php

/**
 * This is the model class for table "pay_rate_rules".
 *
 * The followings are the available columns in table 'pay_rate_rules':
 * @property integer $id
 * @property integer $pay_rate_id
 * @property string $form_field_id
 * @property integer $form_field_value_id
 * @property integer $is_multiple
 * @property integer $rule
 * @property string $additional_rate
 * @property integer $sort_order
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Provider $provider
 */
class PayRateRules extends CActiveRecord
{
	
	const RULE_ADD 		= 1;
	const RULE_DEDUCT 	= 2;
	const RULE_MULTIPLY = 3;
	const RULE_DEVIDE	= 4;
	const RULE_PERCENT	= 5;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PayRateRules the static model class
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
		return 'pay_rate_rules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pay_rate_id,form_field_id, additional_rate, rule','required'),
			array('id, pay_rate_id, form_field_value_id, sort_order, is_multiple, rule', 'numerical', 'integerOnly'=>true),
			array('form_field_id, updated', 'length', 'max'=>45),
			array('additional_rate', 'length', 'max'=>13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pay_rate_id, form_field_id, sort_order, form_field_value_id, is_multiple, rule, additional_rate, updated', 'safe', 'on'=>'search'),
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
			'payRate' => array(self::BELONGS_TO, 'PayRate', 'pay_rate_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'provider_id' => 'Provider',
			'form_field_id' => 'Form Field',
			'form_field_value_id' => 'Form Field Value',
			'is_multiple' => 'Is Multiple',
			'rule' => 'Rule',
			'additional_rate' => 'Additional Rate',
			'sort_order'=>'Sort Order',
			'updated' => 'Updated',
		);
	}
	
	/**
	 * Before Save
	 */
	public function beforeSave(){
		if(parent::beforeSave()){
			$this->updated = time();
			return true;
		}
		return false;
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
		$criteria->compare('pay_rate_id',$this->pay_rate_id);
		$criteria->compare('form_field_id',$this->form_field_id,true);
		$criteria->compare('form_field_value_id',$this->form_field_value_id);
		$criteria->compare('is_multiple',$this->is_multiple);
		$criteria->compare('rule',$this->rule);
		$criteria->compare('additional_rate',$this->additional_rate,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getRateRules()
	{
		return array(
			self::RULE_ADD => '+',
			self::RULE_DEDUCT => '-',
			self::RULE_DEVIDE => '/',
			self::RULE_MULTIPLY => '*',
			self::RULE_PERCENT => '%',
		);
	}
	
	public static function applyRule($rate,$addRate,$rule)
	{
		switch($rule){
			case self::RULE_ADD :
				return self::applyRuleAdd($rate,$addRate);
			break;
			case self::RULE_DEDUCT :
				return self::applyRuleDeduct($rate,$addRate);
			break;
			case self::RULE_MULTIPLY :
				return self::applyRuleMultiply($rate,$addRate);
			break;
			case self::RULE_DEVIDE :
				return self::applyRuleDevide($rate,$addRate);
			break;
			case self::RULE_PERCENT :
				return self::applyRulePercent($rate,$addRate);
			break;		
			default:
				return $rate;
		}
		
	}
	
	public static function applyRuleAdd($rate,$addRate)
	{
		return $rate + $addRate;
	}
	
	public static function applyRuleDeduct($rate,$addRate)
	{
		return $rate - $addRate;
	}
	
	public static function applyRuleMultiply($rate,$addRate)
	{
		return $rate * $addRate;
	}	
	
	public static function applyRuleDevide($rate,$addRate)
	{
		if(!$addRate) $addRate = 1;
		return $rate / $addRate;
	}
	
	public static function applyRulePercent($rate,$addRate)
	{
		return $rate + ($rate /100) * $addRate;
	}	
	
	
	
	
	
	
}