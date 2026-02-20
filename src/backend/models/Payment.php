<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $userId
 * @property int $payment_id
 * @property float|null $amount
 * @property string $confirmationUrl
 * @property int $status
 * @property int $paid
 * @property string $created_at
 * @property string $updated_at
 */
class Payment extends \yii\db\ActiveRecord
{


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
            [['amount'], 'default', 'value' => 0.00],
            [['paid'], 'default', 'value' => 0],
            [['promotion_id', 'userId', 'payment_id', 'confirmationUrl'], 'required'],
            [['promotion_id', 'userId', 'payment_id', 'status', 'paid'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['confirmationUrl'], 'string', 'max' => 255],
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
            'userId' => 'User ID',
            'payment_id' => 'Payment ID',
            'amount' => 'Amount',
            'confirmationUrl' => 'Confirmation Url',
            'status' => 'Status',
            'paid' => 'Paid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
