<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "touched_grades".
 *
 * @property int $id
 * @property int $tg_acadYear_relId
 * @property int $tg_sem_relId
 * @property int $tg_prog_relId
 * @property int $tg_clslvl_relId
 * @property int $tg_semreg_relId
 * @property int $tg_srd2_relId
 * @property int|null $tg_beforeGrade_relId
 * @property int|null $tg_afterGrade_relId
 * @property string|null $tg_reason
 * @property string $tg_created_at
 * @property int $tg_created_by
 * @property string|null $tg_updated_at
 * @property int|null $tg_updated_by
 * @property int|null $tg_approved_by
 * @property string|null $tg_approved_at
 * @property int|null $tg_operation_completed
 *
 * @property SchoolYear $tgAcadYearRel
 * @property GradeScale $tgAfterGradeRel
 * @property User $tgApprovedBy
 * @property GradeScale $tgBeforeGradeRel
 * @property ClassLevel $tgClslvlRel
 * @property User $tgCreatedBy
 * @property Program $tgProgRel
 * @property Semester $tgSemRel 
 * @property SemesterRegistration2 $tgSemregRel
 * @property SemesterRegistration2Detail $tgSrd2Rel
 * @property User $tgUpdatedBy
 */
class TouchedGrades extends \yii\db\ActiveRecord
{
    public $first_name,$courseTitle;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'touched_grades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tg_acadYear_relId', 'tg_sem_relId', 'tg_prog_relId', 'tg_clslvl_relId', 'tg_semreg_relId', 'tg_srd2_relId', 'tg_created_at', 'tg_created_by'], 'required'],
            [['tg_acadYear_relId', 'tg_sem_relId', 'tg_prog_relId', 'tg_clslvl_relId', 'tg_semreg_relId', 'tg_srd2_relId', 'tg_beforeGrade_relId', 'tg_afterGrade_relId', 'tg_created_by', 'tg_updated_by', 'tg_approved_by', 'tg_operation_completed'], 'integer'],
            [['tg_reason','first_name','courseTitle'], 'string'],
            [['tg_created_at', 'tg_updated_at', 'tg_approved_at'], 'safe'],
            [['tg_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['tg_acadYear_relId' => 'id']],
            [['tg_afterGrade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['tg_afterGrade_relId' => 'id']],
            [['tg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tg_approved_by' => 'id']],
            [['tg_beforeGrade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['tg_beforeGrade_relId' => 'id']],
            [['tg_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['tg_clslvl_relId' => 'id']],
            [['tg_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tg_created_by' => 'id']],
            [['tg_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['tg_prog_relId' => 'id']],
            [['tg_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['tg_sem_relId' => 'id']],
            [['tg_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['tg_semreg_relId' => 'id']],
            [['tg_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2Detail::class, 'targetAttribute' => ['tg_srd2_relId' => 'id']],
            [['tg_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['tg_updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'tg_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'first_name' => Yii::t('rms', 'Student Name'),
            'courseTitle' => Yii::t('rms', 'Course Title'),
            'tg_sem_relId' => Yii::t('rms', 'Semester'),
            'tg_prog_relId' => Yii::t('rms', 'Program'),
            'tg_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'tg_semreg_relId' => Yii::t('rms', 'Student Name'),
            'tg_srd2_relId' => Yii::t('rms', 'Course'),
            'tg_beforeGrade_relId' => Yii::t('rms', 'Before Grade'),
            'tg_afterGrade_relId' => Yii::t('rms', 'After Grade'),
            'tg_reason' => Yii::t('rms', 'Grade Change Reason'),
            'tg_created_at' => Yii::t('rms', 'Created At'),
            'tg_created_by' => Yii::t('rms', 'Created By'),
            'tg_updated_at' => Yii::t('rms', 'Updated At'),
            'tg_updated_by' => Yii::t('rms', 'Updated By'),
            'tg_approved_by' => Yii::t('rms', 'Approved By'),
            'tg_approved_at' => Yii::t('rms', 'Approved At'),
            'tg_operation_completed' => Yii::t('rms', 'Operation Completed'),
        ];
    }

    public function getRegistrarCAlert(){
        return $this->find()
            ->where([
                'tg_approved_by' => NULL
            ])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'tg_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgAfterGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'tg_afterGrade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'tg_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgBeforeGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'tg_beforeGrade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'tg_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'tg_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'tg_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'tg_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'tg_semreg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'tg_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTgUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'tg_updated_by']);
    }
}
