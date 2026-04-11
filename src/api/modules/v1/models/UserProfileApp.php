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

//    public function scenarios(): array
//    {
//        $scenarios = parent::scenarios();
////
//        $attr = ['id'];
////        $scenarios[self::SCENARIO_BOOKING_CLIENT] = $attr;
//        $scenarios[self::SCENARIO_BOOKING_MASTER] = $attr;
//
//        return $scenarios;
//    }

    private $_specializationsString = [];

    public function init()
    {
        parent::init();
    }

    /**
     * Виртуальное свойство для получения категорий строкой
     */
    public function getSpecializationsAsString()
    {
        if (!$this->_specializationsString) {
            $names = $this->getSpecializations()
                ->select('name')
                ->column();
            $this->_specializationsString = implode(', ', $names);
        }
        return $this->_specializationsString;
    }

    /**
     * Для установки значения (если нужно)
     */
    public function setSpecializationsAsString($value)
    {
        $this->_specializationsString = $value;
    }

//    public function getWorkDaysShift()
//    {
//        if (!$this->_specializationsString) {
//            $names = $this->getSpecializations()
//                ->select('name')
//                ->column();
//            $this->_specializationsString = implode(', ', $names);
//        }
//        return $this->_specializationsString;
//    }

    public function getWorkDaysShift()
    {
        return $this->hasMany(WorkDaysShiftApp::class, ['user_profile_id' => 'id']);
    }

    public function getCustomShiftTemplatesApp()
    {

        $customShiftTemplates = $this->customShiftTemplates;

        $templates = [];
        foreach ($customShiftTemplates as $customShiftTemplate) {
            $createdAt = new \DateTime($customShiftTemplate->created_at);
            $templates[] = $customShiftTemplate->template . '-' . $createdAt->format('U');
        }

        return $templates;
    }

    public function getServicesApp()
    {
        return $this->hasMany(ServicesApp::class, ['user_profile_id' => 'id']);
    }

    /**
     * Добавляем в fields для API
     */
    public function fields()
    {

//        var_dump($this->scenario);
//        die;

        switch ($this->scenario) {
            case self::SCENARIO_BOOKING_MASTER:
                return [
                    'userId' => 'id',
                    'firstName',
                    'photoUrl',
                    'specialization' => 'specializationsAsString',
                    'rating',
                    'reviewsCount',
                    'city',
                    'phone',
                    'workAddress',
                    'latitude' => fn() => (float) $this->latitude,
                    'longitude' => fn() => (float) $this->longitude,
                ];

            case self::SCENARIO_BOOKING_CLIENT:
                return [
                    'userId' => 'id',
                    'firstName',
                    'photoUrl',
                    'phone',
                    'city',
                    'carBrand',
                ];

            default:
                $fields = parent::fields();
                $fields['specializations'] = 'specializationsAsString';
                $fields['workShifts'] = 'workDaysShift';
                $fields['customShiftTemplates'] = 'customShiftTemplatesApp';
                $fields['services'] = 'servicesApp';
        }



        return $fields;
    }

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