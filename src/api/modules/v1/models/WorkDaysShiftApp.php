<?php

namespace api\modules\v1\models;

use common\models\WorkDaysShift;
use common\models\WorkTimeShift;

class WorkDaysShiftApp extends WorkDaysShift
{

    public function init()
    {
        parent::init();
    }


//    /**
//     * Добавляем в fields для API
//     */
//    public function fields()
//    {
//        $fields = parent::fields();
//        $fields['id'] = 'id';
//
//        return $fields;
//    }


    /**
     * Поля для REST API
     */
    public function fields()
    {
        $fields = parent::fields();

        // Добавляем timeShifts в ответ
        $fields = [];
        $fields['id'] = 'id';
        $fields['date'] = 'day';
        $fields['slots'] = 'workTimeShift';

//        return array_intersect_key($fields, $allowFields);

        return $fields;
    }

    public function extraFields()
    {
        return ['workTimeShift', 'userProfile'];
    }

    /**
     * Связь с workTimeShift (один ко многим)
     */
    public function getWorkTimeShift()
    {
        return $this->hasMany(WorkTimeShiftApp::class, ['work_days_shift_id' => 'id']);
    }

}