<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => '',
            'csrfCookie' => [
                'domain' => '.test.loc', // <-- Для CSRF токена тоже не помешает
                'path' => '/',
                'httpOnly' => true,
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
//            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'identityCookie' => [
                'name' => '_identity', // Имя cookie должно совпадать в обоих приложениях
                'path' => '/',
                'domain' => '.test.loc', // <-- Тоже указываем домен
                'httpOnly' => true,
            ],

        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
//            'name' => 'advanced-frontend',
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
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
