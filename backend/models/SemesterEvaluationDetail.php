<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation_detail".
 *
 * @property integer $id
 * @property integer $semevdet_semev_relId
 * @property integer $semevdet_evalType_relId
 * @property integer $semevdet_completed
 * @property double $semevdet_averagePoint
 * @property integer $semevdet_evaluee_relId
 *
 * @property SemesterEvaluationTypes $semevdetEvalTypeRel
 * @property User $semevdetEvalueeRel
 * @property SemesterEvaluation $semevdetSemevRel
 * @property SemesterEvaluationResult[] $semesterEvaluationResults
 */
class SemesterEvaluationDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semevdet_semev_relId', 'semevdet_evalType_relId', 'semevdet_completed', 'semevdet_averagePoint', 'semevdet_evaluee_relId'], 'required'],
            [['semevdet_semev_relId', 'semevdet_evalType_relId', 'semevdet_completed', 'semevdet_evaluee_relId'], 'integer'],
            [['semevdet_averagePoint'], 'number'],
            [['semevdet_evalType_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluationTypes::className(), 'targetAttribute' => ['semevdet_evalType_relId' => 'id']],
            [['semevdet_evaluee_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['semevdet_evaluee_relId' => 'id']],
            [['semevdet_semev_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluation::className(), 'targetAttribute' => ['semevdet_semev_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semevdet_semev_relId' => Yii::t('rms', 'Semevdet Semev Rel ID'),
            'semevdet_evalType_relId' => Yii::t('rms', 'Eval. Type'),
            'semevdet_completed' => Yii::t('rms', 'Evln Completed?'),
            'semevdet_averagePoint' => Yii::t('rms', 'Average Point'),
            'semevdet_evaluee_relId' => Yii::t('rms', 'Evaluee'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevdetEvalTypeRel()
    {
        return $this->hasOne(SemesterEvaluationTypes::className(), ['id' => 'semevdet_evalType_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevdetEvalueeRel()
    {
        return $this->hasOne(User::className(), ['id' => 'semevdet_evaluee_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevdetSemevRel()
    {
        return $this->hasOne(SemesterEvaluation::className(), ['id' => 'semevdet_semev_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluationResults()
    {
        return $this->hasMany(SemesterEvaluationResult::className(), ['ser_semevdet_relId' => 'id']);
    }
}
