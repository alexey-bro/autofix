<?php

namespace common\models;

use common\models\query\BookingQuery;
use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property int $work_time_shift_id
 * @property string $serviceName
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Booking extends \yii\db\ActiveRecord
{

    public const STATUS_CONFIRM = 1; //confirmed
    public const STATUS_REJECT = 2; //rejected

    public static function listStatus()
    {
        return [
            self::STATUS_CONFIRM => "confirmed",
            self::STATUS_REJECT => "rejected",
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
            [['client_id', 'master_id', 'work_time_shift_id', 'serviceName'], 'required'],
            [['client_id', 'master_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['serviceName'], 'string', 'max' => 255],
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
            'serviceName' => 'Service Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return BookingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BookingQuery(get_called_class());
    }

}
