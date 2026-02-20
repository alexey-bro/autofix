<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property string $fullName
 * @property int $role
 * @property int $user_id
 * @property string $phone
 * @property string $carBrand
 * @property string|null $city
 * @property string|null $email
 * @property int $rating
 * @property int $reviewsCount
 * @property int|null $latitude
 * @property int|null $longitude
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
            [['city', 'email', 'latitude', 'longitude', 'workAddress', 'firstName', 'lastName', 'companyName'], 'default', 'value' => null],
            [['experience'], 'default', 'value' => 0],
            [['fullName', 'role', 'user_id', 'phone', 'carBrand'], 'required'],
            [['role', 'user_id', 'rating', 'reviewsCount', 'latitude', 'longitude', 'experience'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['fullName', 'phone', 'carBrand', 'city', 'email', 'workAddress', 'firstName', 'lastName', 'companyName'], 'string', 'max' => 255],
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

    /**
     * {@inheritdoc}
     * @return UserProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProfileQuery(get_called_class());
    }

}
