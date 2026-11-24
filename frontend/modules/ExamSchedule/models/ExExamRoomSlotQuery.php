<?php

namespace frontend\modules\ExamSchedule\models;

/**
 * This is the ActiveQuery class for [[ExExamRoomSlot]].
 *
 * @see ExExamRoomSlot
 */
class ExExamRoomSlotQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExExamRoomSlot[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExExamRoomSlot|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
