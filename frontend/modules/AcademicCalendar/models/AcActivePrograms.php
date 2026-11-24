<?php

namespace frontend\modules\AcademicCalendar\models;

use Yii;
use backend\models\Program;
use backend\models\Department;
use backend\models\ClassLevel;
use backend\models\Specialization;
use backend\models\Admission;
use backend\models\Semester;
use backend\models\User;
use backend\models\Student;
use backend\models\CourseOfferingTemp;
use backend\models\CourseOffering2;
use backend\models\WGroupSetup;
use backend\models\CourseCatalog;
use backend\models\SemesterRegistration;
use backend\models\SemesterRegistration2;
use backend\models\SemesterRegistration2Detail;

/**
 * This is the model class for table "ac_active_programs".
 *
 * @property int $id
 * @property int $academic_year_relId
 * @property int $admission_relId
 * @property int $program_relId
 * @property int $dept_relId
 * @property int $class_level_relId
 * @property int $semester_relId
 * @property int|null $spec_relId
 * @property int $status
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data 
 * @property string|null $remark
 * @property int|0 $program_schedule_used
 * @property int|0 $graduate_class
 * @property int|1 $entry_type
 * @property int|0 $on_internship
 * @property int|0 $sctions_count
 *
 * @property AcAcademicYear $academicYearRel
 * @property Admission $admissionRel
 * @property ClassLevel $classLevelRel
 * @property User $createdBy
 * @property Department $deptRel
 * @property Program $programRel
 * @property Semester $semesterRel
 * @property Specialization $specRel
 * @property User $updatedBy
 * @property AcProgramSchedule[] $acProgramSchedules
 */
class AcActivePrograms extends \yii\db\ActiveRecord
{

    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 0;
    public const STATUS_UPNEXT = -1;

    public const ENTRY_TYPE_TRANSFER = 2;
    public const ENTRY_TYPE_REGULAR = 1;

    public $group_setups;
    public $course_offerings;
    public $student_count;

