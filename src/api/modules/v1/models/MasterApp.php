<?php

namespace api\modules\v1\models;

class MasterApp extends UserApp
{
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

        $allowFields = [
            'userId',
            'firstName',
            'photoUrl',
            'specialization',
            'rating',
            'reviewsCount',
            'city',
            'phone',
            'workAddress',
            'latitude',
            'longitude',
            'carBrand',
        ];

        return array_intersect_key($fields, array_flip($allowFields));
    }



}