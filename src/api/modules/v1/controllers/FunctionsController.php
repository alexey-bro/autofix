<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\BookingApp;
use api\modules\v1\models\PromotionApp;
use api\modules\v1\models\ReviewApp;
use api\modules\v1\models\ServicesApp;
use api\modules\v1\models\UserApp;
use api\modules\v1\models\UserProfileApp;
use api\modules\v1\models\WorkDaysShiftApp;
use backend\assets\AppAsset;
use backend\controllers\ReviewController;
use common\models\Booking;
use common\models\CustomShiftTemplates;
use common\models\File;
use common\models\Payment;
use common\models\Promotion;
use common\models\Review;
use common\models\Services;
use common\models\Specialization;
use common\models\Specializations;
use common\models\UserProfile;
use common\models\UserToken;
use common\models\WorkDaysShift;
use common\models\WorkTimeShift;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use YooKassa\Client;
use OpenApi\Attributes as OA;


#[OA\Info(
    title: "Autofix APP",
    version: "1.0"
)]
#[OA\OpenApi(
    security: [
        ['bearerAuth' => []],
        ['ApiKeyAuth' => []]
    ]
)]
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    name: "X-API-KEY",
    in: "header"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    description: "JWT Authorization header using the Bearer scheme",
    name: "Authorization",
    scheme: "bearer",
    in: "header"
)]
#[OA\Server(
    url: "https://api.findmymechanic.ru/v1",
    description: "Production server"
)]
#[OA\Server(
    url: "http://api.autofix.loc/v1",
    description: "Local development server"
)]
// Глобальное описание тегов (для кастомизации отображения)
#[OA\Tag(
    name: "Auth",
    description: "Аутентификация и авторизация пользователей"
)]
#[OA\Tag(
    name: "Client",
    description: "Запросы клиента"
)]
#[OA\Tag(
    name: "Master",
    description: "Запросы мастера"
)]
#[OA\Tag(
    name: "Common",
    description: "Общие запросы"
)]
#[OA\Tag(
    name: "Schemas",
    description: "Схемы данных API"
)]
class FunctionsController extends BaseController
{
    //TODO: добавить во все экшены условия, чтобы без обязательных полей возвращались ошибки

    public $modelClass = 'common\models\User';

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class'  => HttpBearerAuth::class,
            'except' => ['login-by-phone'], // эти экшены открытые
        ];

        return $behaviors;
    }


    #[OA\Post(
        path: '/functions/login-by-phone',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            description: 'User phone number',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginByPhoneRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешная авторизация',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LoginResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionLoginByPhone()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $phone = $params['phone'] ?? null;

        $User = UserApp::find()
            ->where(['phone' => $phone])
            ->one();

        if ($User) {
            // Удаляем просроченные токены
            UserToken::deleteExpired($User->id);

            // Генерируем новый токен
            $UserToken = UserToken::generate($User->id);

            return [
                'result' => $User,
                'auth' => [
                    'token'      => $UserToken->token,
                    'expired_at' => $UserToken->expired_at,
                    'user'       => [
                        'id'       => $User->id,
                        'username' => $User->username,
                        'email'    => $User->email,
                    ],
                ],
            ];

        } else {
            throw new UnauthorizedHttpException('Ошибка авторизации');
        }
    }

    #[OA\Post(
        path: '/functions/get-all-users',
        description: 'description',
        summary: 'Список всех пользователей',
        tags: ['Common'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UsersListResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionGetAllUsers()
    {
        $allUsers = UserApp::find()->all();

        return [
            'result' => $allUsers,
        ];

    }

    #[OA\Post(
        path: '/functions/get-user-profile',
        summary: 'Профиль пользователя',
        tags: ['Common'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Профиль пользователя',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/UserResult'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionGetUserProfile()
    {
        $User = UserApp::findOne(Yii::$app->user->id);

        return [
            'result' => $User,
        ];

        // TODO: добавить 404 еслм нет юзера
    }

    #[OA\Post(
        path: '/functions/get-master-by-id',
        summary: 'Профиль пользователя (мастера)',
        tags: ['Master'],
        requestBody: new OA\RequestBody(
            description: 'User Id',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserId')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Профиль пользователя',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            ref: '#/components/schemas/UserResult'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionGetMasterById()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        $userId = $params['userId'] ?? null;

        if ($userId && ($User = UserApp::findOne(['id' => $userId]))) {

            return [
                'result' => $User,
            ];

        } else {
            throw new NotFoundHttpException('Пользователь не найден');
        }

    }

    #[OA\Post(
        path: '/functions/get-client-bookings',
        tags: ['Client'],
        summary: 'Получить свои бронирования (для клиента)',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/BookingsListResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionGetClientBookings()
    {

        // TODO::booking->status поправить в миграции, он там вообще не учитывается

        $params = Yii::$app->getRequest()->getBodyParams();

        $Client = UserApp::findOne(Yii::$app->user->id);

        $response = [];

        if ($Client) {

            $listBooking = BookingApp::find()
                ->where(['client_id' => $Client->id])
                ->all();


            foreach ($listBooking as $Booking) {
                /** @var Booking $Booking */
                $response[] = $Booking;
            }

        } else {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return [
            'result' => $response,
        ];

    }

    #[OA\Post(
        path: '/functions/get-master-bookings',
        summary: 'Получить свои бронирования (для мастера)',
        tags: ['Master'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MasterBookingsListResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
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

                /** @var Booking $Booking */
                $response[] = $Booking;

            }

        } else {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return [
            'result' => $response,
        ];
    }

    #[OA\Post(
        path: '/functions/request-phone-verification',
        summary: 'Отправка кода на телефон',
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/phoneNumber'  // <-- одна строка
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "result",
                            type: "string",
                            example: "Код отправлен"
                        ),
                        new OA\Property(
                            property: "code",
                            description: "Код подтверждения (возвращается только в режиме отладки)",
                            type: "string",
                            example: "123456",
                            nullable: true
                        )
                    ]

                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionRequestPhoneVerification()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $phoneNumber = $params['phoneNumber'] ?? null;

        $code = "123456"; // Пока заглушка

