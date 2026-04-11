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
 * @property int|null $order
 * @property string|null $phoneNumber
 * @property string|null $conditions
 * @property int|null $master_id
 * @property int|null $isPaid
 * @property string|null $publishedUntil
 * @property string $created_at
 * @property string $updated_at
 */
class Promotion extends \yii\db\ActiveRecord
{


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
            [['validUntil', 'order', 'phoneNumber', 'conditions', 'master_id', 'publishedUntil'], 'default', 'value' => null],
            [['isPaid'], 'default', 'value' => 0],
            [['title', 'description'], 'required'],
            [['validUntil', 'publishedUntil', 'created_at', 'updated_at'], 'safe'],
            [['order', 'master_id', 'isPaid'], 'integer'],
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
            'order' => 'Order',
            'phoneNumber' => 'Phone Number',
            'conditions' => 'Conditions',
            'master_id' => 'Master ID',
            'isPaid' => 'Is Paid',
            'publishedUntil' => 'Published Until',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
