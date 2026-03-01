<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property int $user_id
 * @property int $specialization_id
 * @property string $name
 * @property string|null $description
 * @property float|null $price_from
 * @property float|null $price_to
 * @property string $created_at
 * @property string $updated_at
 */
class Services extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => null],
            [['specialization_id'], 'default', 'value' => 0],
            [['price_to'], 'default', 'value' => 0.00],
            [['user_id', 'name'], 'required'],
            [['user_id', 'specialization_id'], 'integer'],
            [['price_from', 'price_to'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'specialization_id' => 'Specialization ID',
            'name' => 'Name',
            'description' => 'Description',
            'price_from' => 'Price From',
            'price_to' => 'Price To',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return ServicesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServicesQuery(get_called_class());
    }

}
