<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "project_group_members".
 *
 * @property integer $id
 * @property integer $pgm_pgSetup_relId
 * @property integer $pgm_std_relId
 * @property integer $pgm_enroll_type
 * @property integer $pgm_grade_relId
 * @property integer $pgm_gradeApproved
 * @property integer $pgm_gradeApproved_by
 * @property string $pgm_gradeApproved_at
 * @property string $pgm_advisor_comment
 * @property string $pgm_created_at
 * @property integer $pgm_craeted_by
 * @property integer $pgm_updated_by
 * @property string $pgm_updated_at
 *
 * @property ProjectGroupName $pgmPgSetupRel
 * @property Student $pgmStdRel
 * @property GradeScale $pgmGradeRel
 * @property User $pgmGradeApprovedBy
 * @property User $pgmCraetedBy
 * @property User $pgmUpdatedBy
 */
class ProjectGroupMembers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_group_members';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pgm_pgSetup_relId', 'pgm_std_relId', 'pgm_created_at', 'pgm_craeted_by', 'pgm_updated_by', 'pgm_updated_at'], 'required'],
            [['pgm_pgSetup_relId', 'pgm_std_relId', 'pgm_enroll_type', 'pgm_grade_relId', 'pgm_gradeApproved', 'pgm_gradeApproved_by', 'pgm_craeted_by', 'pgm_updated_by'], 'integer'],
            [['pgm_gradeApproved_at', 'pgm_created_at', 'pgm_updated_at'], 'safe'],
            [['pgm_advisor_comment'], 'string'],
            [['pgm_pgSetup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectGroupName::className(), 'targetAttribute' => ['pgm_pgSetup_relId' => 'id']],
            [['pgm_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['pgm_std_relId' => 'id']],
            [['pgm_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::className(), 'targetAttribute' => ['pgm_grade_relId' => 'id']],
            [['pgm_gradeApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pgm_gradeApproved_by' => 'id']],
            [['pgm_craeted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pgm_craeted_by' => 'id']],
            [['pgm_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pgm_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pgm_pgSetup_relId' => Yii::t('rms', 'Project Group Name'),
            'pgm_std_relId' => Yii::t('rms', 'Student Name'),
            'pgm_enroll_type' => Yii::t('rms', 'Project Course Enroll Type'),
            'pgm_grade_relId' => Yii::t('rms', 'Grade'),
            'pgm_gradeApproved' => Yii::t('rms', 'Grade Approved'),
            'pgm_gradeApproved_by' => Yii::t('rms', 'Grade Approved By'),
            'pgm_gradeApproved_at' => Yii::t('rms', 'Grade Approved At'),
            'pgm_advisor_comment' => Yii::t('rms', 'Advisor Comment'),
            'pgm_created_at' => Yii::t('rms', 'Created At'),
            'pgm_craeted_by' => Yii::t('rms', 'Craeted By'),
            'pgm_updated_by' => Yii::t('rms', 'Updated By'),
            'pgm_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function getDeptProjectGroups(){
        $pgg = new ProjectCourseSetup();
        $deptGroups = $pgg->getGsetArray(0);

        $deptProjectGroups = ProjectCourseSetup::find()->where(['pcs_gset_relId'=>$deptGroups])->all();

        $deptProjectGroupsArray = [];
        if ($deptProjectGroups && count($deptProjectGroups) > 0) {
            foreach ($deptProjectGroups as $key) {
                $name = ProjectGroupName::findOne(['id'=>$key->pcs_pgName_relId]);
                $deptProjectGroupsArray[] = $name;
            }
        }
        return $deptProjectGroupsArray;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmPgSetupRel()
    {
        return $this->hasOne(ProjectGroupName::className(), ['id' => 'pgm_pgSetup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'pgm_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmGradeRel()
    {
        return $this->hasOne(GradeScale::className(), ['id' => 'pgm_grade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmGradeApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pgm_gradeApproved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmCraetedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pgm_craeted_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgmUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pgm_updated_by']);
    }
}
