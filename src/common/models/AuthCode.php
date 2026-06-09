<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * @property int    $id
 * @property string $email
 * @property string $code
 * @property string $type        'login' | 'register'
 * @property int    $attempts    сколько раз пробовали ввести код
 * @property bool   $is_used
 * @property string $ip
 * @property int    $created_at
 * @property int    $expired_at
 */
class AuthCode extends ActiveRecord
{

    const TYPE_LOGIN    = 1; //код для авторизации пользователя
    const TYPE_REGISTER = 2; //код для регистрации пользователя

    /** Время жизни кода — 10 минут */
    const TTL_SECONDS = 600;

    /** Минимальная пауза между запросами нового кода — 60 секунд */
    const RESEND_COOLDOWN = 60; //60 sec

    /** Максимум попыток ввода кода */
    const MAX_ATTEMPTS = 5;

    public static function tableName(): string
    {
        return '{{%auth_codes}}';
    }

    /**
     * Генерирует и сохраняет новый код.
     * Инвалидирует старые активные коды для этого email.
     */
    public static function generate(string $email, string $type, string $ip): self
    {
        // Инвалидируем все старые активные коды для этого email
        static::updateAll(['is_used' => true], ['email' => $email, 'is_used' => false]);

        $NowDT   = new \DateTime();

        $model = new static();
        $model->email      = $email;
        $model->code       = sprintf('%04d', random_int(0, 9999));
        $model->type       = $type;
        $model->attempts   = 0;
        $model->is_used    = false;
        $model->ip         = $ip;
        $model->expired_at = $NowDT->modify(("+".static::TTL_SECONDS." seconds"))->format('Y-m-d H:i:s');
        $model->save(false);

        return $model;
    }

    /**
     * Возвращает количество секунд до следующего разрешённого запроса (0 = можно).
     */
    public static function getCooldownSeconds(string $email): int
    {

        $last = static::find()
            ->where(['email' => $email])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$last) {
            return 0;
        }

        $DTNow = new \DateTime();
        $LastCodeDT = new \DateTime($last->created_at);

//        var_dump($DTNow);
//        var_dump($LastCodeDT);
//        die;
//
//
//        var_dump($DTNow->getTimestamp() - $LastCodeDT->getTimestamp());
//        die;


        $remaining = static::RESEND_COOLDOWN - ($DTNow->getTimestamp() - $LastCodeDT->getTimestamp());
        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Ищет актуальный (не использованный, не просроченный) код по email.
     */
    public static function findActiveByEmail(string $email): ?self
    {
        $NowDT   = new \DateTime();

        return static::find()
            ->where(['email' => $email, 'is_used' => false])
            ->andWhere(['>', 'expired_at', $NowDT->format('Y-m-d H:i:s')])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }

    /** Помечает код как использованный */
    public function markUsed(): void
    {
        $this->is_used = true;
        $this->save(false);
    }

    /**
     * Увеличивает счётчик попыток.
     * Возвращает true если лимит превышен.
     */
    public function incrementAttempts(): bool
    {
        $this->updateCounters(['attempts' => 1]);
        return $this->attempts >= static::MAX_ATTEMPTS;
    }


    public static function getIp(): string
    {
        // Учитываем прокси (nginx X-Real-IP)
        $ip = Yii::$app->request->headers->get('X-Real-IP')
            ?? Yii::$app->request->headers->get('X-Forwarded-For')
            ?? Yii::$app->request->userIP
            ?? '0.0.0.0';

        // Берём первый IP если X-Forwarded-For содержит цепочку
        return trim(explode(',', $ip)[0]);
    }
}