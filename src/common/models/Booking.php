<?php

namespace common\models;

use api\modules\v1\models\ServicesApp;
use common\models\query\BookingQuery;
use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property int $work_time_shift_id
 * @property int $service_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Booking $client
 * @property Booking $master
 * @property Services $service
 * @property WorkTimeShift $workTimeShift
 * @property WorkDaysShift $workDaysShift
 */
class Booking extends \yii\db\ActiveRecord
{

    public const STATUS_CONFIRM = 1; //confirmed
    public const STATUS_REJECT = 2; //rejected
    public const STATUS_PROCESSING = 3; //processing

    public static function listStatus()
    {
        return [
            self::STATUS_CONFIRM => "confirmed",
            self::STATUS_REJECT => "rejected",
            self::STATUS_PROCESSING => "processing",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 0],
            [['client_id', 'master_id', 'work_time_shift_id', 'service_id'], 'required'],
            [['client_id', 'master_id', 'status', 'service_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
//            [['serviceName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'master_id' => 'Master ID',
            'work_time_shift_id' => 'Work time shift id',
            'service_id' => 'Service ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserProfile::class, ['id' => 'client_id']);
    }

    public function getMaster(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserProfile::class, ['id' => 'master_id']);
    }

    public function getService(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Services::class, ['id' => 'service_id']);
    }

    public function getWorkTimeShift(): \yii\db\ActiveQuery
    {
        return $this->hasOne(WorkTimeShift::class, ['id' => 'work_time_shift_id']);
    }

    // Транзитная связь к work_days_shift (через work_time_shift)
    public function getWorkDaysShift(): \yii\db\ActiveQuery
    {
        return $this->hasOne(WorkDaysShift::class, ['id' => 'work_days_shift_id'])
            ->via('workTimeShift');
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if ($WorkTimeShift = $this->workTimeShift) {

            $WorkTimeShift->isAvailable = 1;
            if (!$WorkTimeShift->save()) {

                return false;
            }
        }

        return true; // Продолжаем физическое удаление
    }

//    // Транзитная связь к пользователю (мастеру) через work_days_shift
//    public function getMaster()
//    {
//        return $this->hasOne(UserProfile::class, ['id' => 'user_profile_id'])
//            ->via('workDaysShift');
//    }



    /**
     * {@inheritdoc}
     * @return BookingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BookingQuery(get_called_class());
    }

}
