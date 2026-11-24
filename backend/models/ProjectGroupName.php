<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "project_group_name".
 *
 * @property integer $id
 * @property string $pg_name
 * @property integer $pg_size
 * @property string $remark
 *
 * @property ProjectCourseSetup[] $projectCourseSetups
 * @property ProjectGroupMembers[] $projectGroupMembers
 */
class ProjectGroupName extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_group_name';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pg_name', 'pg_size', 'remark'], 'required'],
            [['pg_size'], 'integer'],
            [['remark'], 'string'],
            [['pg_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pg_name' => Yii::t('rms', 'Pg Name'),
            'pg_size' => Yii::t('rms', 'Pg Size'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCourseSetups()
    {
        return $this->hasMany(ProjectCourseSetup::className(), ['pcs_pgName_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectGroupMembers()
    {
        return $this->hasMany(ProjectGroupMembers::className(), ['pgm_pgSetup_relId' => 'id']);
    }
}
