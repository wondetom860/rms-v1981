<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation_points".
 *
 * @property integer $id
 * @property integer $semevPoint_semev_relId
 * @property string $sem_eval_point_desc
 * @property string $remark
 *
 * @property SemesterEvaluationTypes $semevPointSemevRel
 */
class SemesterEvaluationPoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation_points';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semevPoint_semev_relId', 'sem_eval_point_desc', 'remark'], 'required'],
            [['semevPoint_semev_relId'], 'integer'],
            [['sem_eval_point_desc', 'remark'], 'string'],
            [['semevPoint_semev_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluationTypes::className(), 'targetAttribute' => ['semevPoint_semev_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semevPoint_semev_relId' => Yii::t('rms', 'Evaluation Type'),
            'sem_eval_point_desc' => Yii::t('rms', 'Evaluation Point'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevPointSemevRel()
    {
        return $this->hasOne(SemesterEvaluationTypes::className(), ['id' => 'semevPoint_semev_relId']);
    }
}
