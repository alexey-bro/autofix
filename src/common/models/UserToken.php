<?php

namespace common\models;


use Yii;
use yii\db\ActiveRecord;

/**
 * @property int       $id
 * @property int       $user_id
 * @property string    $token
 * @property string    $device_id
 * @property string    $device_token
 * @property int       $device_type
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

    //устройство с андроидом
    const DEVICE_TYPE_ANDROID = '1';

    //устройство с ios
    const DEVICE_TYPE_IOS = '2';

    public static function tableName(): string
    {
        return '{{%user_tokens}}';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'token', 'device_type'], 'required'],
            [['user_id', 'device_type'], 'integer'],
            [['token'], 'string', 'max' => 64],
            [['device_id', 'device_token'], 'string', 'max' => 255],
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

        $token = Yii::$app->security->generateRandomString(32);
        $model = new self();
        $model->user_id = $userId;
        $model->token = $token;
        $model->device_id = $deviceId;
//        $model->device_token = null;
        $model->device_type = self::DEVICE_TYPE_ANDROID;
//        $model->created_at = time();
        $model->expired_at      = $DTNow->modify('+' . self::TOKEN_EXPIRE . ' seconds')->format('Y-m-d H:i:s');

        if (!$model->save()) {
            throw new \RuntimeException('Не удалось сохранить токен');
        }

        return $model;
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        // очищаем другие токены, если есть совпадения по device_token
        if (
            $this->device_token &&
            ($changedAttributes &&
                (
                    array_key_exists('device_token', $changedAttributes) &&
                    $changedAttributes['device_token'] !== $this->device_token
                )
            )
        ) {
            $tokens = self::find()->where(['!=', 'id', $this->id])
                ->andWhere(['device_token' => $this->device_token])
                ->all();

            if ($tokens) {
                foreach ($tokens as $token) {
                    /** @var $token UserToken */
                    $token->delete();
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
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