//        Здесь будет вызов SMS.ru / Twilio
//        console.log(`Код для ${phoneNumber}: ${code}`);

        return [
            'result' => 'Код отправлен',
            'code' => $code,
        ];
    }

    #[OA\Post(
        path: '/functions/get-promotions',
        tags: ['Client'],
        summary: 'Список промоакций',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/PromotionsListResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
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

    #[OA\Post(
        path: '/functions/get-rewiews-for-master',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
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

    #[OA\Post(
        path: '/functions/submit-review',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionSubmitReview()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $Client = UserApp::findOne(Yii::$app->user->id);

        $masterId = $params['masterId'] ?? null;
        $authorName = $params['authorName'] ?? null;
        $rating = $params['rating'] ?? null;
        $text = $params['text'] ?? null;
        $title = $params['title'] ?? null; //TODO: хз зачем, его даже в базе нет

        if (!$masterId || !$Client || !$rating || !$text) {
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

    #[OA\Post(
        path: '/functions/submit-promotion',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionSubmitPromotion()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

        $Master = UserApp::findOne(Yii::$app->user->id);

        $title = $params['title'] ?? null;
        $description = $params['description'] ?? null;
        $validUntil = $params['validUntil'] ?? null;
        $phoneNumber = $params['phoneNumber'] ?? null;
        $conditions = $params['conditions'] ?? null;

        $amount = $params['amount'] ?? null;

        $durationDays = $params['durationDays'] ?? Promotion::DEFAULT_DURATION_DAYS;

        if ($Master) {

            $Promotion = new Promotion();

            $Promotion->master_id = $Master->id;

            $Promotion->title = $title;
            $Promotion->description = $description;
            $Promotion->validUntil = $validUntil;
            $Promotion->phoneNumber = $phoneNumber;
            $Promotion->conditions = $conditions;
            $Promotion->durationDays = $durationDays;
            $Promotion->amount = $amount;

            if ($Promotion->save()) {

                if ($amount !== 0 && $amount !== '0') {

                    $shopId = Yii::$app->params['shopId'];
                    $secretKey = Yii::$app->params['secretKey'];

                    $client = new Client();
                    $client->setAuth($shopId, $secretKey);
                    $idempotenceKey = uniqid('', true);

                    if ($amount === null) {
                        $amount = PromotionApp::DEFAULT_PRICE;
                    }

                    try {

                        $response = $client->createPayment(
                            array(
                                'amount' => array(
                                    'value' => $amount,
                                    'currency' => 'RUB',
                                ),
                                'confirmation' => array(
                                    'type' => 'redirect',
                                    'return_url' => 'https://yookassa.ru',
                                ),
                                'capture' => true,
                                'description' => $Promotion->title ?? 'Оплата размещения акции',
                            ),
                            $idempotenceKey
                        );

                        $yookassaPaymentId = $response->getId();
                        $yookassaPaymentStatus = $response->getStatus();
                        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();

                        if ($yookassaPaymentId && $yookassaPaymentStatus && $confirmationUrl) {

                            $Payment = new Payment();
                            $Payment->promotion_id = $Promotion->id;
                            $Payment->user_id = $Master->id;
                            $Payment->payment_id = $yookassaPaymentId;
                            $Payment->confirmationUrl = $confirmationUrl;
                            $Payment->status = Payment::getStatusViaValue($yookassaPaymentStatus);

                            if ($Payment->save()) {

                                $Promotion->payment_id = $Payment->id;
                                $Promotion->save();

                                return [
                                    "result" => [
                                        "success" => true,
                                        "promotionId" => $Promotion->id,
                                        "confirmationUrl" => $confirmationUrl,
                                        "amount" => $amount,
                                        "isPaid" => Promotion::ID_PAID_NO,
                                    ]
                                ];
                            }

                        }

                    } catch (\Exception $e) {
                        return [
                            "code" => 141,
                            "error" => "Ошибка создания промо акции",
                        ];
                    }
                } else {

                    $Promotion->isPaid = Promotion::ID_PAID_YES;
                    $DTEndPromotion = new DateTime();
                    $DTEndPromotion->modify('+' . $durationDays . ' day');
                    $Promotion->publishedUntil = $DTEndPromotion->format('Y-m-d H:i:s'); // TODO: сделать дату окончанием дня 23.59.59 часов
                    $Promotion->save(false);

                    return [
                        "result" => [
                            "success" => true,
                            "promotionId" => $Promotion->id,
                            "confirmationUrl" => "",
                            "amount" => $amount,
                            "isPaid" => Promotion::ID_PAID_YES,
                        ]
                    ];
                }

            }

        } else {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }

    #[OA\Post(
        path: '/functions/update-promotion',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionUpdatePromotion()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $Master = UserApp::findOne(Yii::$app->user->id);

        if (!$Master) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $promotionId = $params['promotionId'] ?? null;
        $title = $params['title'] ?? null;
        $description = $params['description'] ?? null;
        $validUntil = $params['validUntil'] ?? null;
        $phoneNumber = $params['phoneNumber'] ?? null;
        $conditions = $params['conditions'] ?? null;
        $imageUrl = $params['imageUrl'] ?? null;

        $Promotion = Promotion::find()
            ->where([
                'id' => $promotionId,
                'master_id' => $Master->id,
            ])->one();


        if ($Master && $Promotion) {

            $Promotion->title = $title;
            $Promotion->description = $description;
            $Promotion->validUntil = $validUntil;
            $Promotion->phoneNumber = $phoneNumber;
            $Promotion->conditions = $conditions;

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
            throw new NotFoundHttpException('Промоакция или пользователь не найдены');
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }

    #[OA\Post(
        path: '/functions/payment-promotion',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionPaymentPromotion()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $promotionId = $params['promotionId'] ?? null;
        $Master = UserApp::findOne(Yii::$app->user->id);

        $Promotion = Promotion::find()
            ->where([
                'id' => $promotionId,
                'master_id' => $Master->id,
            ])->one();

        if ($Promotion) {

            $Payment = $Promotion->payment;

            if ($Promotion->isPaid) {
                return [
                    "code" => 141,
                    "error" => "Акция уже оплачена",
                ];
            }

            if ($Promotion->amount > 0 && $Payment && in_array($Payment->status, [Payment::PENDING, Payment::CANCELED])) {

                $shopId = Yii::$app->params['shopId'];
                $secretKey = Yii::$app->params['secretKey'];

                $client = new \YooKassa\Client();
                $client->setAuth($shopId, $secretKey);

                $paymentId = $Payment->payment_id;
                try {
                    $response = $client->getPaymentInfo($paymentId);

                    $statusPayment = $response->getStatus();

                    if ($statusPayment == Payment::listStatus()[Payment::PENDING]) {
                        return [
                            'result' => [
                                'payment' => $Payment->confirmationUrl,
                            ]
                        ];
                    } elseif ($statusPayment == Payment::listStatus()[Payment::CANCELED]) {

                        $idempotenceKey = uniqid('', true);
                        $response = $client->createPayment(
                            array(
                                'amount' => array(
                                    'value' => $Promotion->amount,
                                    'currency' => 'RUB',
                                ),
                                'confirmation' => array(
                                    'type' => 'redirect',
                                    'return_url' => 'https://yookassa.ru',
                                ),
                                'capture' => true,
                                'description' => $Promotion->title ?? 'Оплата размещения акции',
                            ),
                            $idempotenceKey
                        );

                        $yookassaPaymentId = $response->getId();
                        $yookassaPaymentStatus = $response->getStatus();
                        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();

                        if ($yookassaPaymentId && $yookassaPaymentStatus && $confirmationUrl) {

                            //TODO: протестировать все это
                            //написать тесты апи

                            //все предидущие пайменты поменять статус на canceled
                            $allPaymentPromotion = Payment::find()->where(['promotion_id' => $Promotion->id])->all();
                            foreach ($allPaymentPromotion as $_payment) {
                                $_payment->status = Payment::CANCELED;
                                $_payment->save();
                            }

                            $Payment = new Payment();
                            $Payment->promotion_id = $Promotion->id;
                            $Payment->user_id = $Master->id;
                            $Payment->payment_id = $yookassaPaymentId;
                            $Payment->confirmationUrl = $confirmationUrl;
                            $Payment->status = Payment::getStatusViaValue($yookassaPaymentStatus);

                            if ($Payment->save()) {

                                $Promotion->payment_id = $Payment->id;
                                $Promotion->save(false);

                                return [
                                    'result' => [
                                        'payment' => $Payment->confirmationUrl,
                                    ]
                                ];
                            }
                        }

                    } elseif ($statusPayment == Payment::listStatus()[Payment::WAITING_FOR_CAPTURE]) {
                        return [
                            "code" => 141,
                            "error" => "Платеж в обработке",
                        ];
                    } elseif ($statusPayment == Payment::listStatus()[Payment::SUCCEEDED]) {
                        $Payment->status = Payment::SUCCEEDED;
                        $Payment->save();
                        $Promotion->payment_id = Promotion::ID_PAID_YES;
                        $Promotion->save();

                        if ($Payment->save() && $Promotion->save()) {
                            return [
                                "code" => 141,
                                "error" => "Акция оплачена",
                            ];
                        }

                    }

                    //TODO: дописать если у плвтежа статус оплачен

//                    var_dump($response->getStatus());

                } catch (\Exception $e) {
                    return [
                        "code" => 141,
                        "error" => "Ошибка",
                    ];
                }

//                die;
//
////                var_dump($response->getPaid());
////                var_dump($response->getCreatedAt());
//                var_dump($response->getExpiresAt());
//
//                die;
//                // Получить JSON представление платежа
//                $jsonString = $response->jsonSerialize();
//                var_dump($jsonString);
//                die;
//
//                $prettyJson = json_encode($jsonString, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//                var_dump($prettyJson);
//                die;
//
//
//                echo "=== Полный ответ в JSON ===\n";
//                echo $prettyJson . "\n";
//
//// Или более короткий вариант напрямую
//                echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//
//
//                die;





                // TODO: добавить ссылку из проиотион на паймент модель
                // TODO: добавить ссылку из паймента на промотин

                //amount у промотион должен быть
                //Создается новая оплата
                // если промотион не оплачен
                // если у него статус пендинг (проверить информацию о текущем платеже)
                // проверить сколько времени осталось у текущего платежа
                // после отплаты выставить статус оплачено и дату истечения

//                return [
//                    "result" => [
//                        "success" => true,
//                    ]
//                ];

            } else {
                return [
                    "code" => 141,
                    "error" => "Ошибка создания платежа",
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Промоакция или пользователь не найдены",
            ];
        }

    }

    // TODO: webhook для проверки оплаты и измененя статуса платежа
    #[OA\Post(
        path: '/functions/delete-promotion',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionDeletePromotion()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

        $Master = UserApp::findOne(Yii::$app->user->id);

        $promotionId = $params['promotionId'] ?? null;

        $Promotion = Promotion::find()
            ->where([
                'id' => $promotionId,
                'master_id' => $Master->id,
            ])->one();

        if ($Promotion) {

            // TODO: Сделать мягкое удаление

            if ($Promotion->delete()) {
                return [
                    "result" => [
                        "success" => true,
                    ]
                ];

            }

        } else {
            throw new NotFoundHttpException('Промоакция или пользователь не найдены');
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];
    }


    #[OA\Post(
        path: '/functions/updatemaster-basic',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    // 1. Обновление базовых полей
    public function actionUpdateMasterBasic()
    {
        $User = UserApp::findOne(Yii::$app->user->id);

        $params = Yii::$app->getRequest()->getBodyParams();

        $city = $params['city'] ?? null;
        $companyName = $params['companyName'] ?? null;
        $experience = $params['experience'] ?? null; //стаж
        $specialization = $params['specialization'] ?? null;

        if ($User) {

            $UserProfile = $User->userProfile;

            $UserProfile->city = $city;
            $UserProfile->companyName = $companyName;
            $UserProfile->experience = $experience;

            if ($specialization) {

                //Удаляем старые специализации
                $listSpecializationUserProfile = Specialization::find()
                    ->where(['user_id' => $User->id])
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
                        $Specialization->user_id = $User->id;
                        $Specialization->save();

                    }

                }

            }

            if ($UserProfile->save()) {
                return [
                    "result" => $User
                ];

            }


        } else {
            throw new NotFoundHttpException('Пользователь не найден');

        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];
    }

    #[OA\Post(
        path: '/functions/update-master-rating',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionUpdateMasterRating()
    {

        $User = UserApp::findOne(Yii::$app->user->id);

        $params = Yii::$app->getRequest()->getBodyParams();

        $newRating = $params['newRating'] ?? null;

        if ($newRating < 1 || $newRating > 5) {
            return [
                "code" => 141,
                "error" => "Оценка должна быть от 1 до 5",
            ];
        }

        if ($User && ($UserProfile = $User->userProfile)) {

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
                    "result" => $User,
                ];
            }

        } else {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return [
            "code" => 141,
            "error" => "Ошибка",
        ];

    }

    #[OA\Post(
        path: '/functions/update-user-profile',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionUpdateUserProfile()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $User = UserApp::findOne(Yii::$app->user->id);
        $UserProfile = $User->userProfile;

        $firstName = $params['firstName'] ?? null;
        $lastName = $params['lastName'] ?? null;
        $city = $params['city'] ?? null;
