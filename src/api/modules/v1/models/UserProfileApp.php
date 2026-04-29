<?php

namespace api\modules\v1\models;

use common\models\Services;
use common\models\UserProfile;
use common\models\WorkDaysShift;

class UserProfileApp extends UserProfile
{

//    self::SCENARIO_DEFAULT
    const SCENARIO_BOOKING_CLIENT = 'booking_client';
    const SCENARIO_BOOKING_MASTER = 'booking_master';

    public function init()
    {
        parent::init();
    }
//
//
//    /**
//     * Добавляем в fields для API
//     */
//    public function fields()
//    {
//
////        var_dump($this->scenario);
////        die;
//
//        switch ($this->scenario) {
//            case self::SCENARIO_BOOKING_MASTER:
//                return [
//                    'userId' => 'id',
//                    'firstName',
//                    'photoUrl',
//                    'specialization' => 'specializationsAsString',
//                    'rating',
//                    'reviewsCount',
//                    'city',
//                    'phone',
//                    'workAddress',
//                    'latitude' => fn() => (float) $this->latitude,
//                    'longitude' => fn() => (float) $this->longitude,
//                ];
//
//            case self::SCENARIO_BOOKING_CLIENT:
//                return [
//                    'userId' => 'id',
//                    'firstName',
//                    'photoUrl',
//                    'phone',
//                    'city',
//                    'carBrand',
//                ];
//
//            default:
//                $fields = parent::fields();
//                $fields['specializations'] = 'specializationsAsString';
//                $fields['workShifts'] = 'workDaysShift';
//                $fields['customShiftTemplates'] = 'customShiftTemplatesApp';
//                $fields['services'] = 'servicesApp';
//                $fields['photoUrl'] = function () {
//                    if ($this->photo) {
//                        return $this->photo->getUrl();
//                    }
//                    return '';
//                };
//        }
//
//
//
//        return $fields;
//    }

    /**
     * Добавляем в extraFields для API
     */
    public function extraFields()
    {
        return [
            'specializations',
            'workShifts'
        ];
    }

}