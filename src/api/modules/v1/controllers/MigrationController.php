<?php
namespace api\modules\v1\controllers;

use common\jobs\DownloadJob;
use common\models\Services;
use common\models\User;
use common\models\UserProfile;
use common\models\WorkDaysShift;
use common\models\WorkTimeShift;
use DateTime;
use Yii;
use yii\rest\ActiveController;

class MigrationController extends ActiveController
{
    public $modelClass = 'common\models\User';
//    public $modelClass = 'common\models\Booking';


    public function actionTest()
    {
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();

        Yii::$app->db->createCommand('TRUNCATE user')->execute();
        Yii::$app->db->createCommand('TRUNCATE user_profile')->execute();
        Yii::$app->db->createCommand('TRUNCATE specialization')->execute();
        Yii::$app->db->createCommand('TRUNCATE work_days_shift')->execute();
        Yii::$app->db->createCommand('TRUNCATE work_time_shift')->execute();
        Yii::$app->db->createCommand('TRUNCATE custom_shift_templates')->execute();
        Yii::$app->db->createCommand('TRUNCATE booking')->execute();
        Yii::$app->db->createCommand('TRUNCATE review')->execute();
        Yii::$app->db->createCommand('TRUNCATE promotion')->execute();

        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();


//        AppConfig.json
//        Booking.json
//        Payment.json
//        Promotion.json
//        Review.json
//        UserProfile.json
//        schema.json

        $pathBack4Db = Yii::getAlias('@console') . '/back4app_db';

        $dataUserProfile = json_decode(file_get_contents($pathBack4Db . '/UserProfile.json'));
        $dataUserProfile = $dataUserProfile->results;

        $i = 0;
        foreach ($dataUserProfile as $data) {
            $i++;


            $username = trim($data->firstName . ' ' . $data->lastName);
            $email = trim($data->email);

            $User                  = new User();
            $User->username        = $username;
            $User->email           = empty($email) ? null : $email;
            $User->status          = User::STATUS_ACTIVE;
            $User->role            = array_search($data->role, User::listRoles());
            $User->created_at      = time();
            $User->updated_at      = time();
            $User->phone           = $data->phone;
            $User->setPassword('12345@');
            $User->generateAuthKey();
            $User->generateEmailVerificationToken();

            if (!$User->save()) {
                var_dump($User->getErrors());
                die();
            }

            $UserProfile = new UserProfile();


//            $UserProfile->fullName = $data;
            $UserProfile->_user_id = $data->userId;
            $UserProfile->user_id = $User->id;
            $UserProfile->carBrand = $data->carBrand ?? null;
            $UserProfile->city = $data->city ?? null;
            $UserProfile->rating = $data->rating ?? null;
//            $UserProfile->reviewsCount = '';
            $UserProfile->latitude = (string)($data->latitude ?? null);
            $UserProfile->longitude = (string)($data->longitude ?? null);
            $UserProfile->workAddress = $data->workAddress ?? null;
            $UserProfile->firstName = $data->firstName ?? null;
            $UserProfile->lastName = $data->lastName ?? null;
            $UserProfile->experience = $data->experience ?? null;
            $UserProfile->companyName = $data->companyName ?? null;


            if ($UserProfile->save()) {

                foreach ($data->services as $service) {

                    $specId = Yii::$app->db->createCommand("SELECT id FROM specializations WHERE name = :name")
                        ->bindValue(':name', $service->category)
                        ->queryOne();

                    if ($specId) {
                        Yii::$app->db->createCommand()
                            ->insert('services', [

                                'user_id' => $User->id,
                                'specialization_id' => $specId['id'],
                                'name' => $service->name,
                                'description' => $service->description,
                                'price_from' => $service->priceFrom,
                                'price_to' => $service->priceTo,
                            ])->execute();
                    }
                }


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
                                    'user_id' => $User->id,
                                    'specialization_id' => $specId['id'],
                                ])->execute();
                        }
                    }
                }

                if (isset($data->workShifts)) {

                    foreach ($data->workShifts as $workShift) {

                        Yii::$app->db->createCommand()
                            ->insert('work_days_shift', [
                                'user_id' => $User->id,
                                'day' => $workShift->date,
                            ])->execute();

                        $workDaysShiftId = Yii::$app->db->getLastInsertID();

                        foreach ($workShift->slots as $timeShift) {

                            $DTStart = new \DateTime($timeShift->startTime);
                            $DTEnd = new \DateTime($timeShift->endTime);

                            if ($DTStart && $DTEnd) {
                                Yii::$app->db->createCommand()
                                    ->insert('work_time_shift', [
                                        'work_days_shift_id' => $workDaysShiftId,
                                        'start' => $DTStart->format('H:i:s'),
                                        'stop' => $DTEnd->format('H:i:s'),
                                        'isAvailable' => (bool)$timeShift->isAvailable,
                                    ])->execute();
                            }


                        }

                    }
                }

                if (isset($data->customShiftTemplates) && $data->customShiftTemplates) {

                    foreach ($data->customShiftTemplates as $customShiftTemplate) {
                        $res = explode('-', $customShiftTemplate);

                        Yii::$app->db->createCommand()
                            ->insert('custom_shift_templates', [
                                'user_id' => $User->id,
                                'template' => $res[0] . '-' . $res[1],
                            ])->execute();
                    }


                }

            }

        }


        $dataBooking = json_decode(file_get_contents($pathBack4Db . '/Booking.json'));
        $dataBooking = $dataBooking->results;

        foreach ($dataBooking as $data) {

            $UserProfileMaster = UserProfile::findOne(['_user_id' => $data->masterId]);
            $UserProfileClient = UserProfile::findOne(['_user_id' => $data->clientId]);

            $DTWorkDayShift = new DateTime($data->slotStart->iso);

            $workDayShift = WorkDaysShift::find()
                ->where([
                    'user_id' => $UserProfileMaster->user_id,
                    'day' => $DTWorkDayShift->format('Y-m-d'),
                ])->one();


            if ($workDayShift) {
                $workTimeShift = WorkTimeShift::find()
                    ->where([
                        'work_days_shift_id' => $workDayShift->id,
                        'start' => $DTWorkDayShift->format('H:i:s'),
                    ])->one();

                $service = Services::find()
                ->where([
                    'user_id' => $UserProfileMaster->user_id,
                    'name' => $data->serviceName,
                ])->one();


                if ($workTimeShift && $service) {

                    Yii::$app->db->createCommand()
                        ->insert('booking', [
                            'client_id' => $UserProfileClient->user_id,
                            'master_id' => $UserProfileMaster->user_id,
                            'work_time_shift_id' => $workTimeShift->id,
                            'service_id' => $service->id,
                        ])->execute();

                }

            }

        }

        $dataReview = json_decode(file_get_contents($pathBack4Db . '/Review.json'));
        $dataReview = $dataReview->results;

        foreach ($dataReview as $data) {

            $UserProfileMaster = UserProfile::findOne(['_user_id' => $data->masterId]);
            $UserProfileClient = UserProfile::findOne(['_user_id' => $data->authorId]);

            // TODO: исправить кодировку text для хранения смайликов
            Yii::$app->db->createCommand()
                ->insert('review', [
                    'client_id' => $UserProfileClient->user_id,
                    'master_id' => $UserProfileMaster->user_id,
                    'rating' => $data->rating,
                    'author_name' => $data->authorName,
                    'text' => $data->text,
                ])->execute();
        }

        $dataPromotion = json_decode(file_get_contents($pathBack4Db . '/Promotion.json'));
        $dataPromotion = $dataPromotion->results;

        foreach ($dataPromotion as $data) {
            $UserProfileMaster = UserProfile::findOne(['_user_id' => $data->createdByMasterId]);

            $publishedUntil = new DateTime($data->publishedUntil->iso);

            Yii::$app->db->createCommand()
                ->insert('promotion', [
                    'title' => $data->title,
                    'description' => $data->description,
                    'validUntil' => $data->validUntil,
//                    'order' => $data->order,
                    'phoneNumber' => $data->phoneNumber,
                    'conditions' => $data->conditions,
                    'master_id' => $UserProfileMaster->user_id,
                    'isPaid' => (int) false,
                    'publishedUntil' => $publishedUntil->format('Y-m-d H:i:s'),
                ])->execute();


        }

        echo "Done! " . time();

    }

    public function actionExampleJob()
    {

        $r = Yii::$app->queue->push(new DownloadJob([
            'url' => 'http://example.com/image.jpg',
            'file' => '/tmp/image.jpg',
        ]));

        var_dump($r);


    }


}