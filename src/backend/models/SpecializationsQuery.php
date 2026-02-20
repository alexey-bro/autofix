<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Specializations]].
 *
 * @see Specializations
 */
class SpecializationsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Specializations[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Specializations|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
