<?php

/**
 * This is the model class for table "log".
 *
 * The followings are the available columns in table 'log':
 * @property integer $id
 * @property integer $from
 * @property integer $to
 * @property integer $count
 * @property string $time
 */
class Log extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, from, to, count', 'numerical', 'integerOnly'=>true),
			array('time', 'safe'),
                        array('count', 'isEnoughAtBalance'),
                        array('to', 'validateUserTo'),
                        array('from', 'validateUserFrom'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, from, to, count, time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'from' => 'From',
			'to' => 'To',
			'count' => 'Count',
			'time' => 'Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('from',$this->from);
		$criteria->compare('to',$this->to);
		$criteria->compare('count',$this->count);
		$criteria->compare('time',$this->time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Log the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function isEnoughAtBalance($attribute){   
            if ($this->count > 0 && (floatval($this->count) == $this->count)){
                $user = User::model()->findByPk(Yii::app()->user->id);
                if ($user && $user->balance < $this->count){
                    $this->addError($attribute, 'Недостаточно средств на счету');
                }
            }else{
                $this->addError($attribute, 'Некорректное значение');
            }
        }
        
        public function validateUserTo($attribute,$params){
            if ($this->to > 0){
                $user = User::model()->findByPk($this->to);
                if (!$user){
                    $this->addError($attribute, 'Такого пользователя не существует');
                }
                if ($this->to == $this->from){
                    $this->addError($attribute, 'Нельзя проводить перевод самому себе');
                }
            }else{
                $this->addError($attribute, 'Некорректное значение');
            }            
        }
        
        public function validateUserFrom($attribute){
            if ($this->from > 0){
                $user = User::model()->findByPk($this->from);
                if (!$user){
                    $this->addError($attribute, 'Такого пользователя не существует');
                }                
            }else{
                $this->addError($attribute, 'Некорректное значение');
            }  
        }
        
        public function transferMoney() {                                
        $transaction = Yii::app()->db->beginTransaction();        
        
        $userFrom = User::model()->findByPk($this->from);
        $userTo = User::model()->findByPk($this->to);                
        
        $userFrom->balance = $userFrom->balance - $this->count;        
        $userTo->balance = $userTo->balance + $this->count;                
        
        if ($userFrom->save() && $userTo->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollback();
            return false;
        }
    }
    
    protected function beforeSave() {
        if (!parent::beforeSave()) {
            return false;
        }
        $this->time = date("Y-m-d H:i:s");        
        return true;
    }

}
