<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;

class UserController extends BaseController
{
    public $modelClass = 'common\models\User';

    public function actionTest()
    {

//        Yii::$app->user->identity
//        $User = Yii::$app->user;
//
//        var_dump($User->id);
//        var_dump($User->identity);
//
////        var_dump($User);


        return 'test bearer';

    }

}