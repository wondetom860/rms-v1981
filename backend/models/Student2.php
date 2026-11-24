<?php

namespace backend\models;

use Yii;
use frontend\modules\AcademicCalendar\models\AcProgramSchedule;

/**
 * This is the model class for table "student".
 *
 * @property integer $id
 * @property integer $std_user_relId
 * @property string $std_regId
 * @property string $std_type
 * @property integer $std_dept_relId
 * @property integer $std_spec_relId
 * @property integer $std_clslvl_relId
 * @property integer $std_prog_relId
 * @property integer $std_admission_relId
 * @property integer $std_acadYear_relId
 * @property integer $std_group_realId
 * @property string $std_semester_status
 * @property string $std_created_at
 * @property integer $std_created_by
 * @property string $std_updated_at
 * @property integer $std_updated_by
 *
 * @property SemesterRegistration[] $semesterRegistrations
 * @property SchoolYear $stdAcadYearRel
 * @property Admission $stdAdmissionRel
 * @property ClassLevel $stdClslvlRel
 * @property User $stdCreatedBy
 * @property Department $stdDeptRel
 * @property StudentGroupHistory $stdGroupReal
 * @property Program $stdProgRel
 * @property Specialization $stdSpecRel
 * @property User $stdUpdatedBy
 * @property User $stdUserRel
 * @property StudentGroupHistory[] $studentGroupHistories
 */
class Student2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['std_user_relId', 'std_regId', 'std_dept_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_created_at', 'std_created_by', 'std_updated_at', 'std_updated_by'], 'required'],
            [['std_user_relId', 'std_dept_relId', 'std_spec_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_group_realId', 'std_created_by', 'std_updated_by'], 'integer'],
            [['std_created_at', 'std_updated_at'], 'safe'],
            [['std_regId'], 'string', 'max' => 15],
            [['std_type'], 'string', 'max' => 20],
            [['std_semester_status'], 'string', 'max' => 40],
            [['std_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['std_acadYear_relId' => 'id']],
            [['std_admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['std_admission_relId' => 'id']],
            [['std_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['std_clslvl_relId' => 'id']],
            [['std_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_created_by' => 'id']],
            [['std_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['std_dept_relId' => 'id']],
            [['std_group_realId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentGroupHistory::class, 'targetAttribute' => ['std_group_realId' => 'id']],
            [['std_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['std_prog_relId' => 'id']],
            [['std_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['std_spec_relId' => 'id']],
            [['std_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_updated_by' => 'id']],
            [['std_user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_user_relId' => 'id']],
            [['std_acProgSched_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcProgramSchedule::class, 'targetAttribute' => ['std_acProgSched_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'std_user_relId' => Yii::t('rms', 'Student Full Name'),
            'std_regId' => Yii::t('rms', 'Reg. ID'),
            'std_type' => Yii::t('rms', 'Type'),
            'std_dept_relId' => Yii::t('rms', 'Department'),
            'std_spec_relId' => Yii::t('rms', 'Specialization'),
            'std_clslvl_relId' => Yii::t('rms', 'Current Class Level'),
            'std_prog_relId' => Yii::t('rms', 'Program'),
            'std_admission_relId' => Yii::t('rms', 'Std. Admission'),
            'std_acadYear_relId' => Yii::t('rms', 'Year Admission'),
            'std_group_realId' => Yii::t('rms', 'Current Student Group'),
            'std_semester_status' => Yii::t('rms', 'Semester Status'),
            'std_created_at' => Yii::t('rms', 'Created At'),
            'std_created_by' => Yii::t('rms', 'Created By'),
            'std_updated_at' => Yii::t('rms', 'Updated At'),
            'std_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration::class, ['semreg_stud_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'std_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdAdmissionRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'std_admission_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'std_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'std_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'std_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdGroupReal()
    {
        return $this->hasOne(StudentGroupHistory::class, ['id' => 'std_group_realId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'std_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'std_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'std_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdUserRel()
    {
        return $this->hasOne(User::class, ['id' => 'std_user_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentGroupHistories()
    {
        return $this->hasMany(StudentGroupHistory::class, ['sgh_stud_relId' => 'id']);
    }
}
