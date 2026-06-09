<?php

namespace api\modules\v1\models\form;

use common\models\User;
use yii\base\Model;

class VerifyCodeForm extends Model
{
    public string $email;
    public string $code;
    public int $type_user;

    public function rules(): array
    {
        return [
            [['email', 'code', 'type_user'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['email', 'code'], 'trim'],
            [['code'], 'string', 'length' => 4],
            [['code'], 'match', 'pattern' => '/^\d{4}$/'],
            [['type_user'], 'integer'],
            [['type_user'], 'in', 'range' => [User::ROLE_USER_CLIENT, User::ROLE_USER_MASTER]],
        ];
    }

    public function beforeValidate()
    {

        if (is_numeric($this->code)) {
            $this->code = sprintf("%04d", (int) $this->code);
        }

        return parent::beforeValidate();
    }

    public function verifyCode(string $email, string $code, string $type): array
    {
//        $record = EmailVerificationCode::findValid($email, $code, $type);
//
//        if (!$record) {
//            throw new \yii\web\UnprocessableEntityHttpException('Неверный код верификации.');
//        }
//
//        if ($record->isExpired()) {
//            $record->markUsed();
//            throw new \yii\web\UnprocessableEntityHttpException('Срок действия кода истёк.');
//        }
//
//        $record->markUsed();
//
//        $isNew = false;
//        $user  = User::findByEmail($email);
//
//        if (!$user) {
//            // Создаём нового пользователя
//            $user = new User();
//            $user->email  = $email;
//            $user->status = User::STATUS_ACTIVE;
//            $user->generateAuthKey();
//            $user->generateAccessToken();
//
//            if (!$user->save()) {
//                throw new \yii\base\Exception('Не удалось создать пользователя.');
//            }
//            $isNew = true;
//        } else {
//            // Обновляем токен существующего пользователя
//            $user->generateAccessToken();
//            $user->save(false);
//        }
//
//        return [
//            'token'  => $user->access_token,
//            'is_new' => $isNew,
//            'user'   => [
//                'id'    => $user->id,
//                'email' => $user->email,
//                'name'  => $user->name,
//            ],
//        ];
    }

}