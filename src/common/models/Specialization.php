<?php

namespace common\models;

use common\models\query\SpecializationQuery;
use Yii;

/**
 * This is the model class for table "specialization".
 *
 * @property int $id
 * @property int $user_profile_id
 * @property int $specialization_id
 */
class Specialization extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specialization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['specialization_id'], 'default', 'value' => 0],
            [['user_profile_id'], 'required'],
            [['user_profile_id', 'specialization_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_profile_id' => 'User ID',
            'specialization_id' => 'Specialization ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialization()
    {
        return $this->hasOne(Specializations::class, ['id' => 'specialization_id']);
    }

    /**
     * {@inheritdoc}
     * @return SpecializationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SpecializationQuery(get_called_class());
    }

}
