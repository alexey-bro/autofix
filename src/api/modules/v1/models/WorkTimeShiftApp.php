<?php

namespace api\modules\v1\models;

use common\models\WorkTimeShift;

class WorkTimeShiftApp extends WorkTimeShift
{

    public function init()
    {
        parent::init();
    }

    public function fields()
    {
        $fields = parent::fields();

        // Добавляем timeShifts в ответ
        $fields = [];
//        $fields['id'] = 'id';
//        $fields['date'] = 'day';
//        $fields['slots'] = 'workTimeShift';

        $fields['id'] = 'id';
        $fields['startTime'] = 'startTime';
        $fields['endTime'] = 'endTime';
        $fields['isAvailable'] = 'isAvailableSlot';

//        return array_intersect_key($fields, $allowFields);

        return $fields;
    }

    public function getIsAvailableSlot()
    {
        return (bool) $this->isAvailable;
    }

    public function getStartTime()
    {

        $workDaysShift = $this->workDaysShift;
        $r = $workDaysShift->day . ' ' . $this->start;
        $t = new \DateTime($r);

        return $t->format('Y-m-d\TH:i');
    }

    public function getEndTime()
    {

        $workDaysShift = $this->workDaysShift;
        $r = $workDaysShift->day . ' ' . $this->stop;
        $t = new \DateTime($r);

        return $t->format('Y-m-d\TH:i');
    }




    public function extraFields()
    {
        return [];
    }





}