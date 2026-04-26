<?php

namespace common\models;

use common\models\query\PromotionQuery;
use Yii;

/**
 * This is the model class for table "promotion".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $validUntil дата окончания самой акции (для клиента)
 * @property string|null $phoneNumber
 * @property string|null $conditions
 * @property int|null $master_id
 * @property int|null $payment_id
 * @property int|null $isPaid
 * @property float|null $amount
 * @property int|null $durationDays
 * @property string|null $publishedUntil
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Payment $payment
 * @property File $photo
 */
class Promotion extends \yii\db\ActiveRecord
{

    public const int DEFAULT_DURATION_DAYS = 7;
    public const int DEFAULT_PRICE = 990;

    public const int ID_PAID_NO = 0;
    public const int ID_PAID_YES = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['validUntil', 'phoneNumber', 'conditions', 'master_id', 'publishedUntil', 'payment_id'], 'default', 'value' => null],
            [['isPaid'], 'default', 'value' => 0],
            [['amount'], 'default', 'value' => 0.00],
            [['durationDays'], 'default', 'value' => self::DEFAULT_DURATION_DAYS],
            [['title', 'description'], 'required'],
            [['validUntil', 'publishedUntil', 'created_at', 'updated_at'], 'safe'],
            [['master_id', 'isPaid', 'durationDays'], 'integer'],
            [['title', 'description', 'phoneNumber', 'conditions'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'validUntil' => 'дата окончания самой акции (для клиента)',
            'phoneNumber' => 'Phone Number',
            'conditions' => 'Conditions',
            'master_id' => 'Master ID',
            'isPaid' => 'Is Paid',
            'amount' => 'Amount',
            'payment_id' => 'Payment ID',
            'durationDays' => 'Duration Days',
            'publishedUntil' => 'Published Until',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public function getPayment()
    {
        return $this->hasOne(Payment::class, ['id' => 'payment_id']);
    }

    public function getPhoto() : \yii\db\ActiveQuery
    {
        return $this->hasMany(File::class, ['entity_id' => 'id'])
            ->onCondition([
                'files.type' => File::TYPE_PROMOTION,
                'files.sub_type' => File::SUB_TYPE_PROMOTION,
            ]);
    }


    /**
     * {@inheritdoc}
     * @return PromotionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PromotionQuery(get_called_class());
    }

}
