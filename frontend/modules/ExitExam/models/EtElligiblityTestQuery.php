<?php

namespace frontend\modules\ExitExam\models;

/**
 * This is the ActiveQuery class for [[EtElligiblityTest]].
 *
 * @see EtElligiblityTest
 */
class EtElligiblityTestQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EtElligiblityTest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EtElligiblityTest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
