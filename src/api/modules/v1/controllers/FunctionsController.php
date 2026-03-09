<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\UserProfileApp;
use common\models\UserProfile;
use Yii;
use yii\rest\ActiveController;

class FunctionsController extends ActiveController
{

    public $modelClass = 'common\models\User';

    public function actionLoginByPhone()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $phone = $params['phone'];

        $UserProfile = UserProfileApp::find()
            ->where(['phone' => $phone])
            ->one();


        return [
            'result' => $UserProfile,
        ];


//        "result": {
//        "objectId": "RD10hCVAbS",
//        "userId": "user_1770296843741",
//        "role": "CLIENT",
//        "firstName": "Алексеев",
//        "lastName": "Иванов",
//        "phone": "+79507606922",
//        "carBrand": "Ренжровер",
//        "city": "",
//        "email": "",
//        "rating": 0,
//        "photos": [],
//        "services": [],
//        "reviewsCount": 0,
//        "companyName": null,
//        "customShiftTemplates": null
    }

    public function actionGetAllUsers()
    {
        $allUsers = UserProfileApp::find()
//            ->where(['_user_id' => 'user_1763968323791'])
            ->all();

        return [
            'result' => $allUsers,
        ];

    }


    public function actionCreateYookassaPayment()
    {
        return 'actionCreateYookassaPayment';
    }

    public function actionCheckAndCreatePromotion()
    {
        return 'actionCheckAndCreatePromotion';
    }

    public function actionYookassaWebhook()
    {
        return 'actionYookassaWebhook';
    }

    public function actionUpdateMasterBasic()
    {
        return 'actionUpdateMasterBasic';
    }

    public function actionUpdateMasterScheduleAndServices()
    {
        return 'actionUpdateMasterScheduleAndServices';
    }

    public function actionRequestPhoneVerification()
    {
        return 'actionRequestPhoneVerification';
    }

    public function actionUpdateUserProfile()
    {
        return 'actionUpdateUserProfile';
    }

    public function actionUpdateMasterRating()
    {
        return 'actionUpdateMasterRating';
    }

    public function actionBookSlot()
    {
        return 'actionBookSlot';
    }

    public function actionWorkShifts()
    {
        return 'actionWorkShifts';
    }

    public function actionCancelBooking()
    {
        return 'actionCancelBooking';
    }

    public function actionGetClientBookings()
    {
        return 'actionGetClientBookings';
    }

    public function actionGetMasterBookings()
    {
        return 'actionGetMasterBookings';
    }

    public function actionUpdateBookingStatus()
    {
        return 'actionUpdateBookingStatus';
    }

    public function actionDeletePhoto()
    {
        return 'actionDeletePhoto';
    }

    public function actionGetPromotions()
    {
        return 'actionGetPromotions';
    }

    public function actionGetUserProfile()
    {
        return 'actionGetUserProfile';
    }

    public function actionGetMasterById()
    {
        return 'actionGetMasterById';
    }

    public function actionDeleteWorkShift()
    {
        return 'actionDeleteWorkShift';
    }

    public function actionSubmitReview()
    {
        return 'actionSubmitReview';
    }

    public function actionGetReviewsForMaster()
    {
        return 'actionGetReviewsForMaster';
    }

    public function actionSubmitPromotion()
    {
        return 'actionSubmitPromotion';
    }

    public function actionDeletePromotion()
    {
        return 'actionDeletePromotion';
    }

    public function actionUpdatePromotion()
    {
        return 'actionUpdatePromotion';
    }


}