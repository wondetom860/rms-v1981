<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation".
 *
 * @property integer $id
 * @property integer $semev_acadYear_relId
 * @property integer $semev_sem_relId
 * @property integer $semev_evaluator_relId
 *
 * @property SchoolYear $semevAcadYearRel
 * @property User $semevEvaluatorRel
 * @property Semester $semevSemRel
 * @property SemesterEvaluationDetail[] $semesterEvaluationDetails
 */
class SemesterEvaluation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semev_acadYear_relId', 'semev_sem_relId','semev_evaluator_relId'], 'required'],
            [['semev_acadYear_relId', 'semev_sem_relId','semev_evaluator_relId'], 'integer'],
            [['semev_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['semev_acadYear_relId' => 'id']],
            [['semev_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['semev_sem_relId' => 'id']],
            [['semev_evaluator_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['semev_evaluator_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semev_acadYear_relId' => Yii::t('rms', 'Acad Year'),
            'semev_sem_relId' => Yii::t('rms', 'Sem'),
            'semev_evaluator_relId' => Yii::t('rms', 'Evaluator'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'semev_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevEvaluatorRel()
    {
        return $this->hasOne(User::className(), ['id' => 'semev_evaluator_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'semev_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluationDetails()
    {
        return $this->hasMany(SemesterEvaluationDetail::className(), ['semevdet_semev_relId' => 'id']);
    }
}
