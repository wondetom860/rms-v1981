<?php

namespace frontend\modules\AcStEva\models;

/**
 * This is the ActiveQuery class for [[AseEvaluationResultDetail]].
 *
 * @see AseEvaluationResultDetail
 */
class AseEvaluationResultDetailQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AseEvaluationResultDetail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AseEvaluationResultDetail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
