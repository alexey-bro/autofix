<?php


use yii\rest\UrlRule;

$urlRulesV1 = require(__DIR__ . '/../modules/v1/config/url-manager.php');

return array_merge(

    [
        // Авторизация
        [
            'class' => UrlRule::class,
            'controller' => 'v1/auth',
            'pluralize' => false,
            'extraPatterns' => [
                'POST login'      => 'login',
                'POST signup'     => 'signup',
                'POST logout'     => 'logout',
                'POST logout-all' => 'logout-all',
                'POST register'   => 'register',  // <- добавить
                'GET  me'         => 'me',
            ],
        ],

        // Ваши ресурсы
//        [
//            'class' => 'yii\rest\UrlRule',
//            'controller' => 'user'
//        ],
    ],


    $urlRulesV1,
//    [
//        [
//            'class' => UrlRule::class,
//            'pattern' => 'v<version:[.\d]+>/<controller:[\w-]+>/<action:[\w-]+>',
//            'route' => '/v<version>/<controller>/<action>'
//        ],
//        [
//            'class' => UrlRule::class,
//            'pattern' => '<controller>/<action>',
//            'route' => '<controller>/<action>'
//        ],
        // Для REST контроллеров
//        [
//    //        'controller' => 'api/modules/v1/controllers/functions',
//            'controller' => 'api/v1/functions',
//            'class' => 'yii\rest\UrlRule',
//            'pluralize' => false,
//            'extraPatterns' => [
//                'GET loginByPhone' => 'login-by-phone',
//    //        'POST createNewUser' => 'create-new-user',
//            ],
//
//        ],
//    ]
);