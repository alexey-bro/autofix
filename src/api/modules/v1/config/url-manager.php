<?php

use yii\rest\UrlRule;

return [
    [
        'class' => UrlRule::class,
        'controller' => 'v1/functions',
        'pluralize' => false, //множественное число
        'extraPatterns' => [
            'POST user/photo' => 'set-user-photo',
        ],
    ],

    [
        'class' => UrlRule::class,
        'controller' => 'v1/functions',
        'pluralize' => false, //множественное число
        'extraPatterns' => [
            'POST promotion/<promotion_id:\d[\d,]*>/photo' => 'set-promotion-photo',
        ],
    ],

    [
        'class' => UrlRule::class,
        'controller' => 'v1/functions',
        'pluralize' => false, //множественное число
        'extraPatterns' => [
            'POST service/<service_id:\d[\d,]*>/photo' => 'set-service-photo',
        ],
    ],
];