<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "custom_shift_templates".
 *
 * @property int $id
 * @property int $user_id
 * @property string $template
 * @property string $created_at
 * @property string $updated_at
 */
class CustomShiftTemplates extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'custom_shift_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'template'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['template'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'template' => 'Template',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CustomShiftTemplatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CustomShiftTemplatesQuery(get_called_class());
    }

}
