<?php

namespace common\models;


use Yii;
use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $token
 * @property string $device_id
 * @property string    $expired_at
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property User   $user
 */
class UserToken extends ActiveRecord
{

    // Время жизни токена — 30 дней
    const TOKEN_EXPIRE = 30 * 24 * 3600;

    public static function tableName(): string
    {
        return '{{%user_tokens}}';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'token'], 'required'],
            [['user_id'], 'integer'],
            [['token'], 'string', 'max' => 64],
            [['device_id'], 'string', 'max' => 255],
            [['token'], 'unique'],
            [['created_at', 'updated_at', 'expired_at'], 'safe'],
        ];
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * Генерация нового токена для пользователя
     */
    public static function generate(int $userId, ?string $deviceId = null): self
    {

        $DTNow = new \DateTime();

        $token             = Yii::$app->security->generateRandomString(32);
        $model             = new self();
        $model->user_id    = $userId;
        $model->token      = $token;
        $model->device_id  = $deviceId;
//        $model->created_at = time();
        $model->expired_at = $DTNow->modify('+' . self::TOKEN_EXPIRE . ' seconds')->format('Y-m-d H:i:s');

        if (!$model->save()) {
            throw new \RuntimeException('Не удалось сохранить токен');
        }

        return $model;
    }

    /**
     * Поиск валидного токена
     */
    public static function findValid(string $token): ?self
    {
        return static::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'expired_at', time()])
            ->one();
    }

    /**
     * Продление токена при каждом запросе (опционально)
     */
    public function refresh(): void
    {
        $this->expired_at = time() + self::TOKEN_EXPIRE;
        $this->save(false);
    }

    /**
     * Удаление устаревших токенов пользователя
     */
    public static function deleteExpired(int $userId): void
    {
        $DTNow = new \DateTime();

        static::deleteAll([
            'and',
            ['user_id' => $userId],
            ['<', 'expired_at', $DTNow->format('Y-m-d H:i:s')],
        ]);
    }

}