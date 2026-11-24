<?php

namespace frontend\modules\AcStEva\models;

use backend\models\User;
use Yii;

/**
 * This is the model class for table "ase_evaluation_result_detail".
 *
 * @property int $id
 * @property int $evaluation_result_relId
 * @property int $evaluee_relId
 * @property int $criteria_relId
 * @property string $ratings
 * @property float $average
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string|null $data
 */
class AseEvaluationResultDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_evaluation_result_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['evaluation_result_relId', 'evaluee_relId', 'criteria_relId', 'ratings', 'average', 'created_by', 'created_at'], 'required'],
            [['evaluation_result_relId', 'evaluee_relId', 'criteria_relId', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['ratings', 'data'], 'string'],
            [['average'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'evaluation_result_relId' => Yii::t('rms', 'Evaluation Result Rel ID'),
            'evaluee_relId' => Yii::t('rms', 'Evaluee Rel ID'),
            'criteria_relId' => Yii::t('rms', 'Criteria Rel ID'),
            'ratings' => Yii::t('rms', 'Ratings'),
            'average' => Yii::t('rms', 'Average'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
          $this->data = md5(serialize((object)[
            'evaluation_result_relId' => $this->evaluation_result_relId,
            'evaluee_relId' => $this->evaluee_relId,
            'criteria_relId' => $this->criteria_relId,
            'ratings' => $this->ratings,
            'average' => $this->average,
            
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    /**
     * Gets query for [[Criteria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCriteria()
    {
        return $this->hasOne(AseCriteriaDescription::class, ['id' => 'criteria_relId']);
    }


    /**
     * Gets query for [[Evaluee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluee()
    {
        return $this->hasOne(User::class, ['id' => 'evaluee_relId']);
    }

     /**
     * Gets query for [[EvalutationResult]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvalutationResult()
    {
        return $this->hasOne(AseEvaluationResult::class, ['id' => 'evaluation_result_relId']);
    }

    /**
     * {@inheritdoc}
     * @return AseEvaluationResultDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AseEvaluationResultDetailQuery(get_called_class());
    }
}
