<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExDateTimeSchedule]].
 *
 * @see ExDateTimeSchedule
 */
class ExDateTimeScheduleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExDateTimeSchedule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExDateTimeSchedule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
