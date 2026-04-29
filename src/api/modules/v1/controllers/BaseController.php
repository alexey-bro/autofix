<?php

namespace api\modules\v1\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class BaseController extends ActiveController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // Подключаем Bearer-аутентификацию
        $behaviors['authenticator'] = [
            'class'  => HttpBearerAuth::class,

            // Маршруты, которые не требуют авторизации:
            'except' => ['login', 'register'],
        ];

        return $behaviors;
    }
}