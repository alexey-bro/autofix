<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\BookingApp;
use api\modules\v1\models\PromotionApp;
use api\modules\v1\models\ReviewApp;
use api\modules\v1\models\UserProfileApp;
use backend\controllers\ReviewController;
use common\models\Booking;
use common\models\CustomShiftTemplates;
use common\models\Promotion;
use common\models\Review;
use common\models\Services;
use common\models\Specialization;
use common\models\Specializations;
use common\models\UserProfile;
use common\models\WorkDaysShift;
use common\models\WorkTimeShift;
use DateTime;
use Yii;
use yii\rest\ActiveController;

class FunctionsController extends ActiveController
{
    //TODO: добавить во все экшены условия, чтобы без обязательных полей возвращались ошибки

    public $modelClass = 'common\models\User';

    public function actionLoginByPhone()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $phone = $params['phone'] ?? null;

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

        //TODO: загрузка фоток, ответ фотками доделать, сделать, сейчас никаких файлов нет

        return [
            'result' => $allUsers,
        ];

    }


    public function actionGetUserProfile()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $userId = $params['userId'] ?? null;

        if ($userId && ($userProfile = UserProfileApp::findOne(['id' => $userId]))) {

            return [
                'result' => $userProfile,
            ];

        }

        // TODO: добавить 404 еслм нет юзера
    }

    public function actionGetMasterById()
    {


        $params = Yii::$app->getRequest()->getBodyParams();
        $userId = $params['userId'] ?? null;

        if ($userId && ($userProfile = UserProfileApp::findOne(['id' => $userId]))) {

            return [
                'result' => $userProfile,
            ];

        }

        // TODO: добавить 404 еслм нет юзера

    }


    public function actionGetClientBookings()
    {

        // TODO::booking->status поправить в миграции, он там вообще не учитывается

        $params = Yii::$app->getRequest()->getBodyParams();
        $clientId = $params['clientId'] ?? null;

        $response = [];

        if ($clientId && ($Client = UserProfileApp::findOne(['id' => $clientId]))) {

            $listBooking = BookingApp::find()
                ->where(['client_id' => $clientId])
                ->all();


            foreach ($listBooking as $Booking) {

                // TODO: master->photoUrl, client->photoUrl пусто, сделать фотки
                /** @var Booking $Booking */
                $response[] = $Booking;

            }

        } else {
            // TODO: добавить 404 еслм нет клиента
        }

        return [
            'result' => $response,
        ];

    }


    public function actionGetMasterBookings()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        $masterId = $params['masterId'] ?? null;

        // TODO::booking->status поправить в миграции, он там вообще не учитывается

        $response = [];

        if ($masterId && ($Master = UserProfileApp::findOne(['id' => $masterId]))) {

            $listBooking = BookingApp::find()
                ->where(['master_id' => $masterId])
                ->all();


            foreach ($listBooking as $Booking) {

                $Booking->scenario = BookingApp::SCENARIO_BOOKING_MASTER;

                // TODO: master->photoUrl, client->photoUrl пусто, сделать фотки
                /** @var Booking $Booking */
                $response[] = $Booking;

            }

        } else {
            // TODO: добавить 404 еслм нет клиента
        }

        return [
            'result' => $response,
        ];
    }

    public function actionRequestPhoneVerification()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $phoneNumber = $params['phoneNumber'] ?? null;

        $ode = "123456"; // Пока заглушка

