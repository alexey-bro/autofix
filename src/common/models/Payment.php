<?php

namespace common\models;

use common\models\query\PaymentQuery;
use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $user_profile_id
 * @property int $payment_id
 * @property string $confirmationUrl
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Payment extends \yii\db\ActiveRecord
{

    /** Ожидает оплаты покупателем pending*/
    public const int PENDING = 1;

    /** Ожидает подтверждения магазином waiting_for_capture*/
    public const int WAITING_FOR_CAPTURE = 2;

    /** Успешно оплачен и подтвержден магазином succeeded*/
    public const  int SUCCEEDED = 3;

    /** Неуспех оплаты или отменен магазином canceled*/
    public const int CANCELED = 4;

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
            [['promotion_id', 'user_profile_id', 'payment_id', 'confirmationUrl'], 'required'],
            [['promotion_id', 'user_profile_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['confirmationUrl', 'payment_id'], 'string', 'max' => 255],
        ];
    }

    public static function listStatus()
    {
        return [
            self::PENDING => 'pending',
            self::WAITING_FOR_CAPTURE => 'waiting_for_capture',
            self::SUCCEEDED => 'succeeded',
            self::CANCELED => 'canceled',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Promotion ID',
            'user_profile_id' => 'User Profile ID',
            'payment_id' => 'Payment ID',
            'confirmationUrl' => 'Confirmation Url',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getStatusViaValue(string $value): int
    {
        $key = array_search($value, self::listStatus());
        if ($key) {
            return $key;
        }

        return self::PENDING;
    }

    /**
     * {@inheritdoc}
     * @return PaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentQuery(get_called_class());
    }

}
