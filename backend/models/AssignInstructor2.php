<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "assign_instructor2".
 *
 * @property integer $id
 * @property integer $ai_coff_relId
 * @property integer $ai_group_relId
 * @property integer $ai_inst_relId
 * @property integer $ai_TA_relId
 * @property string $ai_created_at
 * @property string $ai_updated_at
 * @property integer $ai_created_by
 * @property integer $ai_updated_by
 *
 * @property CourseOffering2[] $courseOffering2s
 */
class AssignInstructor2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'assign_instructor2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ai_coff_relId', 'ai_group_relId', 'ai_inst_relId', 'ai_created_at', 'ai_updated_at', 'ai_created_by', 'ai_updated_by'], 'required'],
            [['ai_coff_relId', 'ai_group_relId', 'ai_inst_relId', 'ai_TA_relId', 'ai_created_by', 'ai_updated_by'], 'integer'],
            [['ai_created_at', 'ai_updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ai_coff_relId' => Yii::t('rms', 'Ai Coff Rel ID'),
            'ai_group_relId' => Yii::t('rms', 'Ai Group Rel ID'),
            'ai_inst_relId' => Yii::t('rms', 'Ai Inst Rel ID'),
            'ai_TA_relId' => Yii::t('rms', 'Ai  Ta Rel ID'),
            'ai_created_at' => Yii::t('rms', 'Ai Created At'),
            'ai_updated_at' => Yii::t('rms', 'Ai Updated At'),
            'ai_created_by' => Yii::t('rms', 'Ai Created By'),
            'ai_updated_by' => Yii::t('rms', 'Ai Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOffering2s()
    {
        return $this->hasMany(CourseOffering2::className(), ['coff_ai_relId' => 'id']);
    }
}
