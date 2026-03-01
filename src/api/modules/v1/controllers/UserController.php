<?php
namespace api\modules\v1\controllers;

use common\models\UserProfile;
use Yii;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
//    public $modelClass = 'common\models\Booking';


    public function actionTest()
    {

        Yii::$app->db->createCommand('TRUNCATE user_profile')->execute();
        Yii::$app->db->createCommand('TRUNCATE specialization')->execute();


//        AppConfig.json
//        Booking.json
//        Payment.json
//        Promotion.json
//        Review.json
//        UserProfile.json
//        schema.json

        $pathBack4Db = Yii::getAlias('@console') . '/back4app_db';

//        var_dump($pathBack4Db);

        $dataUserProfile = json_decode(file_get_contents($pathBack4Db . '/UserProfile.json'));
        $dataUserProfile = $dataUserProfile->results;

//        $data = $dataUserProfile[0];

//        $specialization = explode(',', null);
//        var_dump($specialization);
//
//        die;


        $i = 0;
        foreach ($dataUserProfile as $data) {
            $i++;

            $UserProfile = new UserProfile();


//            $UserProfile->fullName = $data;
            $UserProfile->role = array_search($data->role, UserProfile::listRoles());
//            $UserProfile->user_id = '';
            $UserProfile->phone = $data->phone;
//            $UserProfile->carBrand = ;
//            $UserProfile->city = '';
            $UserProfile->email = $data->email;
            $UserProfile->rating = $data->rating ?? null;
//            $UserProfile->reviewsCount = '';
            $UserProfile->latitude = (string) ($data->latitude ?? null);
            $UserProfile->longitude = (string) ($data->longitude ?? null);
            $UserProfile->workAddress = $data->workAddress ?? null;
            $UserProfile->firstName = $data->firstName ?? null;
            $UserProfile->lastName = $data->lastName ?? null;
            $UserProfile->experience = $data->experience ?? null;
            $UserProfile->companyName = $data->companyName ?? null;

//            $r = $UserProfile->save();
//            var_dump($r);

//            $UserProfile->validate();
//            var_dump($UserProfile->getErrors());

            if ($UserProfile->save()) {

                if (isset($data->specialization)) {
                    $specialization = explode(',', $data->specialization ?? null);

                    foreach ($specialization as $spec) {
                        $spec = trim($spec);

                        $specId = Yii::$app->db->createCommand("SELECT id FROM specializations WHERE name = :name")
                            ->bindValue(':name', $spec)
                            ->queryOne();

                        if ($specId) {
                            Yii::$app->db->createCommand()
                                ->insert('specialization', [
                                    'user_profile_id' => $UserProfile->id,
                                    'specialization_id' => $specId['id'],
                                ])->execute();
                        }
                    }


                }
            }





//            var_dump($data->objectId);
//            var_dump($specialization);

//            $UserProfile->created_at = '';
//            $UserProfile->updated_at = '';

//            $r = $UserProfile->validate();

//            var_dump($i);
//            $UserProfile->save();
//            var_dump($UserProfile->getErrors());

//        var_dump($data);
//
        }


//        return $path;
    }


}