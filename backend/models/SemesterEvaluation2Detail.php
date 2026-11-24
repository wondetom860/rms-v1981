<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation2_detail".
 *
 * @property integer $id
 * @property integer $semev2det_semev_relId
 * @property integer $semev2det_evaluator_relId
 * @property integer $semev2det_completed
 *
 * @property SemesterEvaluation2 $semev2detSemevRel
 * @property User $semev2detEvaluatorRel
 */
class SemesterEvaluation2Detail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation2_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semev2det_semev_relId', 'semev2det_evaluator_relId'], 'required'],
            [['semev2det_semev_relId', 'semev2det_evaluator_relId', 'semev2det_completed'], 'integer'],
            [['semev2det_evaluated_at'],'safe'],
            [['semev2det_semev_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluation2::className(), 'targetAttribute' => ['semev2det_semev_relId' => 'id']],
            [['semev2det_evaluator_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['semev2det_evaluator_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semev2det_semev_relId' => Yii::t('rms', 'Semev2det Semev Rel ID'),
            'semev2det_evaluator_relId' => Yii::t('rms', 'Semev2det Evaluator Rel ID'),
            'semev2det_completed' => Yii::t('rms', 'Semev2det Completed'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemev2detSemevRel()
    {
        return $this->hasOne(SemesterEvaluation2::className(), ['id' => 'semev2det_semev_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemev2detEvaluatorRel()
    {
        return $this->hasOne(User::className(), ['id' => 'semev2det_evaluator_relId']);
    }
}
