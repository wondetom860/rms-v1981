<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExExaminees]].
 *
 * @see ExExaminees
 */
class ExExamineesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExExaminees[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExExaminees|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
