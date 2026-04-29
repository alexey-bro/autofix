<?php

namespace api\modules\v1\models\form;

use common\models\User;
use yii\base\Model;

class RegisterForm extends Model
{
    public string $username = '';
    public string $password = '';
    public string $email    = '';

    public function rules(): array
    {
        return [
            [['username', 'password', 'email'], 'required'],

            // username
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/',
                'message' => 'Имя пользователя может содержать только латинские буквы, цифры, _ и -'],
            ['username', 'unique', 'targetClass' => User::class,
                'message' => 'Это имя пользователя уже занято'],

            // password
            ['password', 'string', 'min' => 6, 'max' => 72],

            // email
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class,
                'message' => 'Этот email уже зарегистрирован'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'email'    => 'Email',
        ];
    }

    /**
     * Создаёт пользователя после успешной валидации
     */
    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user                  = new User();
        $user->username        = $this->username;
        $user->email           = $this->email;
        $user->status          = User::STATUS_ACTIVE;
        $user->created_at      = time();
        $user->updated_at      = time();
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save(false) ? $user : null;
    }
}