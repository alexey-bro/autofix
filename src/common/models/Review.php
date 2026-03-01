<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property int $rating
 * @property string|null $text
 * @property string $created_at
 * @property string $updated_at
 */
class Review extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'default', 'value' => null],
            [['rating'], 'default', 'value' => 0],
            [['uclient_id', 'master_id'], 'required'],
            [['uclient_id', 'master_id', 'rating'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uclient_id' => 'User ID',
            'master_id' => 'Master ID',
            'rating' => 'Rating',
            'text' => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return ReviewQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReviewQuery(get_called_class());
    }

}
