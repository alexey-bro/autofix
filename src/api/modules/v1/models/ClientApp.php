<?php

namespace api\modules\v1\models;

use common\models\User;

class ClientApp extends UserApp
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
            'phone',
            'city',
            'carBrand',
        ];

        return array_intersect_key($fields, array_flip($allowFields));
    }
}