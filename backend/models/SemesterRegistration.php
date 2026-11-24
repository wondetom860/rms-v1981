<?php

namespace backend\models;

use Yii;
use backend\models\Student;
use backend\models\StudentGroupHistory;
use backend\models\User;
use backend\models\CourseOffering;
use backend\models\CourseCatalog;
use backend\models\Semester;
use backend\models\ClassLevel;
use backend\models\SchoolYear;


/**
 * This is the model class for table "semester_registration".
 *
 * @property integer $id
 * @property integer $semreg_stud_relId
 * @property integer $semreg_sem_relId
 * @property integer $semreg_coff_relId
 * @property integer $semreg_cocat_relId
 * @property integer $semreg_clslvl_relId
 * @property integer $semreg_acadYear_relId
 * @property integer $semreg_group_relId
 * @property double $semreg_mark_obtained
 * @property string $semreg_letter_grade
 * @property string $semreg_lgraded_at
 * @property integer $semreg_rep_flag
 * @property integer $semreg_drop_flag
 * @property integer $semreg_add_flag
 * @property integer $semreg_add_inst
 * @property integer $semreg_addw_year
 * @property integer $semreg_addw_group
 * @property integer $semreg_ada_aprvd
 * @property string $semreg_add_aprv_date
 * @property integer $semreg_active_course
 * @property integer $semreg_project
 * @property integer $semreg_transfer
 * @property integer $semreg_reg_by
 * @property string $semreg_reg_date
 * @property integer $semreg_enrl_approved
 * @property integer $semreg_enrl_approved_by
 * @property string $semreg_enrl_approved_date
 * @property integer $semreg_add_approved
 * @property integer $semreg_add_approved_by
 * @property string $semreg_adda_pproved_date
 * @property integer $semreg_approved
 * @property integer $semreg_approved_by
 * @property string $semreg_approved_date
 * @property integer $semreg_penality
 * @property integer $semreg_eval_inst
 * @property integer $semreg_eval_ass
 * @property string $semreg_created_at
 * @property integer $semreg_created_by
 * @property string $semreg_updated_at
 * @property integer $semreg_updated_by
 *
 * @property SchoolYear $semregAcadYearRel
 * @property StudentGroupHistory $semregAddwGroup
 * @property ClassLevel $semregAddwYear
 * @property User $semregEnrlApprovedBy
 * @property ClassLevel $semregClslvlRel
 * @property CourseCatalog $semregCocatRel
 * @property CourseOffering $semregCoffRel
 * @property StudentGroupHistory $semregGroupRel
 * @property Semester $semregSemRel
 * @property Student $semregStudRel
 */
