<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "work_time_shift".
 *
 * @property int $id
 * @property int $work_days_shift_id
 * @property string|null $start
 * @property string|null $stop
 * @property int $isAvailable
 * @property string $created_at
 * @property string $updated_at
 *
 * @property WorkDaysShift $workDaysShift
 * @property Booking $bookings
 * @property UserProfile $userProfile
 */
class WorkTimeShift extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_time_shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start', 'stop'], 'default', 'value' => null],
            [['isAvailable'], 'default', 'value' => 0],
            [['work_days_shift_id'], 'required'],
            [['work_days_shift_id', 'isAvailable'], 'integer'],
            [['start', 'stop', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_days_shift_id' => 'Work Days Shift ID',
            'start' => 'Start',
            'stop' => 'Stop',
            'isAvailable' => 'Is Available',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Связь с dayShift
     */
    public function getWorkDaysShift(): \yii\db\ActiveQuery
    {
        return $this->hasOne(WorkDaysShift::class, ['id' => 'work_days_shift_id']);
    }

    // Связь с бронированиями
    public function getBookings(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Booking::class, ['work_time_shift_id' => 'id']);
    }

    /**
     * Транзитная связь с пользователем
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id'])
            ->via('workDaysShift');
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\WorkTimeShiftQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\WorkTimeShiftQuery(get_called_class());
    }

}
