<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "makeup_exam_reason".
 *
 * @property integer $id
 * @property string $mer_desc
 * @property string $remark
 */
class MakeupExamReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'makeup_exam_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mer_desc'], 'required'],
            [['mer_desc', 'remark'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'mer_desc' => Yii::t('rms', 'Makeup Exam Reason'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }
}
