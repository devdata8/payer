<?php

namespace common\models;

use app\components\StatusValidator;

/**
 * This is the model class for table "payment".
 *
 * @property int    $id
 * @property int    $id_user_from
 * @property int    $id_user_to
 * @property int    $status
 * @property double $amount
 * @property int    $deferred_time
 * @property int    $created_at
 * @property int    $updated_at
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
            ['amount', 'validateAmount'],
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
     * Validate amount before add payment or try processing
     */
    public function validateAmount($attribute, $params)
    {
        $user = $this->userFrom;
        if( $this->isNewRecord) {
            $allowAmount = $user->balance - $user->deferred_balance;
        } else {
            $allowAmount = $user->balance;
        }
        if ($this->$attribute > $allowAmount) {
            if( $this->isNewRecord) {
                $this->addError($attribute,
                    'Amount your pay more then allowed balance for pays. Max allow amount: '.$allowAmount);
            } else {
                $this->addError($attribute,
                    'Amount current pay more then allowed balance. Balance: '.$allowAmount);
            }
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
