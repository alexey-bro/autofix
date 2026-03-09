<?php

namespace common\models;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property string|null $_user_id
 * @property string|null $fullName
 * @property int $role
 * @property int|null $user_id
 * @property string $phone
 * @property string|null $carBrand
 * @property string|null $city
 * @property string|null $email
 * @property int $rating
 * @property int $reviewsCount
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $workAddress
 * @property string|null $firstName
 * @property string|null $lastName
 * @property int|null $experience
 * @property string|null $companyName
 * @property string $created_at
 * @property string $updated_at
 */
class UserProfile extends \yii\db\ActiveRecord
{

    public const ROLE_USER_CLIENT = 1;
    public const ROLE_USER_MASTER = 2;

    public static function listRoles()
    {
        return [
            self::ROLE_USER_CLIENT => 'CLIENT',
            self::ROLE_USER_MASTER => 'MASTER',
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullName', 'user_id', 'carBrand', 'city', 'email', 'latitude', 'longitude', 'workAddress', 'firstName', 'lastName', 'companyName'], 'default', 'value' => null],
            [['experience', 'rating'], 'default', 'value' => 0],
            [['role', 'user_id', 'rating', 'reviewsCount', 'experience'], 'integer'],
            [['phone'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['fullName', 'phone', 'carBrand', 'city', 'email', 'latitude', 'longitude', 'workAddress', 'firstName', 'lastName', 'companyName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullName' => 'Full Name',
            'role' => 'Role',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'carBrand' => 'Car Brand',
            'city' => 'City',
            'email' => 'Email',
            'rating' => 'Rating',
            'reviewsCount' => 'Reviews Count',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'workAddress' => 'Work Address',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'experience' => 'Experience',
            'companyName' => 'Company Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getSpecializations()
//    {
//        return $this->hasOne(Specializations::class, ['id' => 'user_profile_id']);
//    }

    /**
     * Основная связь: пользователь имеет много категорий через промежуточную таблицу
     */
    public function getSpecializations()
    {
        return $this->hasMany(Specializations::class, ['id' => 'specialization_id'])
            ->viaTable('specialization', ['user_profile_id' => 'id']);
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
        return $this->hasMany(Specialization::class, ['user_profile_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new query\UserProfileQuery(get_called_class());
    }

}
