<?php

namespace api\modules\v1\controllers;

use light\swagger\SwaggerAction;
use light\swagger\SwaggerApiAction;
use Yii;
use yii\filters\auth\HttpBearerAuth;
//use yii\rest\Controller;
use yii\helpers\Url;
use yii\web\Controller;

class ApiController extends Controller
{
//    public function behaviors(): array
//    {
//        $behaviors = parent::behaviors();
//
//        $behaviors['authenticator'] = [
//            'class'  => HttpBearerAuth::class,
//            'except' => ['swagger'],
//        ];
//
//        return $behaviors;
//    }


//    public function behaviors(): array
//    {
//        $behaviors = parent::behaviors();
//
//        $behaviors['access'] = [
//            'class' => AccessControl::class,
//            'rules' => [
//                [
//                    'actions' => ['swagger-app', 'app-partner', 'app-driver', 'app-client', 'app-employee', 'swagger-loyalty', 'loyalty'],
//                    'allow' => true,
//                    'roles' => [User::ROLE_ADMIN],
//                ],
//                [
//                    'actions' => ['swagger-client'],
//                    'allow' => true,
//                    'roles' => [User::ROLE_ADMIN, User::ROLE_DEALER, User::ROLE_CLIENT],
//                ],
//                [
//                    'actions' => ['company-client'],
//                    'allow' => true,
//                    'roles' => [User::ROLE_ADMIN, User::ROLE_CLIENT],
//                ],
//                [
//                    'actions' => ['company-dealer'],
//                    'allow' => true,
//                    'roles' => [User::ROLE_ADMIN, User::ROLE_DEALER],
//                ],
//            ],
//        ];
//
//        return $behaviors;
//    }

    public function actionSwagger()
    {
        var_dump(Yii::getAlias('@api/modules/v1/controllers'));

    }

    public function actions(): array
    {
        return [
            //addesss:http://api.yourhost.com/v1/api/swagger-app
            'swagger-app' => [
                'class' => SwaggerAction::class,
                'restUrl' => [
                    ['name' => 'App.api', 'url' => Url::to(['/v1/api/app'], 'http')],
                ],
                'configurations' => ['persistAuthorization' => true, 'sorter' => 'alpha']
            ],
            //addesss:http://api.yourhost.com/v1/api/app
            'app' => [
                'class' => SwaggerApiAction::class,
                //The scan directories, you should use real path there.
                'scanDir' => [
                    Yii::getAlias('@api/modules/v1/controllers'),
                    Yii::getAlias('@api/modules/v1/swagger'),
                ]
            ],

        ];
    }
}