<?php

namespace common\models;

/**
 * This is the model class for table "work_days_shift".
 *
 * @property int $id
 * @property int $user_id
 * @property string $day
 * @property string $created_at
 * @property string $updated_at
 */
class WorkDaysShift extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_days_shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'day'], 'required'],
            [['user_id'], 'integer'],
            [['day', 'created_at', 'updated_at'], 'safe'],
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
            'day' => 'Day',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\WorkDaysShiftQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new query\WorkDaysShiftQuery(get_called_class());
    }

}
