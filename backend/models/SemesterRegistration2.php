<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_registration_2".
 *
 * @property integer $id
 * @property integer $semreg_std_relId
 * @property integer $semreg_acadYear_relId
 * @property integer $semreg_clslvl_relId
 * @property integer $semreg_semester_relId
 * @property double $semreg_SGPA
 * @property double $semreg_CGPA
 * @property integer $semreg_re_registered
 * @property integer $semreg_approved
 * @property integer $semreg_entry_type
 * @property integer $semreg_totalCrdtHr
 * @property integer $semreg_gradePoint_sum
 * @property string $semreg_approved_at
 * @property integer $semreg_created_by
 * @property integer $semreg_updated_by
 * @property string $semreg_created_at
 * @property string $semreg_updated_at
 *
 * @property SchoolYear $semregAcadYearRel
 * @property User $semregApprovedBy
 * @property ClassLevel $semregClslvlRel
 * @property User $semregCreatedBy
 * @property Semester $semregSemesterRel
 * @property Student $semregStdRel
 * @property User $semregUpdatedBy
 * @property SemesterRegistration2Detail[] $semesterRegistration2Details
 */
class SemesterRegistration2 extends \yii\db\ActiveRecord
{
    public $enrollCourses = null;
    public $std_prog_relId = null;
    public $std_dept_relId = null;
    public $first_name, $stdId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration_2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semreg_std_relId', 'semreg_acadYear_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_created_by', 'semreg_updated_by', 'semreg_created_at', 'semreg_updated_at'], 'required'],
            [['semreg_std_relId', 'semreg_acadYear_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_approved', 'semreg_approved_by', 'semreg_created_by', 'semreg_updated_by', 'semreg_last_ssn_relId', 'semreg_entry_type'], 'integer'],
            [['semreg_SGPA', 'semreg_CGPA'], 'number'],
            [['first_name'], 'string'],
            [['semreg_approved_at', 'semreg_created_at', 'semreg_updated_at'], 'safe'],
            [['semreg_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\SchoolYear::class, 'targetAttribute' => ['semreg_acadYear_relId' => 'id']],
            [['semreg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_approved_by' => 'id']],
            [['semreg_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\ClassLevel::class, 'targetAttribute' => ['semreg_clslvl_relId' => 'id']],
            [['semreg_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_created_by' => 'id']],
            [['semreg_semester_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Semester::class, 'targetAttribute' => ['semreg_semester_relId' => 'id']],
            [['semreg_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Student::class, 'targetAttribute' => ['semreg_std_relId' => 'id']],
            [['semreg_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['semreg_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semreg_std_relId' => Yii::t('rms', 'Student Name'),
            'semreg_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'semreg_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'semreg_semester_relId' => Yii::t('rms', 'Semester'),
            'semreg_SGPA' => Yii::t('rms', 'SGPA'),
            'stdId' => Yii::t('rms', 'Reg. ID'),
            'first_name' => Yii::t('rms', 'Student Name'),
            'std_prog_relId' => Yii::t('rms', 'Progam'),
            'std_dept_relId' => Yii::t('rms', 'Department'),
            'semreg_CGPA' => Yii::t('rms', 'CGPA'),
            'semreg_last_ssn_relId' => Yii::t('rms', 'Semester Status'),
            'semreg_gradeCompleted' => Yii::t('rms', 'Grade Report'),
            'semreg_approved' => Yii::t('rms', 'Approved'),
            'semreg_approved_by' => Yii::t('rms', 'Approved By'),
            'semreg_approved_at' => Yii::t('rms', 'Approved At'),
            'semreg_created_by' => Yii::t('rms', 'Created By'),
            'semreg_updated_by' => Yii::t('rms', 'Updated By'),
            'semreg_created_at' => Yii::t('rms', 'Created At'),
            'semreg_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function regradeRequestable()
    {
        // $this->updateSrd3();
        $regradable = false;
        foreach($this->semesterRegistration2Details as $srd2) {
            $srd2->updateSrd3();
            if ($srd2->catalogMinPassGradeNotAchieved() && !$srd2->notRequested()) {
                $regradable = true;
                break;
            }
        }
        return $regradable;
        // return $this->getSemesterRegistration2Details()
        //     ->innerJoinWith('srd2GradeRel')
        //     ->where(' gs_gradePoint <= 2.5 ')
        //     ->exists();
    }

    public function getSemregHeader()
    {
        return "{$this->semregAcadYearRel->sch_year_desc}-{$this->semregSemesterRel->semester_name}";
    }

    public function getStudentGroup()
    {
        $gAssignmentModel = new WGroupAssignment();

        return $gAssignmentModel->getStudentGroupAssignment($this->semregAcadYearRel, $this->semregSemesterRel, $this->semregStdRel);
    }

    public function alreadyExists()
    {
        return self::find()
            ->where([
                'semreg_acadYear_relId' => $this->semreg_acadYear_relId,
                'semreg_semester_relId' => $this->semreg_semester_relId,
                'semreg_clslvl_relId' => $this->semreg_clslvl_relId,
                'semreg_std_relId' => $this->semreg_std_relId
            ])
            ->count() > 0;
    }

    public function upgradeSrd2()
    {
        /**
         * @var \backend\models\SemesterRegistration2Detail $srd2
         */
        $srd2s = $this->getSemesterRegistration2Details()
            ->andWhere(' srd2_grade_relId IS NOT NULL') //AND srd2_gradeLocked <> 1 //add grade_locked => 0 for upgrading only unlocked entries.
            ->andWhere([
                'OR',
                ['srd2_letter_grade' => NULL],
                ['srd2_grade_point_sum' => NULL],
                ['srd2_grade_point' => NULL],
                ['srd2_pass_status' => 0]
            ])
            ->all();
        if ($srd2s) {
            foreach ($srd2s as $srd2) {
                $ltrGrade = $srd2->srd2GradeRel;
                $course = $srd2->srd2CourseRel;

                $grade_point_sum = $course->course_crdthr * $ltrGrade->gs_gradePoint;
                $catalogAcieved = $srd2->catalogMinPassGradeNotAchieved($srd2->srd2GroupRel);
                $srd2->srd2_letter_grade = $ltrGrade->gs_ltr_grade;
                $srd2->srd2_grade_point = $ltrGrade->gs_gradePoint;
                $srd2->srd2_pass_status = ($catalogAcieved  == NULL) ? $ltrGrade->gs_pass_status : intval(!$catalogAcieved);
                $srd2->srd2_grade_point_sum = $grade_point_sum;

                $srd2->save();
            }
        }
    }

    public function getSemesterGrade($studentObj, $prevGset)
    {
        return $this->find()
            ->where([
                'semreg_acadYear_relId' => $prevGset->gs_acadYear_relId,
                'semreg_semester_relId' => $prevGset->gs_sem_relId,
                'semreg_std_relId' => $studentObj->id,
                'semreg_clslvl_relId' => $prevGset->gs_clslvl_relId,
                'semreg_gradeCompleted' => 1,
            ])
            ->one();
    }

    public function createSection()
    {
        // check if sectionassignment record exists
        $gas = new WGroupAssignment();
        $modelCoff = new CourseOffering2();
        $assigned = $gas->getStudentGroupAssignment($this->semregAcadYearRel, $this->semregSemesterRel, $this->semregStdRel);
        if ($assigned) {
            $modelCoff->distributeCoff($assigned->gasGsetRel);
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        $gset = new WGroupSetup();
        $gset->gs_acadYear_relId = $this->semreg_acadYear_relId;
        $gset->gs_sem_relId = $this->semreg_semester_relId;
        $gset->gs_clslvl_relId = $this->semreg_clslvl_relId;
        $gset->gs_adm_relId = $this->semregStdRel->std_admission_relId;
        $gset->gs_prog_relId = $this->semregStdRel->std_prog_relId;
        $gset->gs_dept_relId = $this->semregStdRel->std_dept_relId;
        
        $gset->gs_group_relId = 1;
        if ($gset->suchExists()) {
            $modelCoff->distributeCoff($gset);
        } else {
            $gset->gs_created_at = date('Y-m-d H:i:s');
            $gset->gs_created_by = Yii::$app->user->id;
            $gset->gs_updated_at = date('Y-m-d H:i:s');
            $gset->gs_updated_by = Yii::$app->user->id;
            if ($gset->save()) {
                $modelCoff->distributeCoff($gset);
            } else {
                $transaction->rollBack();
                return "Creating section failed, Error: " . json_encode($gset->errors);
            }
        }
        
        $gas = new WGroupAssignment();
        $gas->gas_gset_relId = $gset->id;
        $gas->gas_std_relId = $this->semreg_std_relId;
        $gas->gas_created_at = time();
        if ($gas->save()) {

            $gset->checkAcLink();

            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return "Assigning to section failed, Error: " . json_encode($gas->errors);
        }
    }

    public function evaluatedAll()
    {

        if ($this->semreg_evaluatedAll == 1) {
            return true;
        }

        $srd2s = $this->semesterRegistration2Details;

        if ($srd2s && count($srd2s) > 0) { //indicates student enrolled some course
            $updt = true;
            foreach ($srd2s as $keySrd2) {
                if ($keySrd2->srd2_evaluationCompleted) {
                    continue;
                } else {
                    $evaluated = $keySrd2->evaluatedCourseInstructors();
                    if ($evaluated == true) {
                        continue;
                    } else if (is_numeric($evaluated) && $evaluated == 1) {
                        $updt = false;
                    } else {
                        return false;
                    }
                }
            }
            if ($updt) {
                $this->semreg_evaluatedAll = 1;
                $this->update();
            }

            return true;
        } else {
            return true;
        }
    }

    public function calculateGPA($prevSemreg = NULL)
    {
        // var_dump("calculate",$prevSemreg);
        if (!$this->semreg_gradeCompleted || ($this->semreg_gradePoint_sum == 0) || ($this->semreg_totalCrdtHr == 0)) { // == 0
            $grade_submission_completed = 1;
            $all_approved = 1;
            $grade_point_sum = 0;
            $rptd_grade_point_sum = 0;
            $crdt_hr_sum = 0;
            $rptd_crdt_hr_sum = 0;
            $prjt_crdt_hr_sum = 0;
            $rptd_prjt_crdt_hr_sum = 0;
            $gset = null;
            $srd2s = $this->semesterRegistration2Details;
            if(array_filter($srd2s,function($srd2){//NG,I,NULL
                return (($srd2->srd2_grade_relId == NULL) || in_array($srd2->srd2GradeRel->gs_ltr_grade,['NG','I']));
            }) != NULL){
                return NULL;
            };
            foreach ($srd2s as $srd2) {
                $course = $srd2->srd2CourseRel;
                $grade_pointed = false;
                // $gset = $srd2->srd2_coadd_flag ? $gset : $srd2->srd2GroupRel;
                if ($srd2->srd2_grade_relId) {
                    if (!$srd2->srd2_gradeApproved) {
                        $all_approved = 0;
                    }
                    $grade_pointed = $srd2->srd2GradeRel->gs_gradePoint != 0; //check if gradeded pass/fail.
                    $grpt = $srd2->srd2GradeRel->gs_gradePoint * $course->course_crdthr;
                    $grade_point_sum += $grpt;
                    $rptd_grade_point_sum += $srd2->srd2_corptd_flag ? $grpt : 0;
                    $rptd_crdt_hr_sum += $srd2->srd2_corptd_flag ? $course->course_crdthr : 0;
                } else {
                    $grade_submission_completed = 0;
                }
                // check if project course:
                $coff2det = $srd2->srd2CoffRel
                    ->getCourseOffering2Details()
                    ->andWhere(['coff2det_course_relId' => $course->id])
                    ->one();
                if (is_null($coff2det)) { //if course is not found in course offering details
                    $srd2->delete();
                    continue;
                }
                if ($coff2det->coff2detCocatRel->cocat_courseProject && !$grade_pointed) {
                    if ($srd2->srd2_corptd_flag) {
                        $rptd_prjt_crdt_hr_sum += $course->course_crdthr;
                    }
                    $prjt_crdt_hr_sum += $course->course_crdthr;
                }

                $crdt_hr_sum += $course->course_crdthr;
            }

            if ($crdt_hr_sum !== 0) {
                $cgps = 0;
                $cchrs = 0;
                $sgpa = 0;
                $cgpa = 0;
                // update entries:
                if (!is_null($prevSemreg)) {
                    $cgps = $prevSemreg->semreg_gradePoint_sum + $grade_point_sum - $rptd_grade_point_sum;
                    $cchrs = $prevSemreg->semreg_totalCrdtHr + $crdt_hr_sum - $rptd_crdt_hr_sum - $prjt_crdt_hr_sum;

                    if ($crdt_hr_sum - ($rptd_crdt_hr_sum + $prjt_crdt_hr_sum + $rptd_prjt_crdt_hr_sum) > 0) {
                        $sgpa = $grade_point_sum / ($crdt_hr_sum - ($rptd_crdt_hr_sum + $prjt_crdt_hr_sum + $rptd_prjt_crdt_hr_sum));
                        $cgpa = $cgps / $cchrs;
                        if ($sgpa > 4) {
                            $sgpa = 0;
                        }
                    } else {
                        if ($grade_point_sum == 0) {
                            $sgpa = 0;
                            $cgpa = $prevSemreg->semreg_CGPA;
                        } else {
                            $sgpa = $prevSemreg->semreg_SGPA;
                            $cgpa = $prevSemreg->semreg_CGPA;
                        }
                    }
                } else { //case of (1-I)
                    $cgps = $grade_point_sum - $rptd_grade_point_sum;
                    $cchrs = $crdt_hr_sum - $rptd_crdt_hr_sum;
                    $sgpa = $crdt_hr_sum > 0 ? $grade_point_sum / $crdt_hr_sum : 0;
                    if ($crdt_hr_sum - $rptd_crdt_hr_sum > 0) {
                        $sgpa = $grade_point_sum / ($crdt_hr_sum - $rptd_crdt_hr_sum);
                        $cgpa = $cgps / $cchrs;
                    } else {
                        $cgpa = 0;
                        $sgpa = 0;
                    }
                }
                // var_dump($sgpa);exit();
                $this->semreg_gradeCompleted = $grade_submission_completed && $all_approved;
                if ($grade_submission_completed) {
                    $this->semreg_gradePoint_sum = $cgps;
                    $this->semreg_totalCrdtHr  = $cchrs;

                    $this->semreg_SGPA = $sgpa;
                    $this->semreg_CGPA = $cgpa;
                } else {
                    $this->semreg_gradePoint_sum = 0;
                    // $this->semreg_totalCrdtHr  = 0;

                    $this->semreg_SGPA = 0;
                    $this->semreg_CGPA = 0;
                    $this->semreg_last_ssn_relId = NULL;
                }
                // echo "<hr>";
                $this->update(false);
                $this->computeStatus($gset);
                // update student record entry for crdthrs and gps:
                if($grade_submission_completed && ($this->semregStdRel->total_credit_hrs < $this->semreg_totalCrdtHr)){
                    $std = $this->semregStdRel;
                    $std->total_credit_hrs = $this->semreg_totalCrdtHr;
                    $std->total_cgpa = $cgpa;
                }
                // sleep(100);
                $this->refresh();
            }
        }
        // $this->
    }

    public function getSemesterStatus($gset=null){
        return $this->computeStatus($gset);
    }

    private function computeStatus($gset = NULL)
    {
        // semregModel
        $srd2Model = new SemesterRegistration2Detail();
        // $gset = $gset ? $gset : $this->semregStdRel->std_group_realId;
        return $srd2Model->getSemregRecord($this->semreg_std_relId, $this->semreg_clslvl_relId, $this->semreg_semester_relId, $this->semregStdRel->std_group_realId, $forChange = NULL, $forReadmision = NULL,$gset);
    }

    public function RemovePossible()
    {
        $delete_ossible = false;
        $delete_message = "";
        if (\Yii::$app->user->can('Registrar')) {
            // check if there is approved letter grade   //
            $srd2s = $this->semesterRegistration2Details;
            if ($srd2s && count($srd2s) > 0) {
                $delete_ossible = true;
                foreach ($srd2s as $srd2) {
                    if (!is_null($srd2->srd2GradeRel) && $srd2->srd2_gradeApproved != 0) {
                        $delete_ossible = false;
                        $delete_message = "Graded semester registration entry can not be deleted.";
                        break;
                    }
                }
            } else {
                $delete_ossible = true;
            }
        } else {
            $delete_ossible = false;
            $delete_message = "Non privelleged user.";
        }

        return [$delete_ossible, $delete_message];
    }

    public function getAcadYearHeader()
    {
        return $this->semregAcadYearRel->sch_year_desc . '-' . $this->semregSemesterRel->semester_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // semreg_last_ssn_relId
    public function getSemesterLastSsnRel()
    {
        return $this->hasOne(StudentSemesterStatusName::class, ['id' => 'semreg_last_ssn_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregAcadYearRel()
    {
        return $this->hasOne(\backend\models\SchoolYear::class, ['id' => 'semreg_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregGroupAssignmentRel()
    {
        return $this->hasOne(WGroupAssignment::class, ['gas_std_relId' => 'semreg_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregLastSsnRel()
    {
        return $this->hasOne(\backend\models\StudentSemesterStatusName::class, ['id' => 'semreg_last_ssn_relId']);
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
    public function getSemregCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregSemesterRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'semreg_semester_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregStdRel()
    {
        return $this->hasOne(\backend\models\Student::class, ['id' => 'semreg_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemregUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'semreg_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Detail()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['srd2_semreg_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details()
    {
        return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_semreg_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinousAssessments()
    {
        return $this->hasMany(ContinuousAssessment2::class, ['ca_semreg_relId' => 'id']);
    }
}
