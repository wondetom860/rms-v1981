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
 * @property string $entry_type
 * @property string $std_type
 * @property integer $std_dept_relId
 * @property integer $std_spec_relId
 * @property integer $std_clslvl_relId
 * @property integer $std_prog_relId
 * @property integer $std_admission_relId
 * @property integer $std_acadYear_relId
 * @property integer|null $std_sem_relId => Student Admission Semester. default values is 1=> semester_1
 * @property integer $std_group_realId
 * @property string $std_semester_status
 * @property string $std_created_at
 * @property integer $std_created_by
 * @property integer|null $std_admission_approved_by
 * @property string|null $std_admission_approved_at
 * @property string $std_updated_at
 * @property integer $std_updated_by
 * @property integer $std_deptChoice
 * @property integer $total_credit_hrs
 * @property integer $total_cgpa
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
 * @property User $stdAdmissionApprovedBy
 * @property User $stdUserRel
 * @property StudentGroupHistory[] $studentGroupHistories
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $studUserRelId = NULL;
    public const STATUS_ACTIVE = 1;

    public $_entry_types = [
        1 => 'Regular',
        2 => 'Transfer',
        3 => 'Imported',
        4 => 'Unknown',
    ];

    public static function tableName()
    {
        return 'student';
    }

    public function getCreditHourSumCalculated(): array
    /** calculated only */
    {
        $this->calculateGPAs();
        $graded_semregs = $this->getSemesterRegistrations()
            ->innerJoinWith('semregClslvlRel')
            ->where(['semreg_gradeCompleted' => 1])
            ->orderBy([
                'clslvl_num' => SORT_ASC,
                'semreg_semester_relId' => SORT_ASC
            ])
            ->all();
        $last_graded_semreg = $graded_semregs ? array_pop($graded_semregs) : NULL;
        if ($last_graded_semreg) {
            $total_credit_hr = $last_graded_semreg->semreg_totalCrdtHr;
            $total_gps = $last_graded_semreg->semreg_gradePoint_sum;

            if ($this->total_credit_hrs < $total_credit_hr) {
                $this->total_credit_hrs = $total_credit_hr;
                $this->total_cgpa = ($total_gps /  $total_credit_hr);
                $this->update();
            }
        }
        return [$total_credit_hr, ($total_gps /  $total_credit_hr)];
    }

    public function getCreditHourSum(): array
    {
        return $this->getCreditHourSumCalculated();
    }

    public function calculateTotalGPS()
    {
        $MGPA = 0;
        $total_credit_hr = 0;
        $total_gps = 0;
        $total_course_count = 0;
        $srd2s = SemesterRegistration2Detail::find()
            ->innerJoinWith('srd2SemregRel')
            ->andWhere(['semreg_std_relId' => $this->id])
            ->all();
        // $catModel = new CourseCatalog();
        if ($srd2s) {
            if ($srd2s) {
                foreach ($srd2s as $srd2) {
                    if ($srd2->courseIsRepeatedSomeWhere()) {
                        continue;
                    }
                    if ($srd2->srd2_grade_relId) {
                        $grade = $srd2->srd2GradeRel;
                        $course = $srd2->srd2CourseRel;
                        if ($grade->gs_gradePoint != 0) {
                            $total_credit_hr += $course->course_crdthr;
                            $total_gps += ($course->course_crdthr * $grade->gs_gradePoint);
                        }
                    }
                    $total_course_count += ($srd2->srd2_corptd_flag == 1) ? 0 : 1;
                }

                $gpa =  $total_credit_hr == 0 ? '-' : sprintf("%0.2f", ($total_gps / $total_credit_hr));

                return [$total_course_count, $total_credit_hr, $total_gps];
            }
        }
        return NULL;
    }

    public function calculateMGPA()
    {
        $MGPA = 0;
        $mTotal_credit_hr = 0;
        $mTotal_gps = 0;
        $mTotal_course_count = 0;
        $srd2s = SemesterRegistration2Detail::find()
            ->innerJoinWith('srd2SemregRel')
            ->andWhere(['semreg_std_relId' => $this->id])
            ->all();
        $catModel = new CourseCatalog();
        if ($srd2s) {
            $majorSrd2s = array_filter($srd2s, function ($srd2) use ($catModel) {
                foreach ($this->getCatalogEntries($srd2, $catModel) as $cocat) {
                    if ($cocat->cocat_courseMajor == 0) {
                        return false;
                    }
                }
                return true;
            });

            if ($majorSrd2s) {
                foreach ($majorSrd2s as $mSrd2) {
                    if ($mSrd2->courseIsRepeatedSomeWhere()) {
                        continue;
                    }
                    if ($mSrd2->srd2_grade_relId) {
                        $grade = $mSrd2->srd2GradeRel;
                        $course = $mSrd2->srd2CourseRel;
                        if ($grade->gs_gradePoint != 0) {
                            $mTotal_credit_hr += $course->course_crdthr;
                            $mTotal_gps += ($course->course_crdthr * $grade->gs_gradePoint);
                        }
                    }
                    $mTotal_course_count += 1;
                }

                return $mTotal_credit_hr == 0 ? '-' : sprintf("%0.2f", ($mTotal_gps / $mTotal_credit_hr));
            }
        }
    }

    // function is_major($srd2, $catModel)
    // {
    //     foreach (getCatalogEntries($srd2, $catModel) as $cocat) {
    //         if ($cocat->cocat_courseMajor == 0) {
    //             return false;
    //         }
    //     }
    //     return true;
    // }

    private function getCatalogEntries($srd2, $catModel)
    {
        $catalogCourses = $catModel->getCatalogCourse2($srd2->srd2GroupRel);
        $courseCatalogEntries = array_filter($catalogCourses, function ($tt) use ($srd2) {
            return $tt->cocat_course_relId == $srd2->srd2_course_relId;
        });

        return $courseCatalogEntries;
    }

    public function getLastEnrolledCourseDeptIds()
    {
        $lastSemreg = $this->getLastSemreg();

        $lastSemregCourseDeptIds = [];
        if ($lastSemreg) {
            foreach ($lastSemreg->semesterRegistration2Details as $srd2) { //coff2det->instRel->inst_dept_relId
                $inst = $srd2->getCourseInstructor();
                // instructor for a course can be selected from department other than course dept.
                if ($inst && $inst->coff2detInstRel) {
                    array_push($lastSemregCourseDeptIds, $inst->coff2detInstRel->inst_dep_relId);
                    if ($inst->coff2detTARel) {
                        array_push($lastSemregCourseDeptIds, $inst->coff2detTARel->inst_dep_relId);
                    }
                }
                array_push($lastSemregCourseDeptIds, $srd2->srd2CourseRel->course_dept_relId);
            }
        }
        // var_dump($lastSemregCourseDeptIds);exit;
        return $lastSemregCourseDeptIds;
    }

    public function getLastSemreg()
    {
        $lastSemester = $this->getSemesterRegistrations()
            ->joinWith('semregClslvlRel')
            ->orderBy([
                'clslvl_num' => SORT_ASC,
                'semreg_semester_relId' => SORT_ASC
            ])
            ->all();
        // $lastSemester[count($lastSemester) - 1]
        return $lastSemester ? array_pop($lastSemester) : NULL;
    }

    public function getAdmissionYear()
    {
        // returns year of admission in E.C
        $yre = '';
        $yr1 = '';
        $year = $this->stdAcadYearRel;
        if ($year) {
            $yr = explode('/', $year->sch_year_desc);
            $yr1 = $yr[0];
            $yre = $yr[0] - 7; //convert to Ethiopian calendar
        } else {
            $yr = explode('/', $this->std_regId);
            $yre = $yr[2];
            $yr1 = $yre + 7;
        }

        return ["{$yr1} (" . $yre . " E.C)", "{$yr1} (" . $yre . " ዓ.ም)"];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['std_user_relId', 'std_regId', 'std_dept_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_created_at', 'std_updated_at', 'std_updated_by', 'std_schlrshpType_relId', 'std_type'], 'required'],
            [['std_user_relId', 'std_dept_relId', 'std_spec_relId', 'std_clslvl_relId', 'std_prog_relId', 'std_admission_relId', 'std_acadYear_relId', 'std_group_realId', 'std_created_by', 'std_updated_by', 'std_schlrshpType_relId', 'std_sem_relId', 'std_acProgSched_relId', 'entry_type'], 'integer'],
            [['std_created_at', 'std_updated_at'], 'safe'],
            [['std_regId'], 'string', 'max' => 15],
            [['std_type'], 'string', 'max' => 20],
            [['std_semester_status'], 'string', 'max' => 40],
            [['std_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['std_acadYear_relId' => 'id']],
            [['std_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['std_sem_relId' => 'id']],
            [['std_admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['std_admission_relId' => 'id']],
            [['std_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['std_clslvl_relId' => 'id']],
            [['std_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_created_by' => 'id']],
            [['std_admission_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['std_admission_approved_by' => 'id']],
            [['std_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['std_dept_relId' => 'id']],
            [['std_group_realId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['std_group_realId' => 'id']],
            [['std_schlrshpType_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ScholarshipType::class, 'targetAttribute' => ['std_schlrshpType_relId' => 'id']],
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
            'std_user_relId' => Yii::t('rms', 'Student Name'),
            'std_regId' => Yii::t('rms', 'Reg. ID.'),
            'std_type' => Yii::t('rms', 'Student Type'),
            'std_sem_relId' => Yii::t('rms', 'Admission Semester'),
            'std_dept_relId' => Yii::t('rms', 'Department'),
            'std_spec_relId' => Yii::t('rms', 'Specialization'),
            'std_clslvl_relId' => Yii::t('rms', 'Current Class level'),
            'std_prog_relId' => Yii::t('rms', 'Program'),
            'std_admission_relId' => Yii::t('rms', 'Admission'),
            'std_acadYear_relId' => Yii::t('rms', 'Ac. Yr. of Admission'),
            'std_group_realId' => Yii::t('rms', 'Current Group'),
            'std_semester_status' => Yii::t('rms', 'Semester Status'),
            'std_created_at' => Yii::t('rms', 'Registered At'), //
            'entry_type' => Yii::t('rms', 'Entry Type'), //
            'std_created_by' => Yii::t('rms', 'Created By'),
            'std_updated_at' => Yii::t('rms', 'Updated At'),
            'std_updated_by' => Yii::t('rms', 'Updated By'),
            'std_admission_approved_by' => Yii::t('rms', 'Approved By'),
            'std_schlrshpType_relId' => Yii::t('rms', 'Scholarship Type'),
            'stdSchlrshpTypeRel.schType_name' => Yii::t('rms', 'Scholarship Type'),
            'stdUserRel.first_name' => Yii::t('rms', 'Full Name'),
            'stdDeptRel.dept_name' => Yii::t('rms', 'Department'),
            'stdSpecRel.spec_name' => Yii::t('rms', 'Specialization'),
            'stdClslvlRel.clslvl_name' => Yii::t('rms', 'Year'),
            'stdAdmissionRel.admission_name' => Yii::t('rms', 'Admission'),
            'stdProgRel.program_name' => Yii::t('rms', 'Program'),
            'std_acProgSched_relId' => Yii::t('rms', 'Admission Schedule'),
        ]; //
    }

    public function checkPendingStatusChange($apt)
    {
        return $this->getProfileChangeRequests()
            ->andWhere([
                'std_attribute' => $apt,
                'approval_status' => NULL
            ])->one();
    }

    public function regNumDuplicate()
    {
        return self::find()
            ->where([
                'std_regId' => $this->std_regId
            ])
            ->andWhere(' id <> ' . $this->id)
            ->count() > 0;
    }

    public function getRecentStudentGroup()
    {
        // 
        $sections = $this->getWGroupAssignments()->orderBy(['id' => SORT_DESC])->all();
        if ($sections && isset($sections[0])) {
            $gset = $sections[0]->gasGsetRel;
            if (($gset->gsActiveProgRel && $gset->gsActiveProgRel->status == 1) || !$this->std_group_realId) {
                $this->std_dept_relId = $gset->gs_dept_relId;
                $this->std_spec_relId = $gset->gs_spec_relId;
                $this->std_clslvl_relId = $gset->gs_clslvl_relId;
                $this->std_group_realId = $gset->id;
                if ($this->std_semester_status != 'ACTIVE') {
                    $this->std_semester_status = 'ACTIVE';
                }
                $this->update(false);
            }
            return $gset;
            // if student profile is not active, while having active group assignment: then update it.
        }

        return NULL;
    }
    public function generateRegistrarRequestAlert()
    {
        if (Yii::$app->user->can('UniversityRegistrar')) {
            return $this->find()
                ->where(['std_admission_approved_by' => null])
                ->count();
        }

        return 0;
    }

    public function getEntryType()
    {
        return $this->_entry_types[$this->entry_type];
    }

    public function getNewAdmittedStudents()
    {
        return $this->find()
            ->where(['std_admission_approved_by' => null])
            ->all();
    }

    public function getProgram()
    {
        return " " . $this->stdAdmissionRel->admission_name . "-" . $this->stdProgRel->program_name;
    }

    public function getDepartment()
    {
        //return Computer and Information Technology - {specialization if any}
        $spec = $this->std_spec_relId ? " ({$this->stdSpecRel->spec_name}) " : '';
        return " " . $this->stdDeptRel->dept_name . "-" . $spec;
    }


    public function getEducationRecord()
    {
        return $this->stdEducInfoRel;
    }

    public function getAdmissionProgram()
    {
        return $this->stdAdmissionRel->admission_name . ' ' . $this->stdProgRel->program_name;
    }


    public function getAdmittedFrom($stdId)
    {
        // get school name where the student attended grade 12
        // st_educ_info_relId
        $modelStudet = $this->findOne($stdId);
        $educationInfo = $modelStudet->stdEducInfoRel;

        if (!is_null($educationInfo)) {
            $secondarySchoolsAttended = $educationInfo->secondarySchoolAttendeds;
            if ($secondarySchoolsAttended && count($secondarySchoolsAttended) > 0) {
                foreach ($secondarySchoolsAttended as $SSAkey) {
                    $gradeLevels = json_decode($SSAkey->ssa_gradeLevels);
                    if (is_array($gradeLevels) && in_array(12, $gradeLevels)) {
                        return strtoupper($SSAkey->ssa_school_name);
                    }
                }
            }
        }
        return "-";
    }

    public function getAdmittedFrom2($stdId)
    {
        // get school name where the student attended grade 12
        // st_educ_info_relId
        $modelStudet = $this->findOne($stdId);
        $educationInfo = $modelStudet->stdEducInfoRel;

        if (!is_null($educationInfo)) {
            $secondarySchoolsAttended = $educationInfo->getSecondarySchoolAttendeds()
                ->andWhere([
                    'ssa_gradeLevels' => [12, '10+2']
                ])
                ->one();
            if ($secondarySchoolsAttended) {

                return strtoupper($secondarySchoolsAttended->ssa_school_name);
                // foreach ($secondarySchoolsAttended as $SSAkey) {
                //     $gradeLevels = json_decode($SSAkey->ssa_gradeLevels);
                //     if (is_array($gradeLevels) && in_array(12, $gradeLevels)) {
                //         return strtoupper($SSAkey->ssa_school_name);
                //     }
                // }
            }
        }
        return "-";
    }

    public function checkIfEligible($onlycount = null)
    {
        $report = "";
        $status = NULL;

        // get all group assignments in which student is assigned
        $gasss = $this->wGroupAssignments;
        // for each gsets, check course offerings made for that gset

        $courseOfferings = [];

        $srd2Model = SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $this->id
            ])
            ->one();

        // this check does only for enrolled courses, what if there is course offereg for the student group but not enrolled by the student?


        $ngsUnclrd = $srd2Model->getNGSFS($this, $this->stdGroupReal, 1);

        $ng = "";
        $fs = "";
        if ((isset($ngsUnclrd['ngs']) || isset($ngsUnclrd['Fs'])) && (!empty($ngsUnclrd['ngs']) || !empty($ngsUnclrd['Fs']))) {
            $status = "Not Eligible";

            if (isset($ngsUnclrd['ngs'])) {
                $ng .= $ngsUnclrd['ngs'] . "<br>";
            }
            if (isset($ngsUnclrd['Fs'])) {
                $fs .= $ngsUnclrd['Fs'] . "<br>";
            }
        } else {
            $status = 'Eligible';
        }

        if ($onlycount) {
            return [$ngsUnclrd, $status];
        }

        $report .= "<td>{$ng}</td><td>{$fs}</td>";
        return [$status, $report];
    }

    // private function checkIfEnrolled($courseId,$studentId){
    //     $srd2Model = new SemesterRegistration2Detail();

    //     return $srd2Model->getNGSFS($studentObj,$gsetObject);
    // }

    public function getFullName_am()
    {
        return $this->stdUserRel->getFullName_am();
    }

    public function getFullName()
    {
        return $this->stdUserRel->getFullName();
    }

    public function getProgramHeader()
    {
        // ."-".$this->stdClslvlRel->getClslvlDisp()
        return $this->stdAdmissionRel->admission_name[0] . $this->stdProgRel->program_name[0];
    }

    public function getYearOfAdmission()
    {
        return $this->stdAcadYearRel->sch_year_desc . "-" . $this->stdSemRel->semester_name;
    }

    public function stdIsGraduated()
    {
        // or check if assigned to graduating class section and completed semester as pass status
        return strtoupper($this->std_semester_status) == 'GRADUATED';
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdSchlrshpTypeRel()
    {
        return $this->hasOne(ScholarshipType::class, ['id' => 'std_schlrshpType_relId']);
    }

    /**
     * Summary of alreadyEnrolled
     * @param mixed $course
     * @return bool
     */
    public function alreadyEnrolled($course)
    {
        return self::find()
            ->innerJoin('semester_registration_2', 'student.id=semester_registration_2.semreg_std_relId')
            ->innerJoin('semester_registration_2_detail', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_course_relId' => $course->id,
                'semreg_std_relId' => $this->id,
            ])
            ->count() > 0;
    }

    public function requestedExitExam() {}

    public function calculateGPAs()
    {
        $i = 0;
        $semregs = $this->getSemesterRegistrations()
            ->orderBy([
                'semreg_acadYear_relId' => SORT_ASC,
                'semreg_semester_relId' => SORT_ASC,
                'semreg_clslvl_relId' => SORT_ASC,
            ])
            ->all();
        foreach ($semregs as $semreg) {
            $ret = $semreg->calculateGPA($i == 0 ? NULL : $semregs[$i - 1]);
            if ($ret == NULL) {
                break;
            }
            $i++;
        }
    }

    public function getEmergencyContactInfo()
    {
        return $this->hasOne(EmergencyContact::class, ['id' => 'std_emergency_contact_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStdEducInfoRel()
    {
        return $this->hasOne(EducationInformation::class, ['id' => 'st_educ_info_relId']);
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
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration2::class, ['semreg_std_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // self sponsored payment history

    public function getSelfSponsoredPaymentHistories()
    {
        return $this->hasMany(SelfSponsoredPaymentHistory::class, ['ssph_std_relId' => 'id']);
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
    public function getStdSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'std_sem_relId']);
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
    public function getStdEmergencyContactRel()
    {
        return $this->hasMany(EmergencyContact::class, ['id' => 'std_emergency_contact_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileChangeRequests()
    {
        return $this->hasMany(StudentProfileChange::class, ['std_relId' => 'id']);
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
    public function getStdGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'std_group_realId']);
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
    public function getNationality()
    {
        return $this->stdUserRel->nationalityRel;
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
    public function getWGroupAssignments()
    {
        return $this->hasMany(WGroupAssignment::class, ['gas_std_relId' => 'id']);
    }
}
