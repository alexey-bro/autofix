<?php

namespace api\modules\v1\models;

use common\models\Booking;
use common\models\UserProfile;
use DateTime;
use DateTimeZone;

class BookingApp extends Booking
{

    const SCENARIO_BOOKING_CLIENT = 'booking_client';
    const SCENARIO_BOOKING_MASTER = 'booking_master';


    public function init()
    {
        parent::init();
    }

    /**
     * Добавляем в fields для API
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields = [];


        $fields['bookingId'] = 'id';
        $fields['status'] = function($model) {

            $listStatus = Booking::listStatus();
            if (isset($listStatus[$model->status])) {
                return $listStatus[$model->status];
            }

            return Booking::listStatus()[Booking::STATUS_REJECT];

        };

        $fields['serviceName'] = function($model) {
            return $this->service->name;
        };

        $fields['createdAt'] = function($model) {
            $date = new DateTime($this->created_at, new DateTimeZone('UTC'));
            return $date->format('Y-m-d\TH:i:s.v\Z');
        };

        $fields['slotStart'] = function($model) {

            $day = $this->workTimeShift->workDaysShift->day;
            $time = $this->workTimeShift->start;
            $datetime = $day . ' ' . $time;

            $date = new DateTime($datetime, new DateTimeZone('UTC'));
            return $date->format('Y-m-d\TH:i:s.v\Z');
        };

        $fields['slotEnd'] = function($model) {

            $day = $this->workTimeShift->workDaysShift->day;
            $time = $this->workTimeShift->stop;
            $datetime = $day . ' ' . $time;

            $date = new DateTime($datetime, new DateTimeZone('UTC'));
            return $date->format('Y-m-d\TH:i:s.v\Z');
        };


        //TODO: это ерунда, надо переделать на extended поля (master и client)
        $fields['master'] = function($model) {
            $Master = $this->master;

            if ($Master) {
                $Master->scenario = UserProfileApp::SCENARIO_BOOKING_MASTER;
                return $Master;
            }

            return null;
        };

        $fields['client'] = function($model) {
            $Client = $this->client;

            if ($Client) {
                $Client->scenario = UserProfileApp::SCENARIO_BOOKING_CLIENT;
                return $Client;
            }

            return null;
        };

        if ($this->scenario === self::SCENARIO_BOOKING_MASTER) {
            unset($fields['master']);
        }

        return $fields;
    }

    public function getClient()
    {
        return $this->hasOne(UserProfileApp::class, ['id' => 'client_id']);
    }

    public function getMaster()
    {
        return $this->hasOne(UserProfileApp::class, ['id' => 'master_id']);
    }

    /**
     * Добавляем в extraFields для API
     */
    public function extraFields()
    {
        return [];
    }

}