//        $photoUrl = $params['photoUrl'] ?? null;
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
        $carBrand = $params['carBrand'] ?? null;




        if ($User && $UserProfile) {

            if ($firstName) {
                $UserProfile->firstName = $firstName;
            }

            if ($lastName) {
                $UserProfile->lastName = $lastName;
            }

            if ($city) {
                $UserProfile->city = $city;
            }

            if ($specialization) {
                //TODO: вынести сохранение специализации в отдельное место

                //Удаляем старые специализации
                $listSpecializationUserProfile = Specialization::find()
                    ->where(['user_id' => $User->id])
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
                        $Specialization->user_id = $User->id;
                        $Specialization->save();

                    }

                }

            }

            if ($services) {

                $listServices = Services::find()
                    ->where(['user_id' => $User->id])
                    ->all();

                foreach ($listServices as $Service) {
                    $Service->delete();
                }

                foreach ($services as $service) {

                    if ($Specialization = Specializations::findOne(['name' => $service['category']])) {

                        $Service = new Services();
                        $Service->user_id = $User->id;
                        $Service->specialization_id = $Specialization->id;
                        $Service->name = $service['name'];
                        $Service->description = $service['description'];
                        $Service->price_from = $service['priceFrom'];
                        $Service->price_to = $service['priceTo'] ?? null;
                        $Service->save();

                    }
                }

            }

            if ($cars && is_iterable($cars)) {
                $carsString = serialize($cars);
                $UserProfile->cars = $carsString;
            }

            //TODO: email должен меняться с подтверждением и проверкой на уникальность
            if ($email) {
                $User->email = $email;
            }

            if ($carBrand) {
                $UserProfile->carBrand = $carBrand;
            }

            if ($workShifts) {

                $UserWorkShiftDays = WorkDaysShift::find()->where(['user_id' => $User->id])->all();
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
                        $WorkDaysShift->user_id = $User->id;
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

//            if ($photos && is_iterable($photos)) {
//                $photosString = serialize($photos);
//                $UserProfile->photos = $photosString;
//            }

            if ($companyName) {
                $UserProfile->companyName = $companyName;
            }

            if ($experience) {
                $UserProfile->experience = (integer) $experience;
            }

            //TODO: phone должен меняться с подтверждением и проверкой на уникальность
            if ($phone) {
                $User->phone = $phone;
            }

            if ($customShiftTemplates &&is_iterable($customShiftTemplates)) {

                $newCustomShiftTemplates = [];

                foreach ($customShiftTemplates as $customShiftTemplate) {
                    $customShiftTemplatesParts = explode('-', $customShiftTemplate);

                    $newCustomShiftTemplates[] = $customShiftTemplatesParts[0] . '-' . $customShiftTemplatesParts[1];
                }

                //Удаляем старые специализации
                $listCustomShiftTemplates = CustomShiftTemplates::find()
                    ->where(['user_id' => $User->id])
                    ->all();

                foreach ($listCustomShiftTemplates as $CustomShiftTemplate) {
                    $CustomShiftTemplate->delete();
                }

                foreach ($newCustomShiftTemplates as $CustomShiftTemplate) {
                    $ModelCustomShiftTemplate = new CustomShiftTemplates();
                    $ModelCustomShiftTemplate->user_id = $User->id;
                    $ModelCustomShiftTemplate->template = $CustomShiftTemplate;

                    $ModelCustomShiftTemplate->save();
                }

            }

            if ($workAddress) {
                $UserProfile->workAddress = $workAddress;
            }

            if ($User->save() && $UserProfile->save()) {
                return [
                    "result" => $User,
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];
        }

        var_dump('ddd');

    }

    #[OA\Post(
        path: '/functions/book-slot',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionBookSlot()
    {
        $params = Yii::$app->getRequest()->getBodyParams();

        $Client = UserApp::findOne(Yii::$app->user->id);

        $masterId = $params['masterId'] ?? null;
        $serviceName = $params['serviceName'] ?? null;
        $startTime = $params['startTime'] ?? null;
        $endTime = $params['endTime'] ?? null;


        if (!$masterId || !$Client || !$startTime || !$endTime) {
            return [
                "code" => 141,
                "error" => "Не хватает параметров",
            ];
        }

        $Master = UserApp::findOne($masterId);

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
                'work_days_shift.user_id' => $masterId,
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
                'user_id' => $Master->id,
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

                    // TODO: Пуш уведомление мастеру о новом бронировании


                    return [
                        "result" => [
                            "success" => true,
                            "bookingId" => $Booking->id,
                        ]
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

    #[OA\Post(
        path: '/functions/cancel-booking',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionCancelBooking()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $bookingId = $params['bookingId'] ?? null;
        $Client = Yii::$app->user;

        if ($bookingId && ($Booking = Booking::find()->where(['id' => $bookingId, 'client_id' => $Client->id])->one())) {
            // TODO: Пуш уведомление мастеру о отмене бронирования клиентом

            if ($Booking->delete()) {
                return [
                    "result" => [
                        "success" => true,
                    ]
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Бронирование не найдено",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка обработки данных",
        ];

    }

    #[OA\Post(
        path: '/functions/update-booking-status',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionUpdateBookingStatus()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $bookingId = $params['bookingId'] ?? null;
        $status = $params['status'] ?? null;

        if (!$bookingId || !$status) {
            return [
                "code" => 141,
                "error" => "Не хватает параметров",
            ];
        }

        $Master = Yii::$app->user;

        $Booking = Booking::find()
            ->where([
                'id' => $bookingId,
                'master_id' => $Master->id,
            ])->one();

        if ($Booking) {

            $Booking->status = $status;

            if (!in_array($status, array_keys(Booking::listStatus()))) {
                return [
                    "code" => 141,
                    "error" => "Ошибка, неверный статус",
                ];
            }

            if ($Booking->save()) {

                //TODO: отправить уведомление клиенту

                /*
                  await Parse.Push.send({
                    where: pushQuery,
                    data: {
                      alert: status === "confirmed"
                        ? "Ваша запись подтверждена мастером"
                        : "Ваша запись отклонена мастером",
                      title: "Статус записи",
                      sound: "default"
                    }
                  });
                 */

                return [
                    "result" => [
                        "success" => true,
                    ]
                ];
            }

        } else {
            return [
                "code" => 141,
                "error" => "Бронирование не найдено",
            ];
        }

        return [
            "code" => 141,
            "error" => "Ошибка обработки данных",
        ];

    }

    #[OA\Post(
        path: '/functions/delete-worck-shift',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionDeleteWorkShift()
    {

        $params = Yii::$app->getRequest()->getBodyParams();
        $Master = Yii::$app->user;

        $shiftDate = $params['shiftDate'] ?? null;

        if (!$Master || !$shiftDate) {
            return [
                "code" => 141,
                "error" => "Не хватает параметров: shiftDate обязательны",
            ];
        }

        $DTShiftDate = null;

        try {
            $DTShiftDate = new DateTime($shiftDate);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                "code" => 141,
                "error" => "Неверный формат даты",
            ];

        }

        if ($Master) {

            $workDaysShift = WorkDaysShift::find()
                ->where([
                    'user_id' => $Master->id,
                    'work_days_shift.day' => $DTShiftDate->format('Y-m-d'),
                ])->one();

            $isRealDelete = false;
            if ($workDaysShift) {

                $listWorkTimeShift = $workDaysShift->workTimeShift;

                // TODO:передклат на удаление из модели в before delete

                foreach ($listWorkTimeShift as $workTimeShift) {
                    /** @var WorkTimeShift $workTimeShift */

                    foreach ($workTimeShift->bookings as $Booking) {
                        $Booking->delete();
                    }

                    $workTimeShift->delete();
                }

                if ($workDaysShift->delete()) {
                    $isRealDelete = true;
                }
            }

            $workDaysShift = WorkDaysShiftApp::find()
                ->where([
                    'user_id' => $Master->id,
                ])->all();

            return [
                "result" => [
                    'success' => true,
                    'shiftRemoved' => $isRealDelete,
                    'updatedWorkShifts' => $workDaysShift,
                ],
            ];




        } else {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];
        }
    }

    #[OA\Post(
        path: '/functions/update-master-shedule-and-services',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionUpdateMasterScheduleAndServices()
    {

        $params = Yii::$app->getRequest()->getBodyParams();

        $Master = UserApp::findOne(Yii::$app->user->id);

        $services = $params['services'] ?? null;
        $workShifts = $params['workShifts'] ?? null;
        $customShiftTemplates = $params['customShiftTemplates'] ?? null;

        if (!$Master) {
            return [
                "code" => 141,
                "error" => "Пользователь не найден",
            ];
        }


        if ($services) {

            $listServices = Services::find()
                ->where(['user_id' => $Master->id])
                ->all();

            foreach ($listServices as $Service) {
                $Service->delete();
            }

            foreach ($services as $service) {

                if ($Specialization = Specializations::findOne(['name' => $service['category']])) {

                    $Service = new Services();
                    $Service->user_id = $Master->id;
                    $Service->specialization_id = $Specialization->id;
                    $Service->name = $service['name'];
                    $Service->description = $service['description'];
                    $Service->price_from = $service['priceFrom'];
                    $Service->price_to = $service['priceTo'] ?? null;
                    $Service->save();
                }
            }

        }

        if ($workShifts) {

            $UserWorkShiftDays = WorkDaysShift::find()->where(['user_id' => $Master->id])->all();
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
                    $WorkDaysShift->user_id = $Master->id;
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

        if ($customShiftTemplates &&is_iterable($customShiftTemplates)) {

            $newCustomShiftTemplates = [];

            foreach ($customShiftTemplates as $customShiftTemplate) {
                $customShiftTemplatesParts = explode('-', $customShiftTemplate);

                $newCustomShiftTemplates[] = $customShiftTemplatesParts[0] . '-' . $customShiftTemplatesParts[1];
            }

            //Удаляем старые специализации
            $listCustomShiftTemplates = CustomShiftTemplates::find()
                ->where(['user_id' => $Master->id])
                ->all();

            foreach ($listCustomShiftTemplates as $CustomShiftTemplate) {
                $CustomShiftTemplate->delete();
            }

            foreach ($newCustomShiftTemplates as $CustomShiftTemplate) {
                $ModelCustomShiftTemplate = new CustomShiftTemplates();
                $ModelCustomShiftTemplate->user_id = $Master->id;
                $ModelCustomShiftTemplate->template = $CustomShiftTemplate;

                $ModelCustomShiftTemplate->save();
            }

        }

        return [
            "result" => $Master,
        ];
    }

    #[OA\Post(
        path: '/functions/set-user-photo',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionSetUserPhoto()
    {
        $User = UserApp::findOne(Yii::$app->user->id);

        $photo = UploadedFile::getInstanceByName('photo');

        if ($photo) {

            $File = new File();
            $File->loadFile($photo);
            $File->setType(File::TYPE_AVATAR);
            $File->setSubType(File::SUB_TYPE_AVATAR);
            $File->setUserId($User->id);
            $File->setEntityId($User->id);
            $File->saveFile();

            return [
                "result" => $User,
            ];

        }

        return [
            "code" => 141,
            "error" => "Загрузите фото",
        ];
    }

    #[OA\Post(
        path: '/functions/set-promotion-photo',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionSetPromotionPhoto($promotion_id)
    {
        $User = UserApp::findOne(Yii::$app->user->id);
        $Promotion = PromotionApp::find()
            ->where([
                'id' => $promotion_id,
                'master_id' => $User->id,
            ])->one();

        if (!$Promotion) {
            return [
                "code" => 141,
                "error" => "Промо акция не найдена",
            ];
        }

        $listPhoto = UploadedFile::getInstancesByName('photo');
        $countFilesNeedLoad = count($listPhoto);
        $countFilesLoaded = 0;

        if ($listPhoto) {
            foreach ($listPhoto as $photo) {
                $File = new File();
                $File->loadFile($photo);
                $File->setType(File::TYPE_PROMOTION);
                $File->setSubType(File::SUB_TYPE_PROMOTION);
                $File->setUserId($Promotion->master_id);
                $File->setEntityId($Promotion->id);
                $File->saveFile();
                $countFilesLoaded++;
            }

            if ($countFilesLoaded == 0) {
                return [
                    "code" => 141,
                    "error" => "Ошибка загрузки файлов",
                ];
            }

            if ($countFilesNeedLoad != $countFilesLoaded) {
                return [
                    "result" => $Promotion,
                    "error" => "Не все файлы были загружены",
                ];
            } else {
                return [
                    "result" => $Promotion,
                ];
            }
        }

        return [
            "code" => 141,
            "error" => "Загрузите фото",
        ];
    }

    #[OA\Post(
        path: '/functions/set-service-photo',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionSetServicePhoto($service_id)
    {

        $Service = ServicesApp::findOne($service_id);

        if (!$Service) {
            return [
                "code" => 141,
                "error" => "Услуга не найдена",
            ];
        }

        $listPhoto = UploadedFile::getInstancesByName('photo');

        $countFilesNeedLoad = count($listPhoto);
        $countFilesLoaded = 0;

        if ($listPhoto) {
            foreach ($listPhoto as $photo) {
                $File = new File();
                $File->loadFile($photo);
                $File->setType(File::TYPE_SERVICE);
                $File->setSubType(File::SUB_TYPE_SERVICE);
                $File->setUserId($Service->user_id);
                $File->setEntityId($Service->id);
                $File->saveFile();
                $countFilesLoaded++;
            }

            if ($countFilesLoaded == 0) {
                return [
                    "code" => 141,
                    "error" => "Ошибка загрузки файлов",
                ];
            }

            if ($countFilesNeedLoad != $countFilesLoaded) {
                return [
                    "result" => $Service,
                    "error" => "Не все файлы были загружены",
                ];
            } else {
                return [
                    "result" => $Service,
                ];
            }
        }

        return [
            "code" => 141,
            "error" => "Загрузите фото",
        ];

    }


//    #[OA\Post(
//        path: '/functions/create-yookassa-payment',
//        responses: [
//            new OA\Response(
//                response: 401,
//                description: 'Ошибка авторизации',
//                content: new OA\JsonContent(
//                    ref: '#/components/schemas/UnauthorizedData'
//                )
//            ),
//        ]
//    )]
    public function actionCreateYookassaPayment()
    {

//        $shopId = Yii::$app->params['shopId'];
//        $secretKey = Yii::$app->params['secretKey'];
//
//        $client = new ClientApp();
//        $client->setAuth($shopId, $secretKey);
//        $idempotenceKey = uniqid('', true);
//        $response = $client->createPayment(
//            array(
//                'amount' => array(
//                    'value' => 100000.0,
//                    'currency' => 'RUB',
//                ),
//                'confirmation' => array(
//                    'type' => 'redirect',
//                    'return_url' => 'https://yookassa.ru',
//                ),
//                'capture' => true,
//                'description' => 'Заказ №1', // Или такой текст = Оплата размещения акции
//            ),
//            $idempotenceKey
//        );
//
//        var_dump($response->getId());
//        var_dump($response->getStatus());
//
////
//
//        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
//        var_dump($confirmationUrl);
//        var_dump($response);
//        die;
//
//
//        //получаем confirmationUrl для дальнейшего редиректа
//        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
//
//        var_dump($confirmationUrl);
//
//        die;


        return 'actionCreateYookassaPayment';
    }

    #[OA\Post(
        path: '/functions/check-and-create-promotion',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionCheckAndCreatePromotion()
    {
        return 'actionCheckAndCreatePromotion';
    }

    #[OA\Post(
        path: '/functions/yookassa-webhook',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionYookassaWebhook()
    {
        return 'actionYookassaWebhook';
    }


//    public function actionWorkShifts()
//    {
//        return 'actionWorkShifts';
//    }

    #[OA\Post(
        path: '/functions/delete-photo',
        responses: [
            new OA\Response(
                response: 401,
                description: 'Ошибка авторизации',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedData'
                )
            ),
        ]
    )]
    public function actionDeletePhoto()
    {
        return 'actionDeletePhoto';
    }








}