class SemesterRegistration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semreg_stud_relId', 'semreg_sem_relId', 'semreg_coff_relId', 'semreg_cocat_relId', 'semreg_clslvl_relId', 'semreg_acadYear_relId', 'semreg_group_relId', 'semreg_lgraded_at', 'semreg_add_aprv_date', 'semreg_reg_date', 'semreg_enrl_approved_by', 'semreg_enrl_approved_date', 'semreg_adda_pproved_date', 'semreg_approved_date', 'semreg_created_at', 'semreg_created_by', 'semreg_updated_at', 'semreg_updated_by'], 'required'],
            [['id', 'semreg_stud_relId', 'semreg_sem_relId', 'semreg_coff_relId', 'semreg_cocat_relId', 'semreg_clslvl_relId', 'semreg_acadYear_relId', 'semreg_group_relId', 'semreg_rep_flag', 'semreg_drop_flag', 'semreg_add_flag', 'semreg_add_inst', 'semreg_addw_year', 'semreg_addw_group', 'semreg_ada_aprvd', 'semreg_active_course', 'semreg_project', 'semreg_transfer', 'semreg_reg_by', 'semreg_enrl_approved', 'semreg_enrl_approved_by', 'semreg_add_approved', 'semreg_add_approved_by', 'semreg_approved', 'semreg_approved_by', 'semreg_penality', 'semreg_eval_inst', 'semreg_eval_ass', 'semreg_created_by', 'semreg_updated_by'], 'integer'],
            [['semreg_mark_obtained'], 'number'],
            [['semreg_lgraded_at', 'semreg_add_aprv_date', 'semreg_reg_date', 'semreg_enrl_approved_date', 'semreg_adda_pproved_date', 'semreg_approved_date', 'semreg_created_at', 'semreg_updated_at'], 'safe'],
            [['semreg_letter_grade'], 'string', 'max' => 3],
            [['semreg_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['semreg_acadYear_relId' => 'id']],
            [['semreg_addw_group'], 'exist', 'skipOnError' => true, 'targetClass' => StudentGroupHistory::class, 'targetAttribute' => ['semreg_addw_group' => 'id']],
            [['semreg_addw_year'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['semreg_addw_year' => 'id']],
            [['semreg_enrl_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['semreg_enrl_approved_by' => 'id']],
            [['semreg_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['semreg_clslvl_relId' => 'id']],
            [['semreg_cocat_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseCatalog::class, 'targetAttribute' => ['semreg_cocat_relId' => 'id']],
            [['semreg_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering::class, 'targetAttribute' => ['semreg_coff_relId' => 'id']],
            [['semreg_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentGroupHistory::class, 'targetAttribute' => ['semreg_group_relId' => 'id']],
            [['semreg_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['semreg_sem_relId' => 'id']],
            [['semreg_stud_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['semreg_stud_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semreg_stud_relId' => Yii::t('rms', 'Student Name'),
            'semreg_sem_relId' => Yii::t('rms', 'Semester'),
            'semreg_coff_relId' => Yii::t('rms', 'Course Offering'),
            'semreg_cocat_relId' => Yii::t('rms', 'Course Catalog'),
            'semreg_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'semreg_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'semreg_group_relId' => Yii::t('rms', 'Group'),
            'semreg_mark_obtained' => Yii::t('rms', 'Semreg Mark Obtained'),
            'semreg_letter_grade' => Yii::t('rms', 'Semreg Letter Grade'),
            'semreg_lgraded_at' => Yii::t('rms', 'Semreg Lgraded At'),
            'semreg_rep_flag' => Yii::t('rms', 'Semreg Rep Flag'),
            'semreg_drop_flag' => Yii::t('rms', 'Semreg Drop Flag'),
            'semreg_add_flag' => Yii::t('rms', 'Semreg Add Flag'),
            'semreg_add_inst' => Yii::t('rms', 'Semreg Add Inst'),
            'semreg_addw_year' => Yii::t('rms', 'Semreg Addw Year'),
            'semreg_addw_group' => Yii::t('rms', 'Semreg Addw Group'),
            'semreg_ada_aprvd' => Yii::t('rms', 'Semreg Ada Aprvd'),
            'semreg_add_aprv_date' => Yii::t('rms', 'Semreg Add Aprv Date'),
            'semreg_active_course' => Yii::t('rms', 'Semreg Active Course'),
            'semreg_project' => Yii::t('rms', 'Semreg Project'),
            'semreg_transfer' => Yii::t('rms', 'Semreg Transfer'),
            'semreg_reg_by' => Yii::t('rms', 'Semreg Reg By'),
            'semreg_reg_date' => Yii::t('rms', 'Semreg Reg Date'),
            'semreg_enrl_approved' => Yii::t('rms', 'Semreg Enrl Approved'),
            'semreg_enrl_approved_by' => Yii::t('rms', 'Semreg Enrl Approved By'),
            'semreg_enrl_approved_date' => Yii::t('rms', 'Semreg Enrl Approved Date'),
            'semreg_add_approved' => Yii::t('rms', 'Semreg Add Approved'),
            'semreg_add_approved_by' => Yii::t('rms', 'Semreg Add Approved By'),
            'semreg_adda_pproved_date' => Yii::t('rms', 'Semreg Adda Pproved Date'),
            'semreg_approved' => Yii::t('rms', 'Semreg Approved'),
            'semreg_approved_by' => Yii::t('rms', 'Semreg Approved By'),
            'semreg_approved_date' => Yii::t('rms', 'Semreg Approved Date'),
            'semreg_penality' => Yii::t('rms', 'Semreg Penality'),
            'semreg_eval_inst' => Yii::t('rms', 'Semreg Eval Inst'),
            'semreg_eval_ass' => Yii::t('rms', 'Semreg Eval Ass'),
            'semreg_created_at' => Yii::t('rms', 'Semreg Created At'),
            'semreg_created_by' => Yii::t('rms', 'Semreg Created By'),
            'semreg_updated_at' => Yii::t('rms', 'Semreg Updated At'),
            'semreg_updated_by' => Yii::t('rms', 'Semreg Updated By'),
        ];
    }

    public function evaluatedAll(){

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'semreg_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregAddwGroup()
    {
        return $this->hasOne(StudentGroupHistory::class, ['id' => 'semreg_addw_group']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregAddwYear()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'semreg_addw_year']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregEnrlApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_enrl_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'semreg_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregCocatRel()
    {
        return $this->hasOne(CourseCatalog::class, ['id' => 'semreg_cocat_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregCoffRel()
    {
        return $this->hasOne(CourseOffering::class, ['id' => 'semreg_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregGroupRel()
    {
        return $this->hasOne(StudentGroupHistory::class, ['id' => 'semreg_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'semreg_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregStudRel()
    {
        return $this->hasOne(Student::class, ['id' => 'semreg_stud_relId']);
    }
}
