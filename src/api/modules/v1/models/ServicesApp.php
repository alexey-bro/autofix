<?php

namespace api\modules\v1\models;

use common\models\Services;

class ServicesApp extends Services
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

        $fields = [];

        $fields['id'] = 'id';
        $fields['category'] = 'specializationName';
        $fields['name'] = 'name';
        $fields['description'] = 'description';

        $fields['priceFrom'] = function($model) {
            return (int) $model->price_from;
        };

        $fields['priceTo'] = function($model) {
            return (int) $model->price_to;
        };

        $fields['photos'] = 'photos';

        return $fields;
    }

    /**
     * Добавляем в extraFields для API
     */
    public function extraFields()
    {

//        return [
//            'specializations',
//            'workShifts'
//        ];

        return [];
    }


    public function getPhotos()
    {
        return [];
    }

    public function getSpecializationName()
    {
        return $this->specializations->name;
    }


}