//        Здесь будет вызов SMS.ru / Twilio
//        console.log(`Код для ${phoneNumber}: ${code}`);

        return [
            'result' => 'Код отправлен',
        ];
    }

    public function actionGetPromotions()
    {
        $DT = new DateTime();

        $listPromotion = PromotionApp::find()
            ->where(['>=', 'publishedUntil', $DT->format('Y-m-d H:i:s')])
            ->all();

        return [
            'result' => $listPromotion,
        ];
    }

    public function actionGetReviewsForMaster()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $masterId = $params['masterId'] ?? null;

        $listReview = ReviewApp::find()
            ->where(['master_id' => $masterId])
            ->all();

        return [
            'result' => $listReview,
        ];
    }

    public function actionSubmitReview()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $masterId = $params['masterId'] ?? null;
        $authorId = $params['authorId'] ?? null;
        $authorName = $params['authorName'] ?? null;
        $rating = $params['rating'] ?? null;
        $text = $params['text'] ?? null;
        $title = $params['title'] ?? null; //TODO: хз зачем, его даже в базе нет

        if (!$masterId || !$authorId || !$rating || !$text) {
            return [
                "code" => 141,
                "error" => "Недостаточно данных для отзыва",
            ];
        }

        if ($rating < 1 || $rating > 5) {
            return [
                "code" => 141,
                "error" => "Оценка должна быть от 1 до 5",
            ];
        }

        $Client = UserProfileApp::findOne(['id' => $authorId]);
        $Master = UserProfileApp::findOne(['id' => $masterId]);

        if ($Client && $Master) {
            $Review = new Review();

            $Review->master_id = $Master->id;
            $Review->client_id = $Client->id;
            $Review->rating = $rating;
            $Review->text = $text;

            if ($authorName) {
                $Review->author_name = $authorName;
            }

            if ($Review->save()) {


                //TODO: немного прееписаьб это, перенести в модель пересчет рейтинга
                $currentRating = $Master->rating ?? 0;
                $currentCount = $Master->reviewsCount ?? 0;

                $newCount = $currentCount + 1;
                $newRating = ($currentRating * $currentCount + $rating) / $newCount;

                $Master->reviewsCount = $newCount;
                $Master->rating = $newRating;

                $Master->save();

                return [
                    "result" => [
                        "success" => true,
                        "reviewId" => $Review->id,
                    ]
                ];

            }

        } else {
            return [
                "code" => 141,
                "error" => "Ошибка, клиент или мастер не найдены",
            ];

        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];
    }

    public function actionSubmitPromotion()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $userId = $params['userId'] ?? null;
        $title = $params['title'] ?? null;
        $description = $params['description'] ?? null;
        $validUntil = $params['validUntil'] ?? null;
        $phoneNumber = $params['phoneNumber'] ?? null;
        $conditions = $params['conditions'] ?? null;
        $order = $params['order'] ?? 0;
        $imageUrl = $params['imageUrl'] ?? null;

        if ($Master = UserProfileApp::findOne(['id' => $userId])) {

            $Promotion = new Promotion();

            $Promotion->master_id = $Master->id;

            $Promotion->title = $title;
            $Promotion->description = $description;
            $Promotion->validUntil = $validUntil;
            $Promotion->phoneNumber = $phoneNumber;
            $Promotion->conditions = $conditions;
            $Promotion->order = $order;

//            $Promotion->imageUrl = $imageUrl; // TODO: доделать загрузку картинок

            if ($Promotion->save()) {
                return [
                    "result" => [
                        "success" => true,
                        "reviewId" => $Promotion->id,
                    ]
                ];

            }

        } else {
            return [
                "code" => 141,
                "error" => "userId обязателен",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }



    public function actionUpdatePromotion()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $promotionId = $params['promotionId'] ?? null;
        $userId = $params['userId'] ?? null;
        $title = $params['title'] ?? null;
        $description = $params['description'] ?? null;
        $validUntil = $params['validUntil'] ?? null;
        $phoneNumber = $params['phoneNumber'] ?? null;
        $conditions = $params['conditions'] ?? null;
        $order = $params['order'] ?? 0;
        $imageUrl = $params['imageUrl'] ?? null;

        $Master = UserProfileApp::findOne(['id' => $userId]);
        $Promotion = Promotion::findOne(['id' => $promotionId]);

        if ($Master && $Promotion) {

            if ($Master->id != $Promotion->master_id) {

                return [
                    "code" => 141,
                    "error" => "Нет прав на редактирование",
                ];
            }


            $Promotion->title = $title;
            $Promotion->description = $description;
            $Promotion->validUntil = $validUntil;
            $Promotion->phoneNumber = $phoneNumber;
            $Promotion->conditions = $conditions;
            $Promotion->order = $order;

//            $Promotion->imageUrl = $imageUrl; // TODO: доделать загрузку картинок

            if ($Promotion->save()) {
                return [
                    "result" => [
                        "success" => true,
                        "reviewId" => $Promotion->id,
                    ]
                ];

            }

        } else {
            return [
                "code" => 141,
                "error" => "Промоакция или пользователь не найдены",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }

    public function actionDeletePromotion()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

        $promotionId = $params['promotionId'] ?? null;
        $userId = $params['userId'] ?? null;

        $Master = UserProfileApp::findOne(['id' => $userId]);
        $Promotion = Promotion::findOne(['id' => $promotionId]);

        if ($Master && $Promotion) {

            if ($Master->id != $Promotion->master_id) {

                return [
                    "code" => 141,
                    "error" => "Нет прав на удаление",
                ];
            }

            // TODO: Сделать мягкое удаление

            if ($Promotion->delete()) {
                return [
                    "result" => [
                        "success" => true,
                    ]
                ];

            }

        } else {
            return [
                "code" => 141,
                "error" => "Промоакция или пользователь не найдены",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }


    // 1. Обновление базовых полей
    public function actionUpdateMasterBasic()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $userId = $params['userId'] ?? null;
        $city = $params['city'] ?? null;
        $companyName = $params['companyName'] ?? null;
        $experience = $params['experience'] ?? null; //стаж
        $specialization = $params['specialization'] ?? null;

        if ($UserProfile = UserProfileApp::findOne(['id' => $userId])) {

            $UserProfile->city = $city;
            $UserProfile->companyName = $companyName;
            $UserProfile->experience = $experience;

            if ($specialization) {

                //Удаляем старые специализации
                $listSpecializationUserProfile = Specialization::find()
                    ->where(['user_profile_id' => $UserProfile->id])
                    ->all();

                foreach ($listSpecializationUserProfile as $specializationUserProfile) {
                    $specializationUserProfile->delete();
                }


                $specialization = explode(',', $specialization ?? null);

                foreach ($specialization as $spec) {
                    $spec = trim($spec);

                    if ($specObject = Specializations::findOne(['name' => $spec])) {

                        $Specialization = new Specialization();
                        $Specialization->specialization_id = $specObject->id;
                        $Specialization->user_profile_id = $UserProfile->id;
                        $Specialization->save();

                    }

                }

            }

            if ($UserProfile->save()) {
                return [
                    "result" => $UserProfile
                ];

            }


        } else {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];

        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];
    }


    public function actionUpdateMasterRating()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $userId = $params['userId'] ?? null;
        $newRating = $params['newRating'] ?? null;

        if ($newRating < 1 || $newRating > 5) {
            return [
                "code" => 141,
                "error" => "Оценка должна быть от 1 до 5",
            ];
        }

        if ($UserProfile = UserProfileApp::findOne(['id' => $userId])) {

            $currentRating = $UserProfile->rating;
            $currentCount = $UserProfile->reviewsCount;

            // Новый средний рейтинг
            $totalRating = $currentRating * $currentCount + $newRating;
            $newReviewsCount = $currentCount + 1;
            $newAverage = $totalRating / $newReviewsCount;

            $UserProfile->rating = round($newAverage);
            $UserProfile->reviewsCount = $newReviewsCount;

            if ($UserProfile->save()) {
                return [
                    "result" => $UserProfile,
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }

    public function actionUpdateUserProfile()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

        $userId = $params['userId'] ?? null;

        $firstName = $params['firstName'] ?? null;
        $lastName = $params['lastName'] ?? null;
        $city = $params['city'] ?? null;
        $photoUrl = $params['photoUrl'] ?? null;
        $specialization = $params['specialization'] ?? null;
        $services = $params['services'] ?? null;
        $cars = $params['cars'] ?? null;
        $email = $params['email'] ?? null;
        $workShifts = $params['workShifts'] ?? null;
        $photos = $params['photos'] ?? null;
        $companyName = $params['companyName'] ?? null;
        $experience = $params['experience'] ?? null;
        $phone = $params['phone'] ?? null;
        $customShiftTemplates = $params['customShiftTemplates'] ?? null;
        $workAddress = $params['workAddress'] ?? null;




        if ($UserProfile = UserProfileApp::findOne(['id' => $userId])) {

            if ($firstName) {
                $UserProfile->firstName = $firstName;
            }

            if ($lastName) {
                $UserProfile->lastName = $lastName;
            }

            if ($city) {
                $UserProfile->city = $city;
            }

            //TODO: доделать фотокарточки, загрузку фоток сделать
//            if ($photoUrl) {
//                $UserProfile->photoUrl = $photoUrl;
//            }

            if ($specialization) {
                //TODO: вынести сохранение специализации в отдельное место

                //Удаляем старые специализации
                $listSpecializationUserProfile = Specialization::find()
                    ->where(['user_profile_id' => $UserProfile->id])
                    ->all();

                foreach ($listSpecializationUserProfile as $specializationUserProfile) {
                    $specializationUserProfile->delete();
                }


                $specialization = explode(',', $specialization ?? null);

                foreach ($specialization as $spec) {
                    $spec = trim($spec);

                    if ($specObject = Specializations::findOne(['name' => $spec])) {

                        $Specialization = new Specialization();
                        $Specialization->specialization_id = $specObject->id;
                        $Specialization->user_profile_id = $UserProfile->id;
                        $Specialization->save();

                    }

                }

            }

            if ($services) {

                $listServices = Services::find()
                    ->where(['user_profile_id' => $UserProfile->id])
                    ->all();

                foreach ($listServices as $Service) {
                    $Service->delete();
                }

                foreach ($services as $service) {

                    if ($Specialization = Specializations::findOne(['name' => $service['category']])) {

                        $Service = new Services();
                        $Service->user_profile_id = $UserProfile->id;
                        $Service->specialization_id = $Specialization->id;
                        $Service->name = $service['name'];
                        $Service->description = $service['description'];
                        $Service->price_from = $service['priceFrom'];
                        $Service->price_to = $service['priceTo'] ?? null;
                        $Service->save();

                        //TODO доделать сохранение фотографий

                    }
                }

            }

            if ($cars && is_iterable($cars)) {
                $carsString = serialize($cars);
                $UserProfile->cars = $carsString;
            }
//
            if ($email) {
                $UserProfile->email = $email;
            }

//
            if ($workShifts) {
//                var_dump($workShifts);
//                die;


                $UserWorkShiftDays = WorkDaysShift::find()->where(['user_profile_id' => $UserProfile->id])->all();
                $UserWorkShiftTimes = [];

                foreach ($UserWorkShiftDays as $UserWorkShiftDay) {

                    $workTimeShifts = $UserWorkShiftDay->workTimeShift;

                    foreach ($workTimeShifts as $workTimeShift) {
                        $workTimeShift->delete();
                    }

                    $UserWorkShiftDay->delete();
                }


                foreach ($workShifts as $workShift) {

                    $DTWorkShift = new DateTime($workShift['date']);

                    if (isset($workShift['slots'])) {

                        $WorkDaysShift = new WorkDaysShift();
                        $WorkDaysShift->user_profile_id = $UserProfile->id;
                        $WorkDaysShift->day = $DTWorkShift->format('Y-m-d');

                        if ($WorkDaysShift->save()) {
                            foreach ($workShift['slots'] as $slot) {

                                $DTWorkShiftTimeStart = new DateTime($slot['startTime']);
                                $DTWorkShiftTimeEnd = new DateTime($slot['endTime']);

                                $WorkTimeShift = new WorkTimeShift();
                                $WorkTimeShift->work_days_shift_id = $WorkDaysShift->id;
                                $WorkTimeShift->start = $DTWorkShiftTimeStart->format('H:i:s');
                                $WorkTimeShift->stop = $DTWorkShiftTimeEnd->format('H:i:s');
                                $WorkTimeShift->isAvailable = (int) $slot['isAvailable'];
                                $WorkTimeShift->save();

                            }
                        }


                    }

                }

            }

            if ($photos && is_iterable($photos)) {
                $photosString = serialize($photos);
                $UserProfile->photos = $photosString;
            }

            if ($companyName) {
                $UserProfile->companyName = $companyName;
            }

            if ($experience) {
                $UserProfile->experience = (integer) $experience;
            }

            if ($phone) {
                $UserProfile->phone = $phone;
            }

            if ($customShiftTemplates &&is_iterable($customShiftTemplates)) {

                $newCustomShiftTemplates = [];

                foreach ($customShiftTemplates as $customShiftTemplate) {
                    $customShiftTemplatesParts = explode('-', $customShiftTemplate);

                    $newCustomShiftTemplates[] = $customShiftTemplatesParts[0] . '-' . $customShiftTemplatesParts[1];
                }

                //Удаляем старые специализации
                $listCustomShiftTemplates = CustomShiftTemplates::find()
                    ->where(['user_profile_id' => $UserProfile->id])
                    ->all();

                foreach ($listCustomShiftTemplates as $CustomShiftTemplate) {
                    $CustomShiftTemplate->delete();
                }

                foreach ($newCustomShiftTemplates as $CustomShiftTemplate) {
                    $ModelCustomShiftTemplate = new CustomShiftTemplates();
                    $ModelCustomShiftTemplate->user_profile_id = $UserProfile->id;
                    $ModelCustomShiftTemplate->template = $CustomShiftTemplate;

                    $ModelCustomShiftTemplate->save();
                }

            }

            if ($workAddress) {
                $UserProfile->workAddress = $workAddress;
            }



//            if (firstName) user.set("firstName", firstName);
//            if (lastName) user.set("lastName", lastName);
//            if (city) user.set("city", city);
//            if (phone) user.set("phone", phone);
//            if (photoUrl) user.set("photoUrl", photoUrl);
//            if (specialization !== undefined) user.set("specialization", specialization); // ✅ Изменено с specializations на specialization
//            if (services) user.set("services", services);
//            if (cars) user.set("cars", cars);
//            if (email) user.set("email", email);
//            if (workShifts !== undefined) user.set("workShifts", workShifts);
//            if (photos !== undefined) user.set("photos", photos);
//            if (companyName !== undefined) user.set("companyName", companyName);
//            if (experience !== undefined) user.set("experience", experience);
//            if (customShiftTemplates !== undefined) user.set("customShiftTemplates", customShiftTemplates);
//            if (workAddress !== undefined) user.set("workAddress", workAddress);



            if ($UserProfile->save()) {
                return [
                    "result" => $UserProfile,
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];
        }

        return 'actionUpdateUserProfile';
    }


    public function actionBookSlot()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

//        $userId = $params['userId'] ?? null;


//        $book = Booking::findOne(1);
////        var_dump($book->workTimeShift);
//        var_dump($book->workDaysShift);
//
//        die;



        $masterId = $params['masterId'] ?? null;
        $clientId = $params['clientId'] ?? null;
        $serviceName = $params['serviceName'] ?? null;
        $startTime = $params['startTime'] ?? null;
        $endTime = $params['endTime'] ?? null;


        if (!$masterId || !$clientId || !$startTime || !$endTime) {
            return [
                "code" => 141,
                "error" => "Не хватает параметров",
            ];
        }

        $Master = UserProfile::findOne($masterId);
        $Client = UserProfile::findOne($clientId);

        if (!$Master) {
            return [
                "code" => 141,
                "error" => "Мастер не найден",
            ];
        }

        if (!$Client) {
            return [
                "code" => 141,
                "error" => "Клиент не найден",
            ];
        }


        // Извлекаем дату и время

        $DTStart = new DateTime($startTime);
        $DTEnd = new DateTime($endTime);

        if (!$DTStart || !$DTEnd) {
            return [
                "code" => 141,
                "error" => "Некорректные даты начала и окончания смены",
            ];
        }



        if ($DTStart->format('Y-m-d') != $DTEnd->format('Y-m-d')) {
            return [
                "code" => 141,
                "error" => "Некорректные даты начала и окончания смены",
            ];
        }

        $day = $DTStart->format('Y-m-d');
        $startTime = $DTStart->format('H:i:s');
        $endTime = $DTEnd->format('H:i:s');

        $bookingExists = Booking::find()
            ->joinWith(['workTimeShift'])
            ->joinWith(['workDaysShift'])
            ->where([
                'work_days_shift.user_profile_id' => $masterId,
                'work_days_shift.day' => $day,
            ])
            ->andWhere(['<=', 'work_time_shift.start', $startTime])   // Интервалы пересекаются, если
            ->andWhere(['>=', 'work_time_shift.stop', $endTime])  // начало < stop_нового И stop > start_нового
            ->exists();

        // TODO: еще добавить условия на booking.status,

        if ($bookingExists)  {
            return [
                "code" => 141,
                "error" => "Этот слот уже забронирован. Пожалуйста, выберите другой.",
            ];
        }


//        $slotExists = UserProfile::find()
//            ->joinWith(['workDaysShift'])
//            ->joinWith(['workTimeShift'])
//            ->where([
//                'user_profile.id' => $masterId,
//                'work_days_shift.day' => $day,
//            ])
//            ->andWhere(['<=', 'work_time_shift.start', $startTime])   // Интервалы пересекаются, если
//            ->andWhere(['>=', 'work_time_shift.stop', $endTime])  // начало < stop_нового И stop > start_нового
////                ->exists();
//            ->all();
//
//        var_dump($slotExists);
//        die;

        $Slot = $Master->getWorkTimeShiftByDateTime($DTStart, $DTEnd);

        $Service = Services::find()
            ->where([
                'user_profile_id' => $Master->id,
                'name' => $serviceName,
            ])->one();

        if (!$Service) {
            return [
                "code" => 141,
                "error" => "Услуга, которую вы запрашиваете, не найдена.",
            ];
        }


        if ($Slot) {

            if (!$Slot->isAvailable) {
                return [
                    "code" => 141,
                    "error" => "Этот слот уже недоступен для записи. Пожалуйста, выберите другой.",
                ];
            } else {

                $Booking = new Booking();
                $Booking->client_id = $Client->id;
                $Booking->master_id = $Master->id;
                $Booking->work_time_shift_id = $Slot->id;
                $Booking->status = Booking::STATUS_PROCESSING;
                $Booking->service_id = $Service->id;

                $Slot->isAvailable = 0;

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if (!$Booking->save()) {
                        return [
                            "code" => 141,
                            "error" => "Ошибка сохранения бронирования",
                        ];
                    }

                    if (!$Slot->save()) {
                        return [
                            "code" => 141,
                            "error" => "Ошибка сохранения слота",
                        ];
                    }

                    $transaction->commit();
                    return [
                        "result" => $Booking,
                    ];

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage(), __METHOD__);
                    return [
                        "code" => 141,
                        "error" => "Ошибка сохранения",
                    ];
                }
            }
        } else {
            return [
                "code" => 141,
                "error" => "Слот не найдем у мастера",
            ];
        }
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

    public function actionUpdateMasterScheduleAndServices()
    {
        return 'actionUpdateMasterScheduleAndServices';
    }

    public function actionWorkShifts()
    {
        return 'actionWorkShifts';
    }

    public function actionCancelBooking()
    {
        return 'actionCancelBooking';
    }

    public function actionUpdateBookingStatus()
    {
        return 'actionUpdateBookingStatus';
    }

    public function actionDeletePhoto()
    {
        return 'actionDeletePhoto';
    }

    public function actionDeleteWorkShift()
    {
        return 'actionDeleteWorkShift';
    }






}