<?php

namespace api\modules\v1\models;

use common\models\Review;
use DateTime;
use IntlDateFormatter;

class ReviewApp extends Review {


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
        $fields['authorName'] = 'author_name';
        $fields['authorPhotoUrl'] = 'authorPhotoUrl';
        $fields['rating'] = 'rating';
        $fields['text'] = 'text';
        $fields['title'] = 'title';

//        $fields['date'] = function($model) {
//
//            $date = new DateTime($this->created_at);
//
//            $formatter = new IntlDateFormatter(
//                'ru_RU',
//                IntlDateFormatter::LONG,
//                IntlDateFormatter::NONE,
//                null,
//                null,
//                'd MMMM yyyy г.'
//            );
//
//            return $formatter->format($date);
//        };

        $fields['date'] = function($model) {

            $date = new DateTime($this->created_at);

            $months = [
                1 => 'января',
                2 => 'февраля',
                3 => 'марта',
                4 => 'апреля',
                5 => 'мая',
                6 => 'июня',
                7 => 'июля',
                8 => 'августа',
                9 => 'сентября',
                10 => 'октября',
                11 => 'ноября',
                12 => 'декабря'
            ];

            $day = $date->format('j');
            $month = $months[(int)$date->format('n')];
            $year = $date->format('Y');

            return "$day $month $year г."; // "24 ноября 2025 г."
        };

        return $fields;
    }

}