    public $bgs = [1 => 'success', 0 => 'danger', -1 => 'warning'];
    public $stss = [1 => 'Active', 0 => 'Closed', -1 => 'Upnext'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ac_active_programs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['academic_year_relId', 'admission_relId', 'program_relId', 'dept_relId', 'class_level_relId', 'semester_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['academic_year_relId', 'admission_relId', 'program_relId', 'dept_relId', 'class_level_relId', 'semester_relId', 'spec_relId', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'program_schedule_used', 'graduate_class', 'on_internship'], 'integer'],
            [['data', 'remark'], 'string'],
            [['academic_year_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['academic_year_relId' => 'id']],
            [['admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['admission_relId' => 'id']],
            [['class_level_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['class_level_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dept_relId' => 'id']],
            [['program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_relId' => 'id']],
            [['semester_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['semester_relId' => 'id']],
            [['spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['spec_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'academic_year_relId' => Yii::t('rms', 'Academic Year'),
            'admission_relId' => Yii::t('rms', 'Admission'),
            'program_relId' => Yii::t('rms', 'Program'),
            'dept_relId' => Yii::t('rms', 'Department'),
            'class_level_relId' => Yii::t('rms', 'Class Year'),
            'semester_relId' => Yii::t('rms', 'Semester'),
            'spec_relId' => Yii::t('rms', 'Specialization'),
            'status' => Yii::t('rms', 'Status'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'on_internship' => Yii::t('rms', 'Internship'),
            'remark' => Yii::t('rms', 'Remark'),
            'group_setups' => Yii::t('rms', 'Groups/Section'),
            'course_offerings' => Yii::t('rms', 'Course Offered'),
        ];
    }

    public function getGraduateStudentList2()
    {
        $all_courses = $this->getAllCourses();
        $all_semregs = $this->getAllSemregs();
        $all_students = $this->getAllStudents();
        $one_srd2 = SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId' => $all_semregs[0]['SEMRID']])
            ->one();

        return [
            'all_courses' => $all_courses,
            'all_semregs' => $all_semregs,
            'acadYear' => $this->academicYearRel,
            'all_students' => $all_students,
            'one_srd2' => $one_srd2,
            'acp' => $this
        ];
    }

    private function getAllStudents()
    {
        $std = $this->getRefStudent();
        if (!$std) {
            return [];
        }
        return Student::find()
            ->where(['std_acadYear_relId' => $std->std_acadYear_relId])
            ->andWhere(['std_prog_relId' => $this->program_relId])
            ->andWhere(['std_dept_relId' => $this->dept_relId])
            ->orderBy(['std_regId' => SORT_ASC])
            ->all();
    }

    private function getAllSemregs()
    {
        $std = $this->getRefStudent();
        if (!$std) {
            return [];
        }
        $sql = "
            SELECT
                semester_registration_2.id as SEMRID,
                semester_registration_2_detail.id as SRD2ID,
                student.std_regId as REGID,
                concat(school_year.sch_year_desc,'-',semester.semester_name) as ACY,
                course.course_code as COURSECODE,
                course.course_crdthr as CREDITHOUR,
                semester_registration_2.semreg_std_relId as SEMREG_STD_RELID
            FROM
                semester_registration_2
                inner join student on student.id=semester_registration_2.semreg_std_relId
                inner join semester_registration_2_detail on semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId
                inner join course on course.id=semester_registration_2_detail.srd2_course_relId
                inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
                inner join semester on semester.id=semester_registration_2.semreg_semester_relId
            WHERE
                student.std_acadYear_relId={$std->std_acadYear_relId} AND
                student.std_prog_relId={$this->program_relId} AND
                student.std_admission_relId={$this->admission_relId} AND
                student.std_dept_relId={$this->dept_relId}
            ORDER BY
                semreg_totalCrdtHr DESC,
                semreg_CGPA DESC,
                student.id,
                school_year.id,
                semester.id
            ;
        ";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    private function getRefStudent()
    {
        $acy = $this->academicYearRel->schoolYearRel->id;
        // $program_duration_in_years = $this->programRel->prog_duration_in_years;
        $gset = $this->groupSetups[0] ?? NULL;
        $gAssignments = $gset ? $gset->wGroupAssignments : NULL;
        $std = NULL;
        foreach($gAssignments as $gas){
            $stdd = SemesterRegistration2::find()
                ->innerJoin('w_group_assignment', 'semester_registration_2.semreg_std_relId=w_group_assignment.gas_std_relId')
                ->where(['semreg_std_relId' => $gas->gas_std_relId])
                ->orderBy(['semester_registration_2.semreg_CGPA' => SORT_DESC])
                ->one();
            $stdd = $stdd->semregStdRel;
            $lastSemreg = $stdd->getLastSemreg();
            if($lastSemreg && $lastSemreg->semreg_acadYear_relId == $acy){
                $std = $stdd;
                break;
            }
        }

        return $std;
    }
    private function getAllCourses()
    {
        $std = $this->getRefStudent();
        if (!$std) {
            return [];
        }
        // var_dump($std);exit;
        $sql = "
            SELECT
                semester_registration_2.id as SEMRID,
                concat(school_year.sch_year_desc,'-',semester.semester_name) as ACY,
                course.id AS COURSEID,
                course.course_title as COURSETITLE,
                course.course_code as COURSECODE,
                course.course_crdthr as CREDITHOUR,
                semester_registration_2.semreg_std_relId as SEMREG_STD_RELID
            FROM
                semester_registration_2
                inner join student on student.id=semester_registration_2.semreg_std_relId
                inner join semester_registration_2_detail on semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId
                inner join course on course.id=semester_registration_2_detail.srd2_course_relId
                inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
                inner join semester on semester.id=semester_registration_2.semreg_semester_relId
            WHERE
                student.std_acadYear_relId={$std->std_acadYear_relId} AND
                student.std_prog_relId={$this->program_relId} AND
                student.std_admission_relId={$this->admission_relId} AND
                student.std_dept_relId={$this->dept_relId}
            GROUP BY
                course.id
            ORDER BY
                school_year.id,
                semester.id,
                course.course_dept_relId,
                course.course_code,
                course.course_title
            ;
        ";
        $command = Yii::$app->db->createCommand($sql);
        $courses = $command->queryAll();
        return $courses;
    }

    public function offerCourse($catIds)
    {
        $cocatIds = json_decode($catIds);

        if ($cocatIds && count($cocatIds)) {
            $catIDS = [];
            $scount = 0;
            $fcount = 0;
            $total = count($cocatIds);
            foreach ($cocatIds as $cocatId) {
                foreach ($this->coff2s as $coff2) {
                    if (is_null($coff2->coff2_cocatIds)) {
                        $coff2->coff2_cocatIds = json_encode($cocatIds);
                        if ($coff2->update(false)) {
                            $scount += count($cocatIds);
                            continue;
                        } else {
                            return json_encode($coff2->getErrors());
                        }
                    }
                    if ($coff2->coff2_cocatIds && in_array($cocatId, json_decode($coff2->coff2_cocatIds, true))) {
                        $scount++;
                    } else {
                        $COCATIDS = json_decode($coff2->coff2_cocatIds, true);
                        // to indicate that pending course offering is there for approval by the 
                        // $coff2->coff2_lock_status = 0;
                        $coff2->coff2_cocatIds = json_encode(array_unique(array_merge($COCATIDS, [$cocatId])));
                        $coff2->coff2_updated_at = time();
                        $coff2->coff2_updated_by = \Yii::$app->user->id;
                        $coff2->getData();

                        $coff2->update(false) ? $scount++ : $fcount++;
                    }

                    // check for approval pending status:
                    if ($coff2->coff2_lock_status) { //approved....
                        // distribute course if group assignment is already made before:
                        $coff2->coff2_lock_status = 0;
                        $coff2->distributeCoff2();
                    } else {
                        $coff2->distributeCoff2();
                        // notify the offering pends for approval.
                        return "Course offering is completed, it will available for enrollment after Registrar/Academic Dean approval.";
                    }
                }
            }
            return "{$scount}/{$total} success.";
        } else {
            return "No course selected.";
        }
    }

    public function checkIfOffered($catalogId)
    {
        // check either in 
        // easily you can call [getCourseOffered] of this object to get offered course objects:
        // 1. courseOffering2.coff2CocatIds
        // 2.courseOffering2Detail.coff2det_cacatId
        $coff2s = $this->coff2s;
        if ($coff2s && count($coff2s) > 0) {
            foreach ($coff2s as $coff2) {
                $coff2->distributeCoff2();
                $coffTemp = $this->coffTemp;
                if ($coff2->coff2_cocatIds && in_array($catalogId, json_decode($coff2->coff2_cocatIds, true))) {
                    // check if it is in the coffTemp entry
                    $tempCocatIds = json_decode($coffTemp->cot_cocat_IDS, true);
                    if (!in_array($catalogId, $tempCocatIds)) {
                        $tempCocatIds[] = $catalogId;
                        $coffTemp->cot_cocat_IDS = json_encode($tempCocatIds);
                        $coffTemp->save();
                    }
                    return true;
                } else {
                    // get from coffTemp and copy to coff2
                    if ($coffTemp) {
                        $coff2->coff2_cocatIds = $coffTemp->cot_cocat_IDS;
                        $coff2->update(false);
                        return in_array($catalogId, json_decode($coff2->coff2_cocatIds, true));
                    } else {
                        // coppy cocatIds from temp to coff2
                        $coff2->copyCocatIdsFromTemp($this);
                    }
                    return in_array($catalogId, json_decode($coff2->coff2_cocatIds, true));
                }
            }
        }

        // else{
        //     $sections = $this->groupSetups;
        //     if ($sections && count($sections) > 0) {
        //         $modelCoff2 = new CourseOffering2();
        //         $modelCoff2->coff2_acadYear_relId = $this->academicYearRel->id;
        //         $modelCoff2->coff2_sem_relId = $this->semesterRel->id;
        //         $modelCoff2->coff2_group_relId = $section[0]->id;
        //         $modelCoff2->coff2_acProg_relId = $this->id;

        //         $modelCoff2->save();
        //         unset($modelCoff2);
        //         checkIfOffered($catalogId);
        //     }else{
        //         return "No section setup made for your department, please try again later.";
        //     }
        // }
    }

    public function getCatalogCourses()
    {
        $catCourses = CourseCatalog::find()
            ->where([
                'cocat_admission_relId' => $this->admission_relId,
                'cocat_program_relId' => $this->program_relId,
                'cocat_dept_relId' => $this->dept_relId,
            ])
            ->andWhere([
                'OR',
                ['cocat_clslvl_relId' => $this->class_level_relId],
                ['cocat_clslvl_relId' => NULL]
            ])->andWhere([
                'OR',
                ['cocat_sem_relId' => $this->semester_relId],
                ['cocat_sem_relId' => NULL]
            ]);

        if (!is_null($this->spec_relId)) {
            $catCourses->andWhere([
                'OR',
                ['cocat_spec_relId' => $this->spec_relId],
                ['cocat_spec_relId' => NULL]
            ]);
        }
        // only active catalog entry should be elligible for offering.
        $catCourses->andWhere(['cocat_status' => 1]);

        return $catCourses->all();
    }

    public function getStudents($modelActiveProgram, $gsetModel, $filterMode, $loadOnlyUngrouped, $activeAcademicYear)
    {
        $student = Student::find();
        if ($filterMode == 3) { //load prev grouped studets: 
            $prevGset = null;
            $prevGsetIds = [];
            if ($activeAcademicYear) {
                $prevAcademicYear = $activeAcademicYear->getImmidiatePrevAcademicYear($gsetModel);
                if (is_string($prevAcademicYear)) {
                    return $prevAcademicYear;
                }
                // filter groupsets made for that academic year:
                return $this->getPrevGsetStds($prevAcademicYear, $gsetModel);
            } else {
                return "Academic year not scheduled";
            }
        } else if ($filterMode == 4) { //load readmitted students: 

        }
    }

    public function getPrevGsetStds($prevAcademicYear, $gsetModel)
    {
        // $prev_class_level = $gsetModel->gsSemRel->semester_name == 'I' ? 
        $gass = \backend\models\WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
            ->innerJoin('ac_active_programs', 'ac_active_programs.id=w_group_setup.gs_active_prog_relId')
            ->where([
                'admission_relId' => $gsetModel->gs_adm_relId,
                'program_relId' => $gsetModel->gs_prog_relId,
                'dept_relId' => $gsetModel->gs_dept_relId,
            ])
            ->andWhere([
                'gs_acadYear_relId' => $prevAcademicYear->schoolYearRel->id,
                'gs_sem_relId' => $prevAcademicYear->semesterRel->id,
            ])
            ->andWhere(' gas_gset_relId <> ' . $gsetModel->id)
            ->all();

        $stdIds = [];

        if ($gass && count($gass) > 0) {
            foreach ($gass as $gas) {
                $stdIds[] = $gas->gas_std_relId;
            }

            return Student::find()->where(['student.id' => $stdIds])->andWhere(" std_semester_status <> 'GRADUATED' ");
        } else {
            // check if the group setup was made in that academic year for the department : 

            return "No Program was set for this department in previous academic year.({$prevAcademicYear->getAcHeader()})";
        }
    }

    public static function linkGsets()
    {
        $nonLinkedGsets = WGroupSetup::find()->where(['gs_active_prog_relId' => NULL])->all();

        if ($nonLinkedGsets && count($nonLinkedGsets) > 0) {
            foreach ($nonLinkedGsets as $nlGset) {
                $program = self::find()
                    ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
                    ->where([
                        'school_year' => $nlGset->gsAcadYearRel->sch_year_desc,
                        'semester' => $nlGset->gsSemRel->semester_name,
                        'admission_relId' => $nlGset->gs_adm_relId,
                        'program_relId' => $nlGset->gs_prog_relId,
                        'dept_relId' => $nlGset->gs_dept_relId,
                        'class_level_relId' => $nlGset->gs_clslvl_relId,
                        'spec_relId' => $nlGset->gs_spec_relId,
                    ])
                    ->all();
                if ($program && count($program) > 0) {
                    if (count($program) == 1) {
                        $nlGset->gs_active_prog_relId = $program[0]->id;
                        $nlGset->update();
                        continue;
                    } else {
                        // more such programs exists...
                        continue;
                    }
                } else {
                    $model = new AcActivePrograms();

                    // getAcademicYearEntry:
                    $academicYear = AcAcademicYear::find()
                        ->where([
                            'school_year' => $nlGset->gsAcadYearRel->sch_year_desc,
                            'semester' => $nlGset->gsSemRel->semester_name,
                        ])
                        ->all();
                    if ($academicYear && count($academicYear) > 0) {
                        if (count($academicYear) == 1) {
                            $model->academic_year_relId = $academicYear[0]->id;
                        } else {
                            continue;
                        }
                    } else {
                        // academic year entry not found:
                        $modelNewAcademicYear = new AcAcademicYear();
                        $modelNewAcademicYear->school_year = $nlGset->gsAcadYearRel->sch_year_desc;
                        $modelNewAcademicYear->semester = $nlGset->gsSemRel->semester_name;
                        $modelNewAcademicYear->start_date = strtotime($nlGset->gs_created_at) - (10 * 86400);
                        $modelNewAcademicYear->end_date = strtotime($nlGset->gs_created_at) + (60 * 86400);

                        $modelNewAcademicYear->season = $modelNewAcademicYear->semester == 'I' ? "AUTUMN" : "SPRING";
                        $modelNewAcademicYear->created_by = Yii::$app->user->id;
                        $modelNewAcademicYear->created_at = time();

                        $modelNewAcademicYear->college_relId = 7; //Hard coded for DEC only

                        $modelNewAcademicYear->getData();
                        if ($modelNewAcademicYear->save(false)) {
                            $model->academic_year_relId = $modelNewAcademicYear->id;
                            unset($modelNewAcademicYear);
                        } else {
                            continue;
                        }
                    }

                    $model->admission_relId =  $nlGset->gs_adm_relId;
                    $model->program_relId =  $nlGset->gs_prog_relId;
                    $model->dept_relId =  $nlGset->gs_dept_relId;
                    $model->class_level_relId =  $nlGset->gs_clslvl_relId;
                    $model->spec_relId =  $nlGset->gs_spec_relId;
                    $model->semester_relId =  $nlGset->gs_sem_relId;

                    //$model->start_date = strtotime($nlGset->gs_created_at);
                    //$model->start_date = $model->start_date + (60*86400);//ta least stayed for 2 months:

                    $model->created_by = Yii::$app->user->id;
                    $model->created_at = time();

                    $model->getData();

                    if ($model->save()) {
                        $nlGset->gs_active_prog_relId = $model->id;
                        $nlGset->update();
                        continue;
                    } else {
                        // some error while creating entry:
                    }
                }
            }
        }
    }

    protected function updateCoff2Temp($cot_cocat_IDS)
    {
        $coff2s = $this->coff2s;
        if ($coff2s && count($coff2s) > 0) {
            $cot_cocat_IDS = $cot_cocat_IDS ? $cot_cocat_IDS : $this->coffTemp->cot_cocat_IDS;
            foreach ($coff2s as $coff2) {
                if (is_null($coff2->coff2_cocatIds)) {
                    $coff2->coff2_cocatIds = $cot_cocat_IDS;
                    $coff2->getData();
                    $coff2->update();
                }
            }
        }
    }

    public function getCourseOffered()
    {
        $courses = [];
        // check from courseOfferingTemp
        $modelCoff = $this->coffTemp; //[cocat-ids]
        // var_dump($modelCoff);exit;

        if ($modelCoff) {
            CourseOffering2::updateAcEntry();
        } else {
            $c = $this->coff2s;
            $cf2 = count($c) > 0 ? $c[0] : new CourseOffering2();
            foreach ($this->groupSetups as $gset) {
                $cf2->distributeCoff($gset);
            }
        }

        if ($this->getStudentCount() == 0) { //no student added to group/section
            if ($modelCoff) {
                $this->updateCoff2Temp($modelCoff->cot_cocat_IDS);
                $cocatIDS = json_decode($modelCoff->cot_cocat_IDS, true);
                // var_dump($cocatIDS);exit;
                if (count($cocatIDS) > 0) {
                    foreach ($cocatIDS as $cocatId) {
                        $cocat = CourseCatalog::findOne($cocatId);
                        if ($cocat) {
                            $courses[] = $cocat->cocatCourseRel;
                        }
                    }
                }
            } else {
                // finding by cot_acProgRel failed: cot_acProgRel is NULL
                // find coffTemp by another method:
                $modelCoff = $this->coffTempByOther();
                // if ($modelCoff) {
                //     $this->getCourseOffered();
                // }
            }
        } else {
            // ckeck from courseOffering2Detail
            $modelCoff = $this->coff2s; //models of course offering detail
            // var_dump($modelCoff,$this->id);
            if ($modelCoff) {
                $coff2dets = $modelCoff[0]->courseOffering2Details;
                foreach ($coff2dets as $coff2det) {
                    $courses[] = $coff2det->coff2detCourseRel;
                }
            }
        }

        return $courses;
    }

    private function coffTempByOther()
    {
        $temp = CourseOfferingTemp::updateAcProgLink($this);
        if (is_bool($temp)) {
            return $temp;
        } else {
            // var_dump($temp);exit;
        }
        // $tempCoff = CourseOfferingTemp::find()
        //     ->where([
        //         'cot_acadYear_relid' => $this->academic_year_relId,
        //         'cot_semester_relId' => $this->semester_relId,
        //         'cot_clslvl_relId' => $this->class_level_relId,
        //         'cot_dept_relId' => $this->dept_relId,
        //     ]);
        // if($this->spec_relId){
        //     $tempCoff = $tempCoff->andWhere(['cot_spec_relId' => $this->spec_relId]);
        // }
        //     $tempCoff = $tempCoff->all();
    }


    private function getStudentCount()
    {
        if (!$this->student_count) {
            $this->student_count = \backend\models\WGroupAssignment::find()
                ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
                ->where(['w_group_setup.gs_active_prog_relId' => $this->id])
                ->count();
        }

        return $this->student_count;
    }
    public function getActiveAcademicYear($withLink = 1)
    {
        $modelAcademicYear = new AcAcademicYear();
        return $modelAcademicYear->getActiveAcademicYear($withLink);
    }

    public function checkProgramActive($gset, $objectQuery = false)
    {
        if (is_null($gset)) {
            return $objectQuery ? NULL : ['type' => 'error', 'message' => 'Uknown Group/Section'];
        }
        // 
        $programs = self::find()
            ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
            ->where([
                'school_year' => $gset->gsAcadYearRel->sch_year_desc,
                'semester' => $gset->gsSemRel->semester_name,
                'semester_relId' => $gset->gs_sem_relId,
                'admission_relId' => $gset->gs_adm_relId,
                'program_relId' => $gset->gs_prog_relId,
                'dept_relId' => $gset->gs_dept_relId,
                'class_level_relId' => $gset->gs_clslvl_relId,
                'spec_relId' => $gset->gs_spec_relId,
            ])
            ->all();
        if ($objectQuery) {
            return $programs;
        }
        if ($programs && count($programs) > 0) {
            $messages = '';
            foreach ($programs as $program) {
                $messages .= "{$program->getClassLevelHeader} - {$program->getStatus()}<br>";
            }
            return ['type' => 'success', 'message' => $messages];
        } else {
            return ['type' => 'warning', 'message' => "Program ({$gset->getGsDisp()}) is NOT active in this academic calendar."];
        }
    }

    public function getGroups()
    {
        $gsets = $this->groupSetups;
        $this->student_count = 0;
        if ($gsets && count($gsets) > 0) {
            $gs = '';
            if (!$this->program_schedule_used) {
                $this->program_schedule_used = 1;
                $this->update();
            }
            foreach ($gsets as $gset) {
                $count = $gset->getWGroupAssignments()->count();
                $this->student_count += $count;
                $gs .= "<a class='btn btn-link' style='font-size:8pt; padding: 2px;' title='{$gset->getGsDisp(null)}' onclick='PrintList({$gset->id});return false;'>{$gset->gsGroupRel->group_name}-({$count})</a>";
            }
            return $gs;
        } else {
            return "-";
        }
    }

    public function cascadeToGsets()
    {
        yii::$app->db->createCommand()->update(
            WGroupSetup::tableName(),
            ['gs_entry_type' => $this->entry_type],
            ['gs_active_prog_relId' => $this->id]
        )
            ->execute();
    }

    public function updelPossible()
    {
        return !$this->program_schedule_used && $this->status == self::STATUS_ACTIVE;
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'status' => $this->status,
            'admission_relId' => intval($this->admission_relId),
            'program_relId' => intval($this->program_relId),
            'dept_relId' => intval($this->dept_relId),
            'class_level_relId' => intval($this->class_level_relId),
            'spec_relId' => intval($this->spec_relId),
            'entry_type' => intval($this->entry_type),
            'graduate_class' => intval($this->graduate_class),
            'on_internship' => intval($this->on_internship),
            'semester_relId' => intval($this->semester_relId),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function findAcpByCotemp($cotempModel, $acAcadeYearMModel)
    {
        return self::find()->where([
            'academic_year_relId' => $acAcadeYearMModel->academic_year_relId,
            'admission_relId' => $cotempModel->cot_adm_relId,
            'program_relId' => $cotempModel->cot_prog_relId,
            'dept_relId' => $cotempModel->cot_dept_relId,
            'class_level_relId' => $cotempModel->cot_clslvel_relId,
            'semester_relId' => $cotempModel->cot_sem_relId,
            'spec_relId' => $cotempModel->cot_spec_relId,
        ])->all();
    }

    public function checkIfExists($id = null)
    {
        if (empty($this->spec_relId)) {
            $this->spec_relId = null;
        }
        return $id ?
            self::find()->where([
                'academic_year_relId' => $this->academic_year_relId,
                'admission_relId' => $this->admission_relId,
                'program_relId' => $this->program_relId,
                'dept_relId' => $this->dept_relId,
                'class_level_relId' => $this->class_level_relId,
                'semester_relId' => $this->semester_relId,
                'spec_relId' => $this->spec_relId,
                'status' => self::STATUS_ACTIVE,
            ])->andWhere(" id <> $id ")->count() > 0
            :
            self::find()->where([
                'academic_year_relId' => $this->academic_year_relId,
                'admission_relId' => $this->admission_relId,
                'program_relId' => $this->program_relId,
                'dept_relId' => $this->dept_relId,
                'class_level_relId' => $this->class_level_relId,
                'semester_relId' => $this->semester_relId,
                'spec_relId' => $this->spec_relId,
                'status' => self::STATUS_ACTIVE,
            ])->count() > 0;
    }

    public function getStatus()
    {
        // check schedule of academic year for the program:
        $st = $this->academicYearRel->getStatus();
        if ($this->academicYearRel->status == AcAcademicYear::STATUS_ACTIVE && $this->status != $this->academicYearRel->status) {
            $this->status = $this->academicYearRel->status;
            $this->update();
        }
        $this->status = $this->academicYearRel->status;
        $st = $this->stss[$this->status];
        return "<span class='bg-" . $this->bgs[$this->status] . "'>{$st}</span>";
    }

    public function programScheduleUsed()
    {
        return $this->program_schedule_used == 1;
    }

    public function getClassLevel()
    {
        // 1-II
        return $this->classLevelRel->clslvl_num . "-" . $this->semesterRel->semester_name;
    }

    public function getProgramHeader()
    {
        return "<b>{$this->programRel->program_name} ({$this->admissionRel->admission_name[0]})</b>";
    }

    public function getClassLevelHeader()
    {
        $spec = $this->spec_relId ? "({$this->specRel->spec_name})" : "";
        return $this->deptRel->dept_code . "{$spec}\\" . $this->classLevelRel->getClslvlDisp() . "\\Sem." . $this->semesterRel->semester_name . "(<b>{$this->admissionRel->admission_name[0]}{$this->programRel->program_name[0]}</b>)";
    }

    public function getActiveSchedules()
    {
        $schedules = $this->getAcProgramSchedules()
            ->andFilterWhere([
                'OR',
                ['status' => self::STATUS_ACTIVE],
                ['status' => self::STATUS_UPNEXT]
            ])
            ->all();

        $taskSchedules = "<span style='font-size:8pt;'>";
        $warningIcon = '<i class="fa fa-lg fa-warning fa-exclamation-circle fa-flip blink pull-right"></i>';
        if ($schedules && count($schedules) > 0) {
            // $taskSchedules = "";
            foreach ($schedules as $schedule) {
                $link = Yii::$app->user->can('Student') && !empty($schedule->actionRel->aca_link) ? "<a class='btn btn-link btn-xs pull-right' title='Link for {$schedule->actionRel->aca_name}' href='/{$schedule->actionRel->aca_link}/'>Link</a>" : '';
                $taskSchedules .= "{$schedule->actionRel->aca_name} : <b>{$schedule->getScheduleInterval()}</b> [{$schedule->getStatus()}]{$link}{$warningIcon}<br>";
            }
        } else {
            // continue;
            // $taskSchedules .= "No task schedule found.";
            return NULL;
        }

        $taskSchedules .= "</span>";

        return $taskSchedules;
    }

    public function getAcadYearHeader()
    {
        return $this->academicYearRel->getAcHeader();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicYearRel()
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'academic_year_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmissionRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'admission_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassLevelRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'class_level_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramRel()
    {
        return $this->hasOne(Program::class, ['id' => 'program_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'semester_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoffTemp()
    {
        return $this->hasOne(CourseOfferingTemp::class, ['cot_acProg_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcProgramSchedules()
    {
        return $this->hasMany(AcProgramSchedule::class, ['active_program_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2s()
    {
        return $this->hasMany(CourseOffering2::class, ['coff2_acProg_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupSetups()
    {
        return $this->hasMany(WGroupSetup::class, ['gs_active_prog_relId' => 'id']);
    }
}
