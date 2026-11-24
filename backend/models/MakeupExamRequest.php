<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "makeup_exam_request".
 *
 * @property integer $id
 * @property integer $mer_std_relId
 * @property integer $mer_srd2_relId
 * @property integer $mer_course_relId
 * @property integer $mer_acadYear_relId
 * @property integer $mer_sem_relId
 * @property integer $mer_group_relId
 * @property integer $mer_clslvl_relId
 * @property integer $mer_reason_relId
 * @property integer $mer_registrarApprove_status
 * @property integer $mer_approved
 * @property integer $mer_approved_by
 * @property string $mer_approved_at
 * @property string $mer_created_at
 * @property string $mer_updated_at
 * @property integer $mer_updated_by
 *
 * @property Student $merStdRel
 * @property User $merUpdatedBy
 * @property SemesterRegistration2Detail $merSrd2Rel
 * @property Course $merCourseRel
 * @property SchoolYear $merAcadYearRel
 * @property Semester $merSemRel
 * @property WGroupSetup $merGroupRel
 * @property ClassLevel $merClslvlRel
 * @property MakeupExamReason $merReasonRel
 * @property User $merApprovedBy
 */
class MakeupExamRequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'makeup_exam_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mer_std_relId', 'mer_srd2_relId', 'mer_course_relId', 'mer_acadYear_relId', 'mer_sem_relId', 'mer_group_relId', 'mer_clslvl_relId', 'mer_reason_relId', 'mer_created_at', 'mer_updated_at', 'mer_updated_by'], 'required'],
            [['mer_std_relId', 'mer_srd2_relId', 'mer_course_relId', 'mer_acadYear_relId', 'mer_sem_relId', 'mer_group_relId', 'mer_clslvl_relId', 'mer_reason_relId', 'mer_approved', 'mer_approved_by', 'mer_updated_by'], 'integer'],
            [['mer_approved_at', 'mer_created_at', 'mer_updated_at'], 'safe'],
            [['mer_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['mer_std_relId' => 'id']],
            [['mer_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['mer_updated_by' => 'id']],
            [['mer_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\SemesterRegistration2Detail::className(), 'targetAttribute' => ['mer_srd2_relId' => 'id']],
            [['mer_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['mer_course_relId' => 'id']],
            [['mer_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['mer_acadYear_relId' => 'id']],
            [['mer_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['mer_sem_relId' => 'id']],
            [['mer_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::className(), 'targetAttribute' => ['mer_group_relId' => 'id']],
            [['mer_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::className(), 'targetAttribute' => ['mer_clslvl_relId' => 'id']],
            [['mer_reason_relId'], 'exist', 'skipOnError' => true, 'targetClass' => MakeupExamReason::className(), 'targetAttribute' => ['mer_reason_relId' => 'id']],
            [['mer_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['mer_approved_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'mer_std_relId' => Yii::t('rms', 'Mer Std Rel ID'),
            'mer_srd2_relId' => Yii::t('rms', 'Mer Srd2 Rel ID'),
            'mer_course_relId' => Yii::t('rms', 'Mer Course Rel ID'),
            'mer_acadYear_relId' => Yii::t('rms', 'Mer Acad Year Rel ID'),
            'mer_sem_relId' => Yii::t('rms', 'Mer Sem Rel ID'),
            'mer_group_relId' => Yii::t('rms', 'Mer Group Rel ID'),
            'mer_clslvl_relId' => Yii::t('rms', 'Mer Clslvl Rel ID'),
            'mer_reason_relId' => Yii::t('rms', 'Mer Reason Rel ID'),
            'mer_approved' => Yii::t('rms', 'Mer Approved'),
            'mer_approved_by' => Yii::t('rms', 'Mer Approved By'),
            'mer_approved_at' => Yii::t('rms', 'Mer Approved At'),
            'mer_created_at' => Yii::t('rms', 'Mer Created At'),
            'mer_updated_at' => Yii::t('rms', 'Mer Updated At'),
            'mer_updated_by' => Yii::t('rms', 'Mer Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'mer_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'mer_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::className(), ['id' => 'mer_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'mer_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'mer_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'mer_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerGroupRel()
    {
        return $this->hasOne(WGroupSetup::className(), ['id' => 'mer_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerClslvlRel()
    {
        return $this->hasOne(ClassLevel::className(), ['id' => 'mer_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerReasonRel()
    {
        return $this->hasOne(MakeupExamReason::className(), ['id' => 'mer_reason_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'mer_approved_by']);
    }
}
