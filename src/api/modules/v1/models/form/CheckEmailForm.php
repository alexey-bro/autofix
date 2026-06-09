<?php

namespace api\modules\v1\models\form;

use api\modules\v1\controllers\AuthController;
use common\models\AuthCode;
use common\models\User;
use Yii;
use yii\base\Model;

class CheckEmailForm extends Model
{
    public ?string $email = null;
    public string  $ip = '';

    public function rules(): array
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
//            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот адрес электронной почты уже занят'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'trim'],
        ];
    }


    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Custom logic: e.g., format a date or normalize a string

            if (!$this->ip) {
                $this->ip = AuthCode::getIp();
            }
            return true;
        }
        return false;
    }

}