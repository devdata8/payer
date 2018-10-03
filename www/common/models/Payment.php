<?php

namespace common\models;

use Yii;
use app\components\StatusValidator;

/**
 * This is the model class for table "payment".
 *
 * @property int    $id
 * @property int    $id_user_from
 * @property int    $id_user_to
 * @property int    $status
 * @property double $amount
 * @property string $deferred_time
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User   $userFrom
 * @property User   $userTo
 */
class Payment extends \yii\db\ActiveRecord
{
    const STATUS_DEFERRED = 1;
    const STATUS_PROCESS = 5;
    const STATUS_FINISHED = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user_from', 'id_user_to', 'amount', 'deferred_time'], 'required'],
            [['id_user_from', 'id_user_to'], 'default', 'value' => null],
            [['id_user_from', 'id_user_to', 'status'], 'integer'],
            [['amount'], 'number'],
//            ['amount', 'compare', 'compareValue' => 30, 'operator' => '<=', 'type' => 'number'],
//            ['amount', 'compare', 'compareValue' => function ($model) {return $model->id;}, 'operator' => '<=', 'type' => 'number'],
//            [['amount'],'number','max'=>100, 'when' => function($model) {
//                return $model->amount == 200;
//            }, 'whenClient' => "function (attribute, value) {
//            return $('#amount').val() == '300';
//        }"],

//            ['amount', function ($attribute, $params) {
//                if ($this->$attribute<=$this->id_user_from) {
//                    $this->addError($attribute,
//                        'Amount of your pay more then Sum of your current balance and deferred balance.');
//                } else {
//                        $this->addError($attribute,
//                            'Amount of your pay more then Sum of your current balance and deferred balance.'
//                            .print_r($this->$attribute,true).' - '.print_r($params,true)
//                            .' - '.print_r($this->id_user_from,true)
//                            .' - '.print_r(($this->$attribute<=$this->id_user_from),true)
//                        );
//                }
//            }],
            ['amount', 'validateAmount'],
//            ['amount', 'validateChildrenFunds', 'when' => function ($model) {
//                return $model->amount > 0;
//            }],
//            ['amount', 'validateChildrenFunds'],
//            ['amount', 'validatePassword'],
            [['deferred_time', 'created_at', 'updated_at'], 'safe'],
            [
                ['id_user_from'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::className(),
                'targetAttribute' => ['id_user_from' => 'id'],
            ],
            [
                ['id_user_to'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::className(),
                'targetAttribute' => ['id_user_to' => 'id'],
            ],
        ];
    }

    /**
     * Validate amount before add payment
     */
    public function validateAmount($attribute, $params)
    {
        $user = $this->userFrom;

        $allowAmount = $user->balance - $user->deferred_balance;
        if ($this->$attribute>$allowAmount) {
            $this->addError($attribute,
                'Amount your pay more then allowed balance. Max allow amount: '.$allowAmount);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'id_user_from'  => 'Payer',
            'id_user_to'    => 'Receiver',
            'status'        => 'Status',
            'amount'        => 'Amount',
            'deferred_time' => 'Deferred time',
            'created_at'    => 'Time create',
            'updated_at'    => 'Time update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTo()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user_to']);
    }
}
