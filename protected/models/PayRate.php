<?php

/**
 * This is the model class for table "pay_rate".
 *
 * The followings are the available columns in table 'pay_rate':
 * @property integer $id
 * @property integer $provider_id
 * @property string $rate
 * @property string $setss_1
 * @property string $setss_2
 * @property string $setss_3
 * @property string $setss_4
 * @property string $setss_5
 * @property integer $updated
 *
 * The followings are the available model relations:
 * @property Provider $provider
 */
class PayRate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PayRate the static model class
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
		return 'pay_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate, provider_id', 'required'),
			array('id, provider_id, updated', 'numerical', 'integerOnly'=>true),
			array('rate, setss_1, setss_2, setss_3, setss_4, setss_5', 'length', 'max'=>13),
			array('rate, setss_1, setss_2, setss_3, setss_4, setss_5', 'type', 'type'=>'float'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, provider_id, rate, setss_1, setss_2, setss_3, setss_4, setss_5, updated', 'safe', 'on'=>'search'),
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
			'provider' => array(self::BELONGS_TO, 'Provider', 'provider_id'),
			'rateRules' => array(self::HAS_MANY,'PayRateRules','pay_rate_id')
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
			'rate' => 'Per hr. Rate',
			'setss_1' => 'Setss 1',
			'setss_2' => 'Setss 2',
			'setss_3' => 'Setss 3',
			'setss_4' => 'Setss 4',
			'setss_5' => 'Setss 5',
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
	 * Delete all related records from PayRateRules
	 */
	public function beforeDelete()
	{
		if(parent::beforeDelete())
		{
			PayRateRules::model()->deleteAll('pay_rate_id = :pid',array(':pid'=>$model->id));
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
		$criteria->compare('provider_id',$this->provider_id);
		$criteria->compare('rate',$this->rate,true);
		$criteria->compare('setss_1',$this->setss_1,true);
		$criteria->compare('setss_2',$this->setss_2,true);
		$criteria->compare('setss_3',$this->setss_3,true);
		$criteria->compare('setss_4',$this->setss_4,true);
		$criteria->compare('setss_5',$this->setss_5,true);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['rows_per_page'],
			),
		));
	}
	
	
	/**
	 * Get payroll calculation for specific provider by specific date
	 * @param FormEntities $entities form entities for calculation
	 * @param Provider $provider  - provider object
	 * @param link $total link for variable we will be stored total sum and time
	 */
	static function calculate($entities,$provider,&$total = null)
	{
		$t = 0;
		$data = array();
		$rate = PayRate::model()->with(array('rateRules'=>array('order'=>'sort_order ASC')))->find(array('condition'=>'provider_id = :prid','params'=>array('prid'=>$provider->id),'order'=>'t.updated DESC'));
		//Set default values, if provider doesn't have specified rate
		if(!$rate)
		{
			$rate = new PayRate();
			$rate->rate = Yii::app()->params['rates']['rate'];
			$rate->setss_1 = Yii::app()->params['rates']['setss_1'];
			$rate->setss_2 = Yii::app()->params['rates']['setss_2'];
			$rate->setss_3 = Yii::app()->params['rates']['setss_3'];
			$rate->setss_4 = Yii::app()->params['rates']['setss_4'];
			$rate->setss_5 = Yii::app()->params['rates']['setss_5'];
		}
		$setss_rates = array(
				''=>0,
				'0'=>0,
				'1'=>$rate->setss_1,
				'2'=>$rate->setss_2,
				'3'=>$rate->setss_3,
				'4'=>$rate->setss_4,
				'5'=>$rate->setss_5
		);
		// A LOT LOT OF HARDCODE!!! 15 - id of date of service, 16 - start time, 17 - end time.  Stupid me ;(
		foreach($entities as $e)
		{
			$only_date = isset($e->formsEntitiesFields[15]) ? $e->formsEntitiesFields[15]->value : '';
			$only_date = DateTime::createFromFormat('m/d/Y',$only_date);
			$begin_date = (isset($e->formsEntitiesFields[15]) ? $e->formsEntitiesFields[15]->value : '') .' '. (isset($e->formsEntitiesFields[16]) ? $e->formsEntitiesFields[16]->value : '');
			$end_date = (isset($e->formsEntitiesFields[15]) ? $e->formsEntitiesFields[15]->value : '') .' '. (isset($e->formsEntitiesFields[17]) ? $e->formsEntitiesFields[17]->value : '');
			$begin_date= DateTime::createFromFormat('m/d/Y h:i A',$begin_date);
			$end_date= DateTime::createFromFormat('m/d/Y h:i A',$end_date);
			$m = getTimeDiff($begin_date,$end_date);
			$amount = $rate->rate;
			$g_size = false;
			//Calculating rate
			//IDs: 18 - db's id of service_type field, 34 - db's id of special education values
			//		22 - db's id of group field
			//If SETSS:
			if(isset($e->formsEntitiesFields[18]) && array_key_exists(34, $e->formsEntitiesFields[18]->getValuesAsArray()))
			{
				$g_size = isset($e->formsEntitiesFields['22']) ? $e->formsEntitiesFields['22']->getSingleValue() : 1; 
				$amount = round(($setss_rates[$g_size]/$g_size)/60 * $m,2);	
				//End of SETSS
			}else{
				foreach($rate->rateRules as $rule)
				{
					if(isset($e->formsEntitiesFields[$rule->form_field_id]))
					{
						$vals = $e->formsEntitiesFields[$rule->form_field_id]->getValuesAsArray();
						if(array_key_exists($rule->form_field_value_id,$vals))
						{
							$amount = PayRateRules::applyRule($amount,$rule->additional_rate,$rule->rule);
						}
					}
				}
				$amount = round($amount/60 * $m,2);
			}
			$data[$only_date->getTimestamp()]['services'][$begin_date->getTimestamp()][] = array('begin_date'=>$begin_date,'g_size'=>$g_size, 'end_date'=>$end_date,'patient'=>$e->patient,'interval'=>$m, 'rate'=>$amount); 
			
		}
		ksort($data,SORT_NUMERIC);
		return $data;
	}
}














