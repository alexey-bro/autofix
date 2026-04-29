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
 *
 * @property WorkTimeShift $workTimeShift
 * @property WorkTimeShift $workTimeShifts
 * @property UserProfile $userProfile
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
     * Связь с user
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'user_id']);
    }

    /**
     * Связь с workTimeShift (один ко многим)
     */
    public function getWorkTimeShift()
    {
        return $this->hasMany(WorkTimeShift::class, ['work_days_shift_id' => 'id']);
    }

    /**
     * Связь с временными слотами
     */
    public function getWorkTimeShifts()
    {
        return $this->hasMany(WorkTimeShift::class, ['work_days_shift_id' => 'id']);
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
