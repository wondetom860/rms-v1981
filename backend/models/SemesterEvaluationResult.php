<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation_result".
 *
 * @property integer $id
 * @property integer $ser_semev_relId
 * @property integer $ser_evaluee_relId
 * @property double $ser_point_given
 *
 * @property SemesterEvaluationDetail $serEvalueeRel
 * @property SemesterEvaluation $serSemevRel
 */
class SemesterEvaluationResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ser_semev_relId', 'ser_evaluee_relId'], 'required'],
            [['ser_semev_relId', 'ser_evaluee_relId'], 'integer'],
            [['ser_point_given'], 'number'],
            [['ser_evaluee_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluationDetail::className(), 'targetAttribute' => ['ser_evaluee_relId' => 'semevdet_evaluee_relId']],
            [['ser_semev_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluation::className(), 'targetAttribute' => ['ser_semev_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ser_semev_relId' => Yii::t('rms', 'Ser Semev Rel ID'),
            'ser_evaluee_relId' => Yii::t('rms', 'Ser Evaluee Rel ID'),
            'ser_point_given' => Yii::t('rms', 'Ser Point Given'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSerEvalueeRel()
    {
        return $this->hasOne(SemesterEvaluationDetail::className(), ['semevdet_evaluee_relId' => 'ser_evaluee_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSerSemevRel()
    {
        return $this->hasOne(SemesterEvaluation::className(), ['id' => 'ser_semev_relId']);
    }
}
