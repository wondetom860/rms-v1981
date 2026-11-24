<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation_types".
 *
 * @property integer $id
 * @property string $semev_type_desc
 * @property integer $semev_type_level
 * @property double $semev_type_maxPoint
 *
 * @property SemesterEvaluation2[] $semesterEvaluation2s
 * @property SemesterEvaluationDetail[] $semesterEvaluationDetails
 */
class SemesterEvaluationTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semev_type_desc'], 'required'],
            [['semev_type_desc'], 'string'],
            [['semev_type_level'], 'integer'],
            [['semev_type_maxPoint'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semev_type_desc' => Yii::t('rms', 'Semester Evaluation Type Desc'),
            'semev_type_level' => Yii::t('rms', 'Level'),
            'semev_type_maxPoint' => Yii::t('rms', 'Maximim Point'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluation2s()
    {
        return $this->hasMany(SemesterEvaluation2::className(), ['semev_evalType_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluationDetails()
    {
        return $this->hasMany(SemesterEvaluationDetail::className(), ['semevdet_evalType_relId' => 'id']);
    }
}
