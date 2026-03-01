<?php


return [


    // Для REST контроллеров
    [
//        'controller' => 'api/modules/v1/controllers/functions',
        'controller' => 'api/v1/functions',
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'extraPatterns' => [
            'GET loginByPhone' => 'login-by-phone',
//        'POST createNewUser' => 'create-new-user',
        ],

    ],

];