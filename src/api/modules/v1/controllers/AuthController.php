<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\form\RegisterForm;
use api\modules\v1\models\form\SignupForm;
use common\models\User;
use common\models\UserToken;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class'  => HttpBearerAuth::class,
            'except' => ['login', 'register', 'signup'], // эти экшены открытые
        ];

        return $behaviors;
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