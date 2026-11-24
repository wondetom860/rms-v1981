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
 * @property integer $std_schlrshpType_relId
 * @property integer $std_admission_relId
 * @property integer $std_acadYear_relId
 * @property integer|null $std_sem_relId
 * @property integer $std_group_realId
 * @property string $std_semester_status
 * @property string $std_marital_status
 * @property integer $std_emergency_contact_relId
 * @property integer $st_educ_info_relId
 * @property string $std_created_at
 * @property integer $std_created_by
 * @property string $std_updated_at
 * @property string $std_deptChoice
 * @property integer $std_updated_by
 * @property integer $std_acProgSched_relId
 *
 * @property AddHistory[] $addHistories
 * @property Attendancedetail[] $attendancedetails
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property MakeupExamRequest[] $makeupExamRequests
 * @property ProjectCourseSetup[] $projectCourseSetups
 * @property ProjectGroupMembers[] $projectGroupMembers
 * @property ReadmissionHistory[] $readmissionHistories
 * @property ReassessmentHistory[] $reassessmentHistories
 * @property SelfSponsoredPaymentHistory[] $selfSponsoredPaymentHistories
 * @property SemesterRegistration2[] $semesterRegistration2s
 * @property SchoolYear $stdAcadYearRel
 * @property Admission $stdAdmissionRel
 * @property ClassLevel $stdClslvlRel
 * @property User $stdCreatedBy
 * @property Department $stdDeptRel
 * @property WGroupSetup $stdGroupReal
 * @property Program $stdProgRel
 * @property Specialization $stdSpecRel
 * @property User $stdUpdatedBy
 * @property User $stdUserRel
 * @property StudentSemesterStatusHistory[] $studentSemesterStatusHistories
 * @property StudentWithdrawalHistory[] $studentWithdrawalHistories
 * @property WGroupAssignment[] $wGroupAssignments
 */
class Student1 extends \yii\db\ActiveRecord
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
            [['std_prog_relId', 'std_schlrshpType_relId', 'std_admission_relId','std_type'], 'required'],
            [['std_user_relId', 'std_dept_relId', 'std_spec_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_schlrshpType_relId', 'std_admission_relId', 'std_pg_EC_relId', 'std_acadYear_relId', 'std_group_realId', 'std_emergency_contact_relId', 'st_educ_info_relId', 'std_created_by', 'std_updated_by','std_sem_relId','std_acProgSched_relId'], 'integer'],
            [['std_marital_status'], 'string'],
            [['std_created_at', 'std_updated_at','std_deptChoice'], 'safe'],
            [['std_regId'], 'string', 'max' => 15],
            [['std_dept_relId', 'std_spec_relId'], 'required' ,'when'=>function($model){
                // if the program selected is masters, both dept and spec are required.

                if (is_null($model->std_prog_relId)) {
                    return false;
                }
                // $prog = Program::findOne($model->std_prog_relId);
                if (strtoupper($model->stdProgRel->program_name) == 'MASTERS') {
                    return true;
                }
                return false;
            }],
            [['std_type'], 'string', 'max' => 20],
            [['std_deptChoice','std_admissionEntries'], 'string', 'max' => 200],
            [['std_semester_status'], 'string', 'max' => 40],
            [['std_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['std_acadYear_relId' => 'id']],
            [['std_admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['std_admission_relId' => 'id']],
            [['std_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['std_clslvl_relId' => 'id']],
            [['std_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_created_by' => 'id']],
            [['std_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['std_dept_relId' => 'id']],
            [['std_group_realId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['std_group_realId' => 'id']],
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
            'std_user_relId' => Yii::t('rms', 'User'),
            'std_regId' => Yii::t('rms', 'Reg ID'),
            'std_type' => Yii::t('rms', 'Student Type'),
            'std_dept_relId' => Yii::t('rms', 'Department'),
            'std_spec_relId' => Yii::t('rms', 'Specialization'),
            'std_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'std_prog_relId' => Yii::t('rms', 'Program'),
            'std_schlrshpType_relId' => Yii::t('rms', 'SponsorShip Type'),
            'std_admission_relId' => Yii::t('rms', 'Admission'),
            'std_acadYear_relId' => Yii::t('rms', 'Year of Admission'),
            'std_sem_relId' => Yii::t('rms', 'Semester of Admission'),
            'std_group_realId' => Yii::t('rms', 'Curr. Group'),
            'std_semester_status' => Yii::t('rms', 'Semester Status'),
            'std_marital_status' => Yii::t('rms', 'Marital Status'),
            'entry_type' => Yii::t('rms', 'Marital Status'),
            'std_emergency_contact_relId' => Yii::t('rms', 'Emergency Contact'),
            'st_educ_info_relId' => Yii::t('rms', 'Educn. Info'),
            'std_created_at' => Yii::t('rms', 'Created At'),
            'std_created_by' => Yii::t('rms', 'Created By'),
            'std_updated_at' => Yii::t('rms', 'Updated At'),
            'std_updated_by' => Yii::t('rms', 'Updated By'),
            'std_acProgSched_relId' => Yii::t('rms', 'Admission Schedule'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddHistories()
    {
        return $this->hasMany(AddHistory::class, ['ah_std_relId' => 'id']);
    }

    public function getStdRegId($admission,$program,$department,$spec=null){
        $adId = \backend\models\Admission::findOne($admission);

        $progId = \backend\models\Program::findOne($program);//
        $scyr = \backend\models\SchoolYear::find()->where(['sch_year_desc'=>Yii::$app->params['C']['Y']])->one();
        // if (is_null($scyr->sch_bach_number) || 0) {
        //     throw new yii\web\NotFoundHttpException("Batch Configuration is Not completed yet, Please wait a moment.", 1);
        // }
        $dept = \backend\models\Department::findOne($department);

        // if (is_null($dept->dept_id_prefix)) {
        //     throw new yii\web\NotFoundHttpException("Department ID_PREFIX Configuration Error, NOT_FOUND_FOR_YOUR_SELECTED_DEPARTMENT.", 1);
        // }
        $regId = '';

        $count = Student::find()->where([
            'std_acadYear_relId'=>$scyr['id'],
            'std_admission_relId'=>$adId['id'],
            'std_prog_relId'=>$progId['id'],
        ])->count();
        
        $count++;


        // if (!is_null($spec)) {
        //     $specObj = \backend\models\Specialization::findOne($spec);
        //     if ($specObj && count($specObj) > 0) {
        //         if ($specObj->spec_id_prefix) {
        //             $regId1 = $specObj->spec_id_prefix;
        //         }                
        //     }
        // }else{
        //     // check department prefix
        //     if ($dept && count($dept) > 0) {
        //         if ($dept->dept_id_prefix) {
        //             $regId1 = $dept->dept_id_prefix;
        //         }
        //     }else{
        //         $regId1 = !is_null($progId->program_prefix) ? strtoupper($progId->program_prefix) : '';
        //     }
        // }
        
        // $rrb = 7;
        // if(intval(date('m')) < 9){$rrb = 8;}
        // $year = substr(date('Y')-$rrb,-2);



        $regId1 = strtoupper($adId->admission_name[0]). (!is_null($progId->program_prefix) ? strtoupper($progId->program_prefix) : die('Program Prefix configuration error! Please contact Registrar office'));
        
        $rrb = 7;
        if(intval(date('m')) < 9){$rrb = 8;}
        $year = substr(date('Y')-$rrb,-2);

        // check if such exisxts in database.
        while (true) {
            // $scyr->sch_bach_number. 
            $regId = $regId1. '/'.sprintf("%04d",$count).'/'.$year;//
            $exisxts = Student1::find()->where(['std_regId'=>$regId])->one();
            if ($exisxts) {
                ++$count;
            }else{
                return $regId;
            }
        }
        return $regId;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendancedetails()
    {
        return $this->hasMany(Attendancedetail::class, ['ad_student_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment2::class, ['ca_std_relid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMakeupExamRequests()
    {
        return $this->hasMany(MakeupExamRequest::class, ['mer_std_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCourseSetups()
    {
        return $this->hasMany(ProjectCourseSetup::class, ['pcs_group_leader' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectGroupMembers()
    {
        return $this->hasMany(ProjectGroupMembers::class, ['pgm_std_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReadmissionHistories()
    {
        return $this->hasMany(WStudentReadmissionHistory::class, ['rh_student_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReassessmentHistories()
    {
        return $this->hasMany(ReassessmentHistory::class, ['ras_std_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelfSponsoredPaymentHistories()
    {
        return $this->hasMany(SelfSponsoredPaymentHistory::class, ['ssph_std_relId' => 'id']);
    }

    // public function beforeSave($insert)
    // {
    //     if (parent::beforeSave($insert)) {
    //         if ($this->isNewRecord) {
    //             $this->std_admission_relId = null;
    //             $this->std_prog_relId = null;
    //             $this->std_clslvl_relId = null;

    //             Yii::$app->session->setFlash('error','Something went wrong, Please try again.');
    //         }
    //         return false;
    //     }
    //     return false;
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2s()
    {
        return $this->hasMany(SemesterRegistration2::class, ['semreg_std_relId' => 'id']);
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
    public function getStdSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'std_sem_relId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdScholarshpTypeRel()
    {
        return $this->hasOne(ScholarshipType::class, ['id' => 'std_schlrshpType_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdEmergencyContactRel()
    {
        return $this->hasOne(EmergencyContact::class, ['id' => 'std_emergency_contact_relId']);
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
        return $this->hasOne(WGroupSetup::class, ['id' => 'std_group_realId']);
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
    public function getStdAcProgSchedRel()//std_acProgSched_relId
    {
        return $this->hasOne(AcProgramSchedule::class, ['id' => 'std_acProgSched_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSemesterStatusHistories()
    {
        return $this->hasMany(StudentSemesterStatusHistory::class, ['sssh_std_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentWithdrawalHistories()
    {
        return $this->hasMany(WStudentWithdrawalHistory::class, ['swh_student_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWGroupAssignments()
    {
        return $this->hasMany(WGroupAssignment::class, ['gas_std_relId' => 'id']);
    }
}