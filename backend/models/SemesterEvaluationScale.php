<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_evaluation_scale".
 *
 * @property integer $id
 * @property integer $ses_pass_status
 * @property string $ses_name
 * @property double $ses_maxPoint
 * @property double $ses_minPoint
 * @property string $remark
 */
class SemesterEvaluationScale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation_scale';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ses_name', 'ses_maxPoint', 'ses_minPoint'], 'required'],
            [['ses_maxPoint', 'ses_minPoint','ses_pass_status',], 'number'],
            [['remark'], 'string'],
            [['ses_name'], 'string', 'max' => 50],
            [['ses_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ses_name' => Yii::t('rms', 'Scale'),
            'ses_pass_status' => Yii::t('rms', 'Pass Status'),
            'ses_maxPoint' => Yii::t('rms', 'Evaluation Max Point'),
            'ses_minPoint' => Yii::t('rms', 'Min Point'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }
}
