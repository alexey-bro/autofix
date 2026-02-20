<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int $client_id
 * @property int $master_id
 * @property string $slotStart
 * @property string $slotEnd
 * @property string $serviceName
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Booking extends \yii\db\ActiveRecord
{


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
            [['client_id', 'master_id', 'slotStart', 'slotEnd', 'serviceName'], 'required'],
            [['client_id', 'master_id', 'status'], 'integer'],
            [['slotStart', 'slotEnd', 'created_at', 'updated_at'], 'safe'],
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
            'slotStart' => 'Slot Start',
            'slotEnd' => 'Slot End',
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
