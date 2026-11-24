<?php

namespace backend\models;


use Yii;
use common\components\CommonControllerFunctions as CCF;
use \backend\models\CourseOffering2;
use common\components\CommonControllerFunctions;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;


/**
 * This is the model class for table "course_catalog".
 *
 * @property integer $id
 * @property integer $cocat_admission_relId
 * @property integer $cocat_program_relId
 * @property integer $cocat_dept_relId
 * @property integer $cocat_clslvl_relId
 * @property integer $cocat_spec_relId
 * @property integer $cocat_sem_relId 
 * @property integer $cocat_status
 * @property integer $cocat_created_by
 * @property string $cocat_created_at
 * @property integer $cocat_updated_by
 * @property string $cocat_updated_at
 *
 * @property CocatDetail[] $cocatDetails
 * @property Admission $cocatAdmissionRel
 * @property ClassLevel $cocatClslvlRel
 * @property User $cocatCreatedBy
 * @property Department $cocatDeptRel
 * @property Program $cocatProgramRel
 * @property Semester $cocatSemRel
 * @property Specialization $cocatSpecRel
 * @property User $cocatUpdatedBy
 * @property CourseOffering2[] $courseOfferings
 * @property SemesterRegistration[] $semesterRegistrations
 */

/**
 * This is the model class for table "w_group_setup".
 *
 * @property integer $id
 * @property integer $gs_group_relId
 * @property integer $gs_adm_relId
 * @property integer $gs_prog_relId
 * @property integer $gs_acadYear_relId
 * @property integer $gs_dept_relId
 * @property integer $gs_active_prog_relId
 * @property integer $gs_clslvl_relId
 * @property integer $gs_spec_relId
 * @property integer $gs_sem_relId
 * @property integer $gs_created_by
 * @property integer $gs_updated_by
 * @property string $gs_created_at
 * @property string $gs_updated_at
 * @property integer $gs_status
 * @property integer $gs_size
 * @property string $remark
 * @property string $gs_internship
 *
 * @property WGroupAssignment[] $wGroupAssignments
 * @property SchoolYear $gsAcadYearRel
 * @property Admission $gsAdmRel
 * @property ClassLevel $gsClslvlRel
 * @property User $gsCreatedBy
 * @property Department $gsDeptRel
 * @property Program $gsProgRel
 * @property Semester $gsSemRel
 * @property Specialization $gsSpecRel
 * @property User $gsUpdatedBy
 * @property WGroup $gsGroupRel
 */
class WGroupSetup extends \yii\db\ActiveRecord
{

