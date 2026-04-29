<?php

namespace common\models;

use api\modules\v1\models\UserProfileApp;
use common\models\query\ReviewQuery;
use Yii;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property int $rating
 * @property string|null $author_name
 * @property string|null $text
 * @property string $created_at
 * @property string $updated_at
 *
 * @property UserProfile $client
 * @property UserProfile $master
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
            [['client_id', 'master_id', 'rating'], 'required'],
            [['client_id', 'master_id', 'rating'], 'integer'],
            [['text', 'author_name'], 'string'],
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
            'client_id' => 'ClientApp ID',
            'master_id' => 'MasterApp ID',
            'rating' => 'Rating',
            'text' => 'Text',
            'author_name' => 'author name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getClient()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'client_id']);
    }

    public function getMaster()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'master_id']);
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
