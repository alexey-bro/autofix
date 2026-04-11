<?php

namespace api\modules\v1\models;

use common\models\Promotion;
use DateTime;
use DateTimeZone;

class PromotionApp extends Promotion
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
        $fields['objectId'] = 'id';
        $fields['title'] = 'title';
        $fields['description'] = 'description';
        $fields['validUntil'] = 'validUntil';
        $fields['publishedUntil'] = function($model) {
            $date = new DateTime($this->publishedUntil, new DateTimeZone('UTC'));
            return $date->format('Y-m-d\TH:i:s.v\Z');
        };

        $fields['phoneNumber'] = 'phoneNumber';
        $fields['conditions'] = 'conditions';
        $fields['imageUrl'] = 'imageUrl'; // TODO этого нет

        $fields['createdByMasterId'] = 'master_id';
        $fields['master_id'] = 'master_id';
        $fields['order'] = 'order';

//        "order": 1,
//        "createdByMasterId": "user_1763968323791"

        return $fields;
    }

}