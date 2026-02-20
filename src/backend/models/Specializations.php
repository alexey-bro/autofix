<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "specializations".
 *
 * @property int $id
 * @property string $name
 * @property int $is_deleted
 * @property string|null $description
 * @property string $created_at
 * @property string $updated_at
 */
class Specializations extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specializations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['name'], 'required'],
            [['is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return SpecializationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SpecializationsQuery(get_called_class());
    }

}
