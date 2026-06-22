<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string|null $email
 * @property string $phone
 * @property int $role
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 *
 * @property WorkDaysShift $workDaysShift
 * @property WorkDaysShift $workDaysShifts
 * @property WorkTimeShift $workTimeShifts
 * @property Services $services
 * @property Review $reviewClient
 * @property Review $reviewMaster
 * @property Booking $bookingClient
 * @property Booking $bookingMaster
 * @property Promotion $promotion
 * @property UserProfile $userProfile
 * @property File $photo
 */
class User extends ActiveRecord implements IdentityInterface
{

    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;


    public const ROLE_USER_CLIENT = 1;
    public const ROLE_USER_MASTER = 2;
    public const ROLE_USER_ADMIN = 9;

    public static function listRoles()
    {
        return [
            self::ROLE_USER_CLIENT => 'CLIENT',
            self::ROLE_USER_MASTER => 'MASTER',
            self::ROLE_USER_ADMIN => 'ADMIN',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'unique',
                'targetClass' => User::class,
                'message' => 'Этот адрес электронной почты уже занят',
                'filter' => function ($query) {
                    if (!$this->isNewRecord) {
                        $query->andWhere(['<>', 'id', $this->id]);
                    }
                }
            ],
            [['username', 'email'], 'trim'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            ['role', 'in', 'range' => function() {
                return array_keys(User::listRoles());
            }],
            [['role'], 'integer'],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Custom logic: e.g., format a date or normalize a string
            if (!$this->username) {
                $this->username = 'user_' . time();
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

//    /**
//     * {@inheritdoc}
//     */
//    public static function findIdentityByAccessToken($token, $type = null)
//    {
//        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
//    }

    /**
     * Ищем пользователя по Bearer-токену через таблицу user_tokens
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {


        $userToken = UserToken::findValid($token);

        if (!$userToken) {
            return null;
        }

        // Опционально: продлеваем токен при каждом запросе
        // $userToken->refresh();

        return static::findOne(['id' => $userToken->user_id, 'status' => self::STATUS_ACTIVE]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // Код только при создании нового пользователя

//            Yii::info("Создан новый пользователь: {$this->id}");
            $UserProfile = new UserProfile();
            $UserProfile->user_id = $this->id;
            $UserProfile->save();

            // Например: отправка письма, создание профиля, логирование
            // Profile::create(['user_id' => $this->id]);
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }


    /**
     * Альтернативный вариант с via()
     */
    public function getSpecializationsVia()
    {
        return $this->hasMany(Specializations::class, ['id' => 'specialization_id'])
            ->via('specializationsRecords');
    }

    /**
     * Прямая связь с промежуточной таблицей
     */
    public function getSpecializationsRecords()
    {
        return $this->hasMany(Specialization::class, ['user_id' => 'id']);
    }


    public function getWorkDaysShift()
    {
        return $this->hasMany(WorkDaysShift::class, ['user_id' => 'id']);
    }

    /**
     * Связь с рабочими днями
     */
    public function getWorkDaysShifts()
    {
        return $this->hasMany(WorkDaysShift::class, ['user_id' => 'id']);
    }

    public function getServices()
    {
        return $this->hasMany(Services::class, ['user_id' => 'id']);
    }

    public function getReviewClient()
    {
        return $this->hasMany(Review::class, ['id' => 'client_id']);
    }

    public function getReviewMaster()
    {
        return $this->hasMany(Review::class, ['id' => 'master_id']);
    }

    public function getBookingClient()
    {
        return $this->hasMany(Booking::class, ['id' => 'master_id']);
    }

    public function getBookingMaster()
    {
        return $this->hasMany(Booking::class, ['id' => 'client_id']);
    }

    public function getPromotion()
    {
        return $this->hasMany(Promotion::class, ['id' => 'master_id']);
    }

    public function getCustomShiftTemplates()
    {
        return $this->hasMany(CustomShiftTemplates::class, ['user_id' => 'id']);
    }

    public function getUserToken()
    {
        return $this->hasMany(UserToken::class, ['user_id' => 'id']);
    }

    /**
     * Основная связь: пользователь имеет много категорий через промежуточную таблицу
     */
    public function getSpecializations()
    {
        return $this->hasMany(Specializations::class, ['id' => 'specialization_id'])
            ->viaTable('specialization', ['user_id' => 'id']);
    }

    /**
     * Связь со временем работы через рабочие дни
     */
    public function getWorkTimeShifts()
    {
        return $this->hasMany(WorkTimeShift::class, ['work_days_shift_id' => 'id'])
            ->via('workDaysShifts');
    }

    /**
     * Получить слот по дате и времени
     */
    public function getWorkTimeShiftByDateTime(\DateTime $DTStart, \DateTime $DTStop): null|WorkTimeShift
    {
        $date = $DTStart->format('Y-m-d');
        $startTime = $DTStart->format('H:i:s');
        $stopTime = $DTStop->format('H:i:s');

        return $this->getWorkTimeShifts()
            ->joinWith('workDaysShift')
            ->andWhere(['work_days_shift.day' => $date])
            ->andWhere(['work_time_shift.start' => $startTime])
            ->andWhere(['work_time_shift.stop' => $stopTime])
            ->one();
    }

    public function getPhoto() : \yii\db\ActiveQuery
    {
        return $this->hasOne(File::class, ['entity_id' => 'id'])
            ->onCondition([
                'files.type' => File::TYPE_AVATAR,
                'files.sub_type' => File::SUB_TYPE_AVATAR
            ]);
    }
}
