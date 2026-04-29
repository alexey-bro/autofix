<?php

use api\modules\v1\Module;
use yii\web\JsonParser;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$urlRules = require(__DIR__ . '/url-manager.php');
//$modulesV1 = require(__DIR__ . '/../modules/v1/config/modules.php');
//$modulesV1 = require(__DIR__ . '/../modules/v1/config/modules.php');

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
//    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCsrfValidation' => false,
//            'cookieValidationKey' => 'sdfsdfsdgfsdgKBkbkKJlhbnjHKBhkjlb687t5^FG^&&',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON, // По умолчанию отвечаем JSON
            'charset' => 'UTF-8',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
//            'identityClass' => 'api\modules\v1\models\UserApp',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
            'enableSession' => false, // API не использует сессии
//            'loginUrl' => null, // Не перенаправляем на страницу входа
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-api',
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
        // stock
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//            'rules' => [
//            ],
//        ],

    // deep seek
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true, // Строгий разбор URL
//            'showScriptName' => false,
//            'rules' => [
//                [
//                    'class' => 'yii\rest\UrlRule',
////                    'controller' => 'v1/user', // Контроллер user в модуле v1
//                    'pluralize' => false, // Не множественное число в URL (оставим /user, не /users)
//                ],
//                // Можно добавить правила для других версий
//                // ['class' => 'yii\rest\UrlRule', 'controller' => 'v2/user', 'pluralize' => false],
//            ],
//        ],

    //mts
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => $urlRules,
        ],
    ],

    'modules' => [
        'v1' => [
            'class' => Module::class
        ],
    ],
    'params' => $params,
];