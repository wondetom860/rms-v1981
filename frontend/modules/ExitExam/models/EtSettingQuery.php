<?php

namespace frontend\modules\ExitExam\models;

/**
 * This is the ActiveQuery class for [[EtSetting]].
 *
 * @see EtSetting
 */
class EtSettingQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EtSetting[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EtSetting|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