    public $grpStatus = null;
    public $prevAcadYear = null;
    public $prevSem = null;
    public $prevGroup = NULL;
    public $students = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_group_setup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gs_group_relId', 'gs_adm_relId', 'gs_prog_relId', 'gs_acadYear_relId', 'gs_dept_relId', 'gs_clslvl_relId', 'gs_sem_relId', 'gs_created_by', 'gs_updated_by', 'gs_created_at', 'gs_updated_at'], 'required'],
            [['gs_group_relId', 'gs_adm_relId', 'gs_prog_relId', 'gs_acadYear_relId', 'gs_dept_relId', 'gs_clslvl_relId', 'gs_spec_relId', 'gs_sem_relId', 'gs_created_by', 'gs_updated_by', 'gs_status', 'gs_internship', 'gs_active_prog_relId'], 'integer'],
            [['gs_created_at', 'gs_updated_at'], 'safe'],
            [['remark'], 'string'],
            [['gs_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['gs_acadYear_relId' => 'id']],
            [['gs_active_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::class, 'targetAttribute' => ['gs_active_prog_relId' => 'id']],
            [['gs_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['gs_adm_relId' => 'id']],
            [['gs_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['gs_clslvl_relId' => 'id']],
            [['gs_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gs_created_by' => 'id']],
            [['gs_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['gs_dept_relId' => 'id']],
            [['gs_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['gs_prog_relId' => 'id']],
            [['gs_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['gs_sem_relId' => 'id']],
            [['gs_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['gs_spec_relId' => 'id']],
            [['gs_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gs_updated_by' => 'id']],
            [['gs_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroup::class, 'targetAttribute' => ['gs_group_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gs_active_prog_relId' => Yii::t('rms', 'Academic Program'),
            'gs_group_relId' => Yii::t('rms', 'Group'),
            'gs_adm_relId' => Yii::t('rms', 'Admission'),
            'gs_prog_relId' => Yii::t('rms', 'Program'),
            'gs_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'gs_dept_relId' => Yii::t('rms', 'Department'),
            'gs_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'gs_spec_relId' => Yii::t('rms', 'Specialization'),
            'gs_sem_relId' => Yii::t('rms', 'Semester'),
            'gs_internship' => Yii::t('rms', 'Internship Group?'),
            'gs_created_by' => Yii::t('rms', 'Created By'),
            'gs_updated_by' => Yii::t('rms', 'Updated By'),
            'gs_created_at' => Yii::t('rms', 'Created At'),
            'gs_updated_at' => Yii::t('rms', 'Updated At'),
            'gs_status' => Yii::t('rms', 'Status'),
            'gs_size' => Yii::t('rms', 'Size'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * Summary of getCredits
     * @param mixed $stdObject
     * @return array
     * return has ti form of [0 => [15-23],1=>11]
     */
    public function getCredits($stdObject)
    {
        $total_credits = 0;
        $min_credit_load = 0;
        $max_credit_load = 0;
        $possible_credits = 0;
        $possible = false;
        $maxAllowedObject = CommonControllerFunctions::getMaxAllowedCreditHrPerSemester($this, $stdObject);
        if ($maxAllowedObject) {
            $max_credit_load = $maxAllowedObject->src_max_crdthr;
            $min_credit_load = $maxAllowedObject->src_min_crdthr;
        }
        $coff2s = $this->getCourseOfferings()->all();
        $preqModel = new PreRequisitController();
        // check if already inrolled toe course while iterating over offered courses.

        if ($coff2s) {
            foreach ($coff2s as $coff2) {
                foreach ($coff2->courseOffering2Details as $coff2Detail) {
                    // check if enrolled
                    $course = $coff2Detail->coff2detCourseRel;
                    if ($stdObject->alreadyEnrolled($course)) {
                        continue;
                    }
                    $pass = $preqModel->safeOfPrerequisit($course, $stdObject, $this);
                    if ($pass && $pass[0]) {
                        $possible_credits += $course->course_crdthr;
                    }
                    $total_credits += $course->course_crdthr;
                }
            }

            $possible = $possible_credits >= $min_credit_load;
        }

        return ["[{$min_credit_load}-{$max_credit_load}]", "{$possible_credits}/{$total_credits}", $possible];
    }

    public function studentEnrolledCourse($studentId){
        $count = SemesterRegistration2::find()
            ->where([
                'semreg_std_relId' => $studentId,
                'semreg_acadYear_relId' => $this->gs_acadYear_relId,
                'semreg_semester_relId' => $this->gs_sem_relId,
                'semreg_clslvl_relId' => $this->gs_clslvl_relId,
            ])
            ->count();
        return $count > 0;
    }


    public function studentEnrolledCourse2($studentId)
    {
        // all added course case fails here:

        $count = $this->getSemesterRegistrations()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->andFilterWhere(['semreg_std_relId' => $studentId])
            ->count('semester_registration_2_detail.id');
        // var_dump(['gsetId: '=>$this->id,'Course Count:'=>$count,'Test: ' => $count > 0]);
        return $count > 0;
    } 

    public function getAcadYearHeader()
    {
        if ($this->gs_active_prog_relId) {
            return $this->gsActiveProgRel->academicYearRel->getAcHeader();
        } else {
            return $this->gsAcadYearRel->sch_year_desc . "-" . $this->gsSemRel->semester_name;
        }
    }

    public function getAcadYearHeader2()
    {
        return $this->gsAcadYearRel->sch_year_desc . "-" . $this->gsSemRel->semester_name;
    }

    public function prevGset()
    {
        $gsets = self::find()
            ->innerJoin('school_year', 'school_year.id=w_group_setup.gs_acadYear_relId')
            ->innerJoin('semester', 'semester.id=w_group_setup.gs_sem_relId')
            ->where([
                'gs_adm_relId' => $this->gs_adm_relId,
                'gs_prog_relId' => $this->gs_prog_relId,
                'gs_dept_relId' => $this->gs_dept_relId,
            ])
            ->andWhere('w_group_setup.id <> ' . $this->id)
            ->orderBy([
                'school_year.sch_year_desc' => SORT_DESC,
                'semester.semester_name' => SORT_DESC
            ])
            ->all();
        if ($gsets && count($gsets) > 0) {
            return $gsets[0];
        }

        return NULL;
    }

    // public function getStudentGroup
    public function getDeptGsets($deptObj, $acadYearId, $semId)
    {
        return $this->find()
            ->where([
                'gs_dept_relId' => $deptObj->id,
                'gs_acadYear_relId' => $acadYearId,
                'gs_sem_relId' => $semId
            ])
            ->all();
    }

    public function checkAcLink()
    {
        if (is_null($this->gs_active_prog_relId)) {
            $acps = AcActivePrograms::find()
                ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
                ->where([
                    'school_year' => $this->gsAcadYearRel->sch_year_desc,
                    'semester' => $this->gsSemRel->semester_name,
                    'admission_relId' => $this->gs_adm_relId,
                    'program_relId' => $this->gs_prog_relId,
                    'dept_relId' => $this->gs_dept_relId,
                    'class_level_relId' => $this->gs_clslvl_relId,
                    'spec_relId' => $this->gs_spec_relId,
                ])
                ->all();
            // print_r($acps);exit;

            if (count($acps) == 1) {
                $this->gs_active_prog_relId = $acps[0]->id;
                $this->update();

                // check all course offerings for this group:

            } else {
                if (count($acps) == 0) {
                    // create active program from gsetup:
                    // this case happened while registering transfer students by registrar:
                    $this->createAcProgram();
                    return "<p class='bg-warning' style='font-style:italic; font-size:8pt;'>No Ac. Program set.</p>";
                } else if (count($acps) > 0) {
                    return "<p class='bg-danger' style='font-style:italic; font-size:8pt;'>Multiple active program entries found.</p>";
                }
            }

            // if count is more than 1, thiere is error in filtering. 

        } else {
            Yii::$app->db->createCommand()
                ->update(
                    'course_offering2',
                    ['coff2_acProg_relId' => $this->gs_active_prog_relId],
                    ['coff2_group_relId' => $this->id]
                )
                ->execute();
        }


        return $this->gsActiveProgRel->getClassLevelHeader() . "<br>{$this->gsActiveProgRel->getAcadYearHeader()}";
    }

    public function suchExists()
    {
        $exists = self::find()
            ->where([
                'gs_acadYear_relId' => $this->gs_acadYear_relId,
                'gs_sem_relId' => $this->gs_sem_relId,
                'gs_adm_relId' => $this->gs_adm_relId,
                'gs_prog_relId' => $this->gs_prog_relId,
                'gs_clslvl_relId' => $this->gs_clslvl_relId,
                'gs_dept_relId' => $this->gs_dept_relId
            ])->one();
        if ($exists) {
            $this->id = $exists->id;
            return true;
        }

        return false;
    }

    private function createAcProgram()
    {
        $acp = new AcActivePrograms();

        $acy = AcAcademicYear::find()->where([
            'school_year' => $this->gsAcadYearRel->sch_year_desc,
            'semester' => $this->gsSemRel->semester_name,
        ])->one();

        $acp->academic_year_relId = $acy->id;
        $acp->admission_relId = $this->gs_adm_relId;
        $acp->program_relId = $this->gs_prog_relId;
        $acp->dept_relId = $this->gs_dept_relId;
        $acp->class_level_relId = $this->gs_clslvl_relId;
        $acp->spec_relId = $this->gs_spec_relId;
        $acp->semester_relId = $this->gs_sem_relId;

        $acp->created_by = Yii::$app->user->id;
        $acp->created_at = time();

        $acp->getData();

        $acp->save();
        $this->gs_active_prog_relId = $acp->id;
        $this->update();
    }

    // public function getCatalog(){
    //     return CourseCatalog::find()
    //         ->where([
    //             'cocat_admission_relId' => $this->gs_adm_relId,
    //             'cocat_admission_relId' => $this->gs_adm_relId,
    //         ])
    //         ->andWhere()
    //         ->all();
    // }

    public function getGsets($admId, $progId, $deptId, $clslvlId, $acadYear, $semId, $spec = NULL)
    {
        $gset = $this->find()
            ->where([
                'gs_adm_relId' => $admId,
                'gs_prog_relId' => $progId,
                'gs_acadYear_relId' => $acadYear,
                'gs_sem_relId' => $semId,
            ]);
        if (!is_null($clslvlId)) {
            $gset = $gset->andWhere(['gs_clslvl_relId' => $clslvlId]);
        }
        if (isset($deptId) && !empty($deptId)) {
            $gset = $gset->andWhere(['gs_dept_relId' => $deptId]);
        }
        if (isset($spec) && !empty($spec)) {
            $gset = $gset->andWhere(['gs_spec_relId' => $spec]);
        }

        return $gset->all();
    }

    public function getGsDisp($i = 1)
    {
        return $i ?
            $this->gsDeptRel->dept_code . "<i>[Sem:{$this->gsSemRel->semester_name},{$this->gsClslvlRel->getClslvlDisp($this)} - {$this->gsProgRel->program_name}]</i>"
            :
            $this->gsDeptRel->dept_code . "[{$this->gsAdmRel->admission_name[0]}{$this->gsProgRel->program_name[0]}({$this->gsClslvlRel->clslvl_num}-{$this->gsSemRel->semester_name})]";
    }

    public function getPrevStudentGset($stdObj, $cGset)
    {
        if (CCF::specialCaseCheckP($cGset)) {
            return NULL;
        }
        if (is_object($stdObj) && is_object($cGset)) {
            $prevClslvl = NULL;
            $prevSem = NULL;
            $prevAcadYear = $cGset->gsAcadYearRel->id;
            if ($cGset->gsSemRel->semester_name == 'I') {
                $prevClslvl  =  $cGset->gsClslvlRel->clslvl_num - 1;
                $prevAcadYear = $cGset->gs_acadYear_relId - 1;
                $prevSem = $cGset->gsProgRel->program_name == 'Extension' ? 'Summer' : 'II';
            } else if ($cGset->gsSemRel->semester_name == 'II') {
                $prevClslvl  =  $cGset->gsClslvlRel->clslvl_num;
                $prevSem = 'I';
            } else if ($cGset->gsSemRel->semester_name == 'Summer') {
                $prevClslvl  =  $cGset->gsClslvlRel->clslvl_num;
                $prevSem = 'II';
            }

            $prevSemObj = Semester::findOne(['semester_name' => $prevSem]);
            // 'clslvl_admission_relId'=>$cGset->gs_adm_relId,
            $prevClslvlObj = ClassLevel::findOne([
                'clslvl_num' => $prevClslvl,
                'clslvl_program_relId' => $cGset->gs_prog_relId,
            ]);

            return CCF::getStudentGroupHistoryPublic($prevClslvlObj->id, $prevSemObj->id, $stdObj->id);
        }
        return NULL;
    }

    public static function getCurrentDepGroups($instDept)
    { //all group setups made for the department yet.
        return WGroupSetup::find()
            ->where([
                'gs_dept_relId' => $instDept,
            ])
            ->all();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWGroupAssignments()
    {
        return $this->hasMany(WGroupAssignment::class, ['gas_gset_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'gs_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'gs_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'gs_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsActiveProgRel()
    {
        return $this->hasOne(AcActivePrograms::class, ['id' => 'gs_active_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gs_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'gs_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'gs_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'gs_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'gs_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gs_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsGroupRel()
    {
        return $this->hasOne(WGroup::class, ['id' => 'gs_group_relId']);
    }

    // getSemesterRegistrations

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering2::class, ['coff2_group_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_group_relId' => 'id']);
        // // course added students information differs here.
        // $srd2s = $this->hasMany(SemesterRegistration2Detail::class, ['srd2_group_relId' => 'id'])->all();
        // $semreg = $srd2s[0] ? $srd2s[0]->srd2SemregRel : NULL;
        // return $semreg ? $semreg->getSemesterRegistration2Details() : NULL;
    }


    /**
     * returns students enrolled courses who are assigned to this gset.
     * @return Yii\db\ActiveQuery
     */
    public function getSemregs()
    {
        return SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
            ->where([
                'semreg_acadYear_relId' => $this->gs_acadYear_relId,
                'semreg_semester_relId' => $this->gs_sem_relId,
                'semreg_clslvl_relId' => $this->gs_clslvl_relId,
                'student.std_group_realId' => $this->id
            ]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSemesterRegistrations2($stdId)
    {
        return SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_acadYear_relId' => $this->gs_acadYear_relId,
                'semreg_semester_relId' => $this->gs_sem_relId,
                'semreg_clslvl_relId' => $this->gs_clslvl_relId,
                'semreg_std_relId' => $stdId
            ]);
    }
    public function getStudentSemesterRegistrations($stdId)
    {
        // return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_group_relId' => 'id']);
        // // course added students information differs here.
        $srd2s = $this->hasMany(SemesterRegistration2Detail::class, ['srd2_group_relId' => 'id'])
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->andFilterWhere(['semreg_std_relId' => $stdId])
            ->all();
        $semreg = isset($srd2s[0]) ? $srd2s[0]->srd2SemregRel : NULL;
        return $semreg ? $semreg->getSemesterRegistration2Details() : $semreg;
    }
}
