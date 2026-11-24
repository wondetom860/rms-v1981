<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExInvigilators]].
 *
 * @see ExInvigilators
 */
class ExInvigilatorsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExInvigilators[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExInvigilators|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
