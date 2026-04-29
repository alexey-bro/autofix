<?php

namespace common\models;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property string|null $_user_id
 * @property string|null $fullName
 * @property int $user_id
 * @property string|null $carBrand
 * @property string|null $city
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
 *
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
            [['fullName', 'carBrand', 'city', 'latitude', 'longitude', 'workAddress', 'firstName', 'lastName', 'companyName'], 'default', 'value' => null],
            [['experience', 'rating'], 'default', 'value' => 0],
            [['user_id', 'rating', 'reviewsCount', 'experience'], 'integer'],
            [['user_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['fullName', 'carBrand', 'city', 'latitude', 'longitude', 'workAddress', 'firstName', 'lastName', 'companyName'], 'string', 'max' => 255],
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
            'user_id' => 'User ID',
            'carBrand' => 'Car Brand',
            'city' => 'City',
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


    // Транзитная связь к work_days_shift (через work_time_shift)
//    public function getWorkDaysShift(): \yii\db\ActiveQuery
//    {
//        return $this->hasOne(WorkDaysShift::class, ['id' => 'work_days_shift_id'])
//            ->via('workTimeShift');
//    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['user_id' => 'id']);
    }


//    public function getWorkTimeShift(): \yii\db\ActiveQuery
//    {
//        return $this->hasOne(WorkTimeShift::class, ['id' => 'work_time_shift_id']);
//    }
//
//    // Транзитная связь к work_days_shift (через work_time_shift)
//    public function getWorkDaysShift(): \yii\db\ActiveQuery
//    {
//        return $this->hasOne(WorkDaysShift::class, ['id' => 'work_days_shift_id'])
//            ->via('workTimeShift');
//    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new query\UserProfileQuery(get_called_class());
    }

}
