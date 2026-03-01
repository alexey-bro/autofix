<?php

namespace api\components;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class WebControllerBase extends Controller
{
//    /**
//     * @throws BadRequestHttpException
//     */
//    public function beforeAction($action): bool
//    {
//        if (parent::beforeAction($action)) {
//            // Check if the user is authenticated
//            if (!Yii::$app->user->isGuest) {
//                Yii::$app->language = Yii::$app->user->identity->lang;
//            }
//
//            return true;
//        }
//
//        return false;
//    }
}