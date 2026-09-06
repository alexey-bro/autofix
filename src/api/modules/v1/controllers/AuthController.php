<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\form\CheckEmailForm;
use api\modules\v1\models\form\RegisterForm;
use api\modules\v1\models\form\SignupForm;
use api\modules\v1\models\form\VerifyCodeForm;
use api\modules\v1\models\UserApp;
use common\models\AuthCode;
use common\models\CustomShiftTemplates;
use common\models\Services;
use common\models\Specialization;
use common\models\Specializations;
use common\models\User;
use common\models\UserToken;
use common\models\WorkDaysShift;
use common\models\WorkTimeShift;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class'  => HttpBearerAuth::class,
            'except' => [
                'check-email',
                'send-code',
                'verify-code',
                'resend-code',
            ], // эти экшены открытые
        ];

        return $behaviors;
    }


    #[OA\Post(
        path: '/auth/check-email',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            description: 'User email',
            required: true,
            ref: '#/components/requestBodies/email'
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "exists",
                            type: 'bool',
                            example: true
                        ),
                        new OA\Property(
                            property: "message",
                            description: "Сообщение о том, найден пользователь или нет",
                            type: "string",
                            example: "Пользователь найден. Выполните вход.",
                            nullable: false
                        )
                    ],
                    examples: [
                        new OA\Examples(
                            example: 'user_found',
                            summary: 'Пользователь найден',
                            value: ['exists' => true, 'message' => 'Пользователь найден. Выполните вход.']
                        ),
                        new OA\Examples(
                            example: 'user_not_found',
                            summary: 'Пользователь не найден',
                            value: ['exists' => false, 'message' => 'Пользователь не найден. Выполните регистрацию.']
                        ),
                    ],

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
    public function actionCheckEmail(): array
    {
        $form = new CheckEmailForm();
        $form->load(Yii::$app->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $this->validationError($form);
        }

        $User = User::findOne(['email' => $form->email]);
        $exists = $User !== null;

        return [
            'exists'  => $exists,
            'message' => $exists
                ? 'Пользователь найден. Выполните вход.'
                : 'Пользователь не найден. Выполните регистрацию.',
        ];

    }


    #[OA\Post(
        path: '/auth/send-code',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            description: 'User email',
            required: true,
            ref: '#/components/requestBodies/email'
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Успешный ответ',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: 'bool',
                            example: true
                        ),
                    ],
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
    public function actionSendCode(): array
    {
        $form = new CheckEmailForm();
        $form->load(Yii::$app->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $this->validationError($form);
        }

        // Cooldown: нельзя запрашивать новый код раньше чем через 60 сек
        $cooldown = AuthCode::getCooldownSeconds($form->email);

        if ($cooldown > 0) {
            return [
                'success' => false,
                'message' => "Повторный запрос кода возможен через {$cooldown} сек.",
            ];

//            $this->addError('email', "Повторный запрос кода возможен через {$cooldown} сек.");
//            return null;
        }

        $User = User::findOne(['email' => $form->email]);

        // Определяем тип: логин или регистрация
        $typeAuth = $User ? AuthCode::TYPE_LOGIN : AuthCode::TYPE_REGISTER;

        // Генерируем код
        $authCode = AuthCode::generate($form->email, $typeAuth, $form->ip);

        // Отправляем письмо
        $view = $typeAuth === AuthCode::TYPE_LOGIN ? 'authCode' : 'verifyCode';
        Yii::$app->mailer
            ->compose(
                ['html' => "{$view}-html", 'text' => "{$view}-text"],
                ['code' => $authCode->code, 'user' => $User]
            )
            ->setFrom('info@findmymechanic.ru')
            ->setTo($form->email)
            ->setSubject($typeAuth === AuthCode::TYPE_LOGIN ? 'Код входа' : 'Подтверждение email')
            ->send();

        return [
            'success' => true,
        ];

    }

    #[OA\Post(
        path: '/auth/verify-code',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            description: 'Data for auth user',
            required: true,
            ref: '#/components/requestBodies/VerifyCode'
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
    public function actionVerifyCode(): array
    {

        $form = new VerifyCodeForm();

        $params = Yii::$app->request->getBodyParams();
        $form->load($params, '');

        if (!$form->validate()) {
            return $this->validationError($form);
        }

        $firstName = $params['firstName'] ?? null;
        $lastName = $params['lastName'] ?? null;
        $city = $params['city'] ?? null;
        $specialization = $params['specialization'] ?? null;
        $services = $params['services'] ?? null;
        $cars = $params['cars'] ?? null;
        $workShifts = $params['workShifts'] ?? null;
        $companyName = $params['companyName'] ?? null;
        $experience = $params['experience'] ?? null;
        $phone = $params['phone'] ?? null;
        $workAddress = $params['workAddress'] ?? null;
        $carBrand = $params['carBrand'] ?? null;
        $latitude = $params['latitude'] ?? null;
        $longitude = $params['longitude'] ?? null;

        $User = User::findOne(['email' => $form->email]);
        // Определяем тип: логин или регистрация
        $typeAuth = $User ? AuthCode::TYPE_LOGIN : AuthCode::TYPE_REGISTER;

        $AuthCode = AuthCode::findActiveByEmail($form->email);

        if (!$AuthCode) {
            return [
                'success' => false,
                'error' => "Код не найден",
            ];
        }

        if ($AuthCode->code !== $form->code) {
            return [
                'success' => false,
                'error' => "Ошибочный код",
            ];
        }

        if ($AuthCode->type == AuthCode::TYPE_REGISTER) {

            $User = new User();
            $User->email  = $form->email;
            $User->status = User::STATUS_ACTIVE;
            $User->setPassword(Yii::$app->security->generateRandomString(32));
            $User->generateAuthKey();
            $User->role = $form->type_user;

            if ($User->save()) {
                $UserProfile = $User->userProfile;

                if ($firstName) {
                    $UserProfile->firstName = $firstName;
                }

                if ($lastName) {
                    $UserProfile->lastName = $lastName;
                }

                if ($latitude) {
                    $UserProfile->latitude = $latitude;
                }

                if ($longitude) {
                    $UserProfile->longitude = $longitude;
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

                if ($carBrand) {
                    $UserProfile->carBrand = $carBrand;
                }

                if ($workShifts) {

                    $UserWorkShiftDays = WorkDaysShift::find()->where(['user_id' => $User->id])->all();

                    $UserWorkShiftTimes = WorkTimeShift::find()
                        ->select([
                            'day',
                            'start',
                            'stop',
                        ])
                        ->leftJoin('work_days_shift', 'work_days_shift.id = work_time_shift.work_days_shift_id')
                        ->where(['user_id' => $User->id])
                        ->asArray()
                        ->all();

                    $groupedDays = [];
                    foreach ($UserWorkShiftTimes as $item) {
                        $day = $item['day'];

                        unset($item['day']);

                        if (!isset($groupedDays[$day])) {
                            $groupedDays[$day] = [];
                        }

                        $groupedDays[$day][] = $item;
                    }

//              Та же группировка, но в другом стиле
//                $grouped = array_reduce($UserWorkShiftTimes, function($carry, $item) {
//                    $carry[$item['day']][] = $item;
//                    return $carry;
//                }, []);

                    foreach ($workShifts as $workShift) {

                        $DTWorkShift = new DateTime($workShift['date']);

                        if (isset($workShift['slots'])) {

                            $day = $DTWorkShift->format('Y-m-d');

                            $WorkDaysShift = null;

                            if (isset($groupedDays[$day])) {
                                $WorkDaysShift = WorkDaysShift::find()->where([
                                    'user_id' => $User->id,
                                    'day' => $day
                                ])->one();
                            }

                            if (!$WorkDaysShift) {
                                $WorkDaysShift = new WorkDaysShift();
                                $WorkDaysShift->user_id = $User->id;
                                $WorkDaysShift->day = $DTWorkShift->format('Y-m-d');
                            }

                            if ($WorkDaysShift->save()) {
                                foreach ($workShift['slots'] as $slot) {

                                    $start = (new DateTime($slot['startTime']))->format('H:i:s');
                                    $stop = (new DateTime($slot['endTime']))->format('H:i:s');

                                    if (isset($groupedDays[$day])) {
                                        $found = false;
                                        foreach ($groupedDays[$day] as $shift) {
                                            if ($shift['start'] === $start && $shift['stop'] === $stop) {
                                                $found = true;
                                                break;
                                            }
                                        }

                                        if ($found) {
                                            continue;
                                        }
                                    }

                                    $WorkTimeShift = new WorkTimeShift();
                                    $WorkTimeShift->work_days_shift_id = $WorkDaysShift->id;
                                    $WorkTimeShift->start = $start;
                                    $WorkTimeShift->stop = $stop;
                                    $WorkTimeShift->isAvailable = (int) $slot['isAvailable'];
                                    $WorkTimeShift->save();

                                }
                            }

                        }

                    }

                }

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

                if ($workAddress) {
                    $UserProfile->workAddress = $workAddress;
                }

                $User->save();
                $UserProfile->save();

            }

        }

        $AuthCode->markUsed();

        $UserApp = UserApp::findOne(['id' => $User->id]);

        // Удаляем просроченные токены
        UserToken::deleteExpired($UserApp->id);

        // Генерируем новый токен
        $UserToken = UserToken::generate($UserApp->id);

        if ($UserApp && $UserToken) {
            return [
                'result' => $UserApp,
                'auth' => [
                    'token'      => $UserToken->token,
                    'expired_at' => $UserToken->expired_at,
                    'user'       => [
                        'id'       => $UserApp->id,
                        'username' => $UserApp->username,
                        'email'    => $UserApp->email,
                    ],
                ],
            ];

        } else {
            throw new UnauthorizedHttpException('Ошибка авторизации');
        }

    }

    public function actionResendCode(): array
    {

    }

    private function validationError($form): array
    {
        Yii::$app->response->statusCode = 422;
        return ['errors' => $form->errors];
    }


    /**
     * POST /auth/register
     * Body: { "username": "...", "password": "...", "email": "..." }
     */
    public function actionRegister(): array
    {
        $form = new RegisterForm();
        $form->load(Yii::$app->request->getBodyParams(), '');

        $user = $form->register();

        if (!$user) {
            // Возвращаем ошибки валидации
            return [
                'success' => false,
                'errors'  => $form->getErrors(),
            ];
        }

        // Сразу выдаём токен — пользователь залогинен после регистрации
        $userToken = UserToken::generate($user->id);

        Yii::$app->response->statusCode = 201; // Created

        return [
            'success'    => true,
            'token'      => $userToken->token,
            'expired_at' => $userToken->expired_at,
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ];
    }

    /**
     * POST /auth/login
     * Body: { "username": "...", "password": "...", "device_id": "..." }
     */
    public function actionSignup(): array
    {

//        $body     = Yii::$app->request->getBodyParams();
//        $username = $body['username'] ?? '';
//        $password = $body['password'] ?? '';
//        $email = $body['email'] ?? '';
//
//        var_dump('ddd');
//        die;

        $model = new SignupForm();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        var_dump($model);
        die;


        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }



        return $this->render('signup', [
            'model' => $model,
        ]);


        if (!$username || !$password || !$email) {
            return [
                "code" => 141,
                "error" => "Имя пользователя не должно быть пустым",
            ];
        }

        $User = User::findOne(['username' => $username]);

        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Неверный логин или пароль');
        }
        var_dump('ddd');
        die;


        // Удаляем просроченные токены
        UserToken::deleteExpired($user->id);

        // Генерируем новый токен
        $userToken = UserToken::generate($user->id, $deviceId);

        return [
            'token'      => $userToken->token,
            'expired_at' => $userToken->expired_at,
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ];
    }

    /**
     * POST /auth/login
     * Body: { "username": "...", "password": "...", "device_id": "..." }
     */
    public function actionLogin(): array
    {

        $body     = Yii::$app->request->getBodyParams();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';
        $deviceId = $body['device_id'] ?? null;

        $user = User::findOne(['username' => $username]);

        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Неверный логин или пароль');
        }

        // Удаляем просроченные токены
        UserToken::deleteExpired($user->id);

        // Генерируем новый токен
        $userToken = UserToken::generate($user->id, $deviceId);

        return [
            'token'      => $userToken->token,
            'expired_at' => $userToken->expired_at,
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ];
    }

    /**
     * POST /auth/logout
     * Header: Authorization: Bearer <token>
     */
    public function actionLogout(): array
    {
        $token = $this->getBearerToken();

        UserToken::deleteAll(['token' => $token]);

        return ['message' => 'Выход выполнен успешно'];
    }

    /**
     * POST /auth/logout-all — выход со всех устройств
     */
    public function actionLogoutAll(): array
    {
        $userId = Yii::$app->user->id;

        UserToken::deleteAll(['user_id' => $userId]);

        return ['message' => 'Выход со всех устройств выполнен'];
    }

    /**
     * GET /auth/me — информация о текущем пользователе
     */
    public function actionMe(): array
    {
        $user = Yii::$app->user->identity;

        return [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
        ];
    }

    private function getBearerToken(): string
    {
        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            throw new BadRequestHttpException('Токен не найден');
        }

        return $matches[1];
    }

}