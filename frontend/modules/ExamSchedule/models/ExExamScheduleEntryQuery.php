<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExExamScheduleEntry]].
 *
 * @see ExExamScheduleEntry
 */
class ExExamScheduleEntryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExExamScheduleEntry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExExamScheduleEntry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
