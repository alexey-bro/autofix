<?php

namespace api\modules\v1\models;

use common\models\User;
use common\models\UserProfile;
use DateTime;

class UserApp extends User
{

    const SCENARIO_BOOKING_CLIENT = 'booking_client';
    const SCENARIO_BOOKING_MASTER = 'booking_master';

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

    public function getWorkDaysShift()
    {
        return $this->hasMany(WorkDaysShiftApp::class, ['user_id' => 'id']);
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
        return $this->hasMany(ServicesApp::class, ['user_id' => 'id']);
    }

    /**
     * Добавляем в fields для API
     */
    public function fields()
    {

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
                $fields = parent::fields();

                $fields = [
                    'id' => 'id',
                    'userId' => 'id',
                    'email' => 'email',
                    'phone' => 'phone',
                    'role' => function($model) {
                        return User::listRoles()[$model->role];
                    },
                    'role_int' => 'role',
                    'specialization' => 'specializationsAsString',
                    'photos' => function () {
                        $photos = [];
                        return $photos;
                    },
                    'photoUrl' => function () {
                        if ($this->photo) {
                            return $this->photo->getUrl();
                        }
                        return '';
                    },
                    'createdAt' => function () {
                        $DT = DateTime::createFromFormat('U', $this->created_at);
//                        return $DT->format('Y-m-d H:i:s');
                        return $DT->format('Y-m-d\TH:i:s.v\Z');
                    },
                    'updatedAt' => function () {
                        $DT = DateTime::createFromFormat('U', $this->updated_at);
                        return $DT->format('Y-m-d\TH:i:s.v\Z');
                    },
                    'services' => 'servicesApp',
                    'workShifts' => 'workDaysShift',
                    'customShiftTemplates' => 'customShiftTemplatesApp',
                ];

                $fields['city'] = fn() => $this->userProfileApp?->city;
                $fields['companyName'] = fn() => $this->userProfileApp?->companyName;
                $fields['experience'] = fn() => $this->userProfileApp?->experience;
                $fields['firstName'] = fn() => $this->userProfileApp?->firstName;
                $fields['lastName'] = fn() => $this->userProfileApp?->lastName;
                $fields['latitude'] = fn() => $this->userProfileApp?->latitude;
                $fields['longitude'] = fn() => $this->userProfileApp?->longitude;
                $fields['workAddress'] = fn() => $this->userProfileApp?->workAddress;
                $fields['rating'] = fn() => $this->userProfileApp?->rating;
                $fields['reviewsCount'] = fn() => $this->userProfileApp?->reviewsCount;
                $fields['carBrand'] = fn() => $this->userProfileApp?->carBrand;
//        }



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


//    /**
//     * Добавляем в fields для API
//     */
//    public function fields()
//    {
//
//        $fields = [
//            'id',
//            'email',
//            'firstName'  => fn() => $this->profile?->firstName,
//            'specializations' => $this->profile->getSpecializationsAsString(),
//        ];
//
//
//
////        $fields = parent::fields();
////        $fields['specializations'] = 'specializationsAsString';
////        $fields['workShifts'] = 'workDaysShift';
////        $fields['customShiftTemplates'] = 'customShiftTemplatesApp';
////        $fields['services'] = 'servicesApp';
////        $fields['photoUrl'] = function () {
////            if ($this->photo) {
////                return $this->photo->getUrl();
////            }
////            return '';
////        };
//
//
//        $baseFields = [
//            'id',
//            'email',
//        ];
//
////        if ($this->profile) {
////            $profileFields = $this->profile->fields();
////            // убираем userId чтобы не дублировать лишнее
////            unset($profileFields['userId']);
////        }
//
////        var_dump($profileFields);
////        die;
//
//
//        // Берём ключи полей из UserProfile и оборачиваем в замыкания
////        $profileFields = [];
////        foreach ((new UserProfileApp())->fields() as $key => $value) {
////            $field = is_int($key) ? $value : $key; // нормализуем ключ
////            if ($field === 'userId') continue;      // пропускаем userId
////
////            $profileFields[$field] = fn() => $this->profile?->$field ?? null;
////        }
//
////        var_dump($profileFields);
////        die;
//
//
////        return array_merge($baseFields, $profileFields);
//
//        return $fields;
//    }

    public function getUserProfileApp()
    {
        return $this->hasOne(UserProfileApp::class, ['user_id' => 'id']);
    }

}