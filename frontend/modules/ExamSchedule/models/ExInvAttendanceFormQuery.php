<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExInvAttendanceForm]].
 *
 * @see ExInvAttendanceForm
 */
class ExInvAttendanceFormQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExInvAttendanceForm[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExInvAttendanceForm|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
