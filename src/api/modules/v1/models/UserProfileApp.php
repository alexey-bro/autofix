<?php

namespace api\modules\v1\models;

use common\models\UserProfile;

class UserProfileApp extends UserProfile
{

    private $_specializationsString = [];

    public function init()
    {
        parent::init();
    }

    /**
     * Виртуальное свойство для получения категорий строкой
     */
    public function getSpecializationsAsString()
    {
        if (!$this->_specializationsString) {
            $names = $this->getSpecializations()
                ->select('name')
                ->column();
            $this->_specializationsString = implode(', ', $names);
        }
        return $this->_specializationsString;
    }

    /**
     * Для установки значения (если нужно)
     */
    public function setSpecializationsAsString($value)
    {
        $this->_specializationsString = $value;
    }

    /**
     * Добавляем в fields для API
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields['specializations'] = 'specializationsAsString';
        return $fields;
    }

    /**
     * Добавляем в extraFields для API
     */
    public function extraFields()
    {
        return ['specializations'];
    }

}