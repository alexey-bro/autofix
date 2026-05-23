<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => '',
            'csrfCookie' => [
                'domain' => '.test.loc', // <-- Для CSRF токена тоже не помешает
                'path' => '/',
                'httpOnly' => true,
            ],

        ],
        'user' => [
            'identityClass' => 'common\models\User',
//            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'identityCookie' => [
                'name' => '_identity', // Имя cookie должно совпадать в обоих приложениях
                'path' => '/',
                'domain' => '.test.loc', // <-- Тоже указываем домен
                'httpOnly' => true,
            ],
            'enableAutoLogin' => true,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
//            'name' => 'advanced-backend',
            'name' => 'advanced_dev',
            'cookieParams' => [
                'domain' => '.test.loc', // <-- Точка в начале ОЧЕНЬ важна!
                'path' => '/',
                'httpOnly' => true,
            ],

        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
