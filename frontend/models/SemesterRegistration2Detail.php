<?php

namespace frontend\models;

use \common\components\CommonControllerFunctions as CCF;
use \backend\models\WGroupSetup;
use \backend\models\CourseCatalog;
use \backend\models\GradeScale;
use \backend\models\SemesterEvaluation2;
use \backend\models\CourseOffering2Detail;
use backend\models\ReassessmentHistory;
use backend\models\Student;
use backend\models\TouchedGrades;
use Yii;

/**
 * This is the model class for table "semester_registration_2_detail".
 *
 * @property integer $id
 * @property integer $srd2_semreg_relId
 * @property integer $srd2_coff_relId
 * @property integer $srd2_course_relId
 * @property integer $srd2_grade_relId
 * @property integer $srd2_coadd_flag
 * @property integer $srd2_corptd_flag
 * @property integer $srd2_codrpd_relId
 * @property integer $srd2_corProject
 * @property integer $srd2_enrollApproved
 * @property integer $srd2_enrollApproved_by
 * @property string $srd2_enrolApprovrd_at
 * @property integer $srd2_group_relId
 * @property integer $srd2_updated_by
 * @property string $srd2_updated_at
 * @property string $srd2_created_at
 * @property integer $srd2_created_by
 *
 * @property CourseOffering $srd2CoffRel
 * @property Course $srd2CourseRel
 * @property User $srd2CreatedBy
 * @property User $srd2EnrollApprovedBy
 * @property GradeScale $srd2GradeRel
 * @property StudentGroupHistory $srd2GroupRel
 * @property SemesterRegistration2 $srd2SemregRel
 * @property User $srd2UpdatedBy
 * 
 */



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
 * @property integer $srd2_gradeLocked
 * @property integer $srd2_gradeApproved
 * @property integer $srd2_gradeApproved_by
 * @property integer $srd2_gradeApproved_at
 * @property integer $semreg_approved
 * @property integer $semreg_approved_by
 * @property string $semreg_approved_at
 * @property integer $semreg_created_by
 * @property integer $semreg_updated_by
 * @property string $semreg_created_at
 * @property string $semreg_updated_at
 **/

class SemesterRegistration2Detail extends \yii\db\ActiveRecord
{

    public $acadYearId;
    public $semId;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    private function getNgs($studentObj, $gsetObject)
    {
        $remCourse = "";
        $thisSemesterRegisteredCourses = $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('grade_scale', 'grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
            ->where([
                'semreg_semester_relId' => $gsetObject->gs_sem_relId,
                'semreg_acadYear_relId' => $gsetObject->gs_acadYear_relId,
                'semreg_std_relId' => $studentObj->id,
            ])
            ->andWhere(['OR', ['srd2_grade_relId' => NULL], ['grade_scale.gs_valid' => 0]])
            ->all();
        if ($thisSemesterRegisteredCourses && count($thisSemesterRegisteredCourses) > 0) {
            foreach ($thisSemesterRegisteredCourses as $key) {
                $remCourse .= " [" . $key->srd2CourseRel->course_code . '(' . $key->srd2CourseRel->course_crdthr . ')] ';
            }
        }

        return $remCourse;
    }

    public function notRequested()
    {
        return ReassessmentHistory::find()
            ->where([
                'ras_srd2_relId' => $this->id,
            ])
            ->andWhere(' ras_aft_grade_relId IS NULL ')
            ->count() > 0;
    }

    /**
     * Summary of alreadyRequested
     * @return bool
     * checks if this srd2 has been already requested for grade change.
     */
    public function alreadyRequested()
    {
        return TouchedGrades::find()->where(['tg_srd2_relId' => $this->id])->count() > 0;
    }
    public function courseIsRepeatedSomeWhere()
    {
        // if the course is repeated in the same class level and semester, then bypass it.
        if (!($this->srd2_corptd_flag)) {
            $repeatedEntry = self::find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'semreg_clslvl_relId' => $this->srd2SemregRel->semreg_clslvl_relId,
                    'semreg_semester_relId' => $this->srd2SemregRel->semreg_semester_relId,
                    'semreg_std_relId' => $this->srd2SemregRel->semreg_std_relId,
                    'srd2_course_relId' => $this->srd2_course_relId,
                    'srd2_corptd_flag' => 1
                ])
                ->andWhere([' <> ', 'semester_registration_2_detail.id', $this->id])
                ->one();
            if ($repeatedEntry && count($repeatedEntry) > 0) {
                return true;
            }
        } else {
            return false;
        }
    }
    public function enrolledThesis2($stdId)
    {
        return $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->innerJoin('course_offering2', 'course_offering2.id = semester_registration_2_detail.srd2_coff_relId')
            ->innerJoin('course_offering2_detail', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('course_catalog', 'course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
            ->where([
                'semreg_std_relId' => $stdId,
                'cocat_courseProject' => 1
            ])
            ->all();
    }
    public function enrolledThesis($stdId)
    {
        // 'srd2_grade_relId' => NULL
        return $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->innerJoin('course', 'course.id = semester_registration_2_detail.srd2_course_relId')
            ->where([
                'semreg_std_relId' => $stdId,
            ])
            ->andWhere([
                'OR',
                ['like', 'course.course_title', strtoupper("THESIS PHASE I")],
                ['like', 'course.course_title', strtoupper("THESIS PHASE II")]
            ])
            ->all();
    }

    public function getThesisIIEnrolledStudents($adObj, $intId)
    {
        return $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->innerJoin('course_catalog', 'course_catalog.id = semester_registration_2_detail.srd2_cocat_relId')
            ->where([
                'semreg_semester_relId' => $adObj->sa_sem_relId,
                'semreg_acadYear_relId' => $adObj->sa_acadYear_relId,
            ])
            ->all();
    }
    public function getPGClearableCourses($stdObject)
    { //Cleareable courses for PG by suplementary Exam
        // for Now we just assume courses with C (But not cleared yet) can be examed supplementarly.
        $modelGs = new GradeScale();
        $clearableGrades = $modelGs->getClearableGrades($stdObject);

        $ClearableGradesArray = [];
        if ($clearableGrades && count($clearableGrades) > 0) {
            foreach ($clearableGrades as $Clearablekey) {
                $ClearableGradesArray[] = $Clearablekey->id;
            }

            $clearableCoursesArray = [];
            $clearableCourses = $this->find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where(['srd2_grade_relId' => $ClearableGradesArray])
                ->andWhere(['semreg_std_relId' => $stdObject->id])
                ->all();
            $modelSupExam = new \backend\models\SupplementaryExam();

            if ($clearableCourses && count($clearableCourses) > 0) {
                $checkedCourses = [];
                foreach ($clearableCourses as $CCkey) {
                    // check if already Cleared it before!
                    if ($this->clearedGrade($stdObject, $CCkey, $ClearableGradesArray)) {
                        continue;
                    } else {
                        if (in_array($CCkey->srd2_course_relId, $checkedCourses)) {
                            continue;
                        }
                        $checkedCourses[] = $CCkey->srd2_course_relId;
                        // check if already requested for SE and pending for approval fro the concerned
                        if ($modelSupExam->isRequested($CCkey)) {
                            continue;
                        }

                        $clearableCoursesArray[] = $CCkey;
                    }
                }
                return $clearableCoursesArray;
            }
            return "No Clearable Grade Obtained Yet.";
        }
        return "No Clearable Grade Obtained Yet.";
    }

    public function clearedFailGrade()
    {
        return $this->find()
            ->innerJoin('grade_scale', 'grade_scale.id=srd2_grade_relId')
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=srd2_semreg_relId')
            ->where([
                'srd2_course_relId' => $this->srd2_course_relId,
                'semreg_std_relId' => $this->srd2SemregRel->semreg_std_relId,
            ])
            ->andWhere('gs_pass_status <> 0 AND gs_ltr_grade <> "F" AND semester_registration_2_detail.id <> ' . $this->id)
            ->count() > 0;
    }

    private function clearedGrade($stdObject, $srd2Obj, $ClearableGradesArray)
    {
        $c = $this->getSemregInfo($stdObject, $srd2Obj->srd2CourseRel);
        return (!in_array($c->srd2_grade_relId, $ClearableGradesArray));
    }
    private function getUnclearedFs($studentObj, $gsetObject)
    {
        $max  = "";

        $GradeScaleObj = new GradeScale();
        $invalidGrades = $GradeScaleObj->getInvalidGrades($studentObj, $gsetObject);
        $invalidGradesArray = [];
        if ($invalidGrades && count($invalidGrades) > 0) {
            foreach ($invalidGrades as $keyWT) {
                $invalidGradesArray[] = $keyWT->id;
            }
        }
        $semregInfo = $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where(['srd2_grade_relId' => $invalidGradesArray])
            ->andWhere(['semreg_std_relId' => $studentObj->id])
            ->all();
        if ($semregInfo && count($semregInfo) > 0) {
            foreach ($semregInfo as $SRD2key) {
                $clrd = $this->find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where(['NOT IN', 'srd2_grade_relId', $invalidGradesArray])
                    ->andWhere(['srd2_course_relId' => $SRD2key->srd2_course_relId])
                    ->andWhere(['semreg_std_relId' => $studentObj->id])
                    ->all();
                if ($clrd && count($clrd) > 0) {
                    continue;
                }
                $max .= " [" . $SRD2key->srd2CourseRel->course_code . '(' . $SRD2key->srd2CourseRel->course_crdthr . ')] ';
            }
        }

        return $max;
    }
    public function getNGSFS($studentObj, $gsetObject)
    {
        return [
            'ngs' => $this->getNgs($studentObj, $gsetObject),
            'Fs' => $this->getUnclearedFs($studentObj, $gsetObject)
        ];
    }


    public function evaluatedCourseInstructors()
    {
        // var_dump('frontend');die();
        // check if the student evaluated the instructor or NOT
        if ($this->srd2_evaluationCompleted == 1) {
            return true;
        } else {
            $modelEvaluation = new SemesterEvaluation2();
            // srd2_coadd_flag is a flag indicating whether a course registration is by adding or NOT
            // if added, the flag will be NON-NULL value which holds the group with which the course added.

            if (isset($this->srd2_coadd_flag) && !is_null($this->srd2_coadd_flag)) {
                $groupId = $this->srd2_coadd_flag;
            } else {
                $groupId = $this->srd2_group_relId;
            }
            // get course Instructors:

            $coff2Det = CourseOffering2Detail::find()
                ->innerJoin('course_offering2', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                ->where([
                    'coff2det_coff2_relId' => $this->srd2_coff_relId,
                    'coff2det_course_relId' => $this->srd2_course_relId,
                    'coff2_group_relId' => $groupId,
                ])
                ->one();

            if ($coff2Det) { //corresponding offering entry found.
                // var_dump($coff2Det->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name);
                // var_dump($coff2Det->coff2det_bypassEvaluation);die();
                if ($coff2Det->coff2det_bypassEvaluation) {
                    //check if byPass evaluation for this offering is set or not.
                    // if so, leave students free to see their grades regardless of evaluating the Inst/TA.
                    return true;
                }
                // check if evaluated instructor.
                $instructor = $coff2Det->coff2detInstRel;
                $evaluatedInstructor = true;
                if ($instructor) {
                    $evaluatedInstructor = $modelEvaluation->checkIfEvaluated($groupId, $this->srd2_course_relId, $instructor->inst_user_relId, $this->srd2SemregRel->semregStdRel->std_user_relId);
                }

                // before checking either evaluated TA or not 
                $evaluatedTA = true;
                $TA = $coff2Det->coff2detTARel;
                if ($TA) {
                    $evaluatedTA = $modelEvaluation->checkIfEvaluated($groupId, $this->srd2_course_relId, $TA->inst_user_relId, $this->srd2SemregRel->semregStdRel->std_user_relId);
                }

                if ($evaluatedInstructor && $evaluatedTA) {
                    $this->srd2_evaluationCompleted = 1;
                    $this->update();
                    return true;
                } else {
                    return false;
                }
            }
            // if not found
            return 1;
        }
    }

    public function getSemregInfo($studentObj, $courseObj)
    {
        // course may be repeated:
        $max  = NULL;
        $semregInfo = $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where(['semreg_std_relId' => $studentObj->id])
            ->andWhere(['srd2_course_relId' => $courseObj->id])
            ->all();
        if ($semregInfo) {
            // more than One enroll history may found for the course.
            $max = $semregInfo[0];
            foreach ($semregInfo as $SRD2key) {
                if ($SRD2key->srd2_created_at > $max->srd2_created_at) {
                    $max = $SRD2key; //Last enrolled is considered for further calculation
                }
            }
        }

        return $max;
    }
    public function getSemregRecordPrev($stdId, $cGset, $forChange = NULL)
    {

        $semObj = \backend\models\Semester::findOne($cGset->gs_sem_relId);
        $clslvlObj = \backend\models\ClassLevel::findOne($cGset->gs_clslvl_relId);

        $std = \backend\models\Student::findOne($stdId);
        // special case;
        if (CCF::specialCaseCheckP($cGset, $std)) {
            return ['-', '-', '-'];
        }
        $gset = NULL;
        $gass = \backend\models\WGroupAssignment::find()
            ->innerJoin('w_group_setup', 'w_group_setup.id=w_group_assignment.gas_gset_relId')
            ->innerJoin('school_year', 'school_year.id=w_group_setup.gs_acadYear_relId')
            ->innerJoin('class_level', 'class_level.id=w_group_setup.gs_clslvl_relId')
            ->innerJoin('semester', 'semester.id=w_group_setup.gs_sem_relId')
            ->where(['gas_std_relId' => $stdId])
            ->orderBy([
                'sch_year_desc' => SORT_ASC,
                'clslvl_num' => SORT_ASC,
                'semester_name' => SORT_ASC
            ])
            ->all();
        if ($gass) {
            $prev = $gass[0];
            foreach ($gass as $gas) {
                if ($gas->gas_gset_relId == $cGset->id) {
                    $gset = $prev->gasGsetRel;
                    break;
                }
                $prev = $gas;
            }
        }

        if (!($gset)) {
            return "<p class='bg-warning'>Error with previous semester registration.</>";
        }

        // $ps = $semObj->semester_name == 'I'?
        //         ($std->stdAdmissionRel->admission_name == 'Extension'?('Summer'):('II')):
        //     ($semObj->semester_name == 'Summer'?'II':'I');
        // $cs = $semObj->semester_name == 'I'?$clslvlObj->clslvl_num - 1:($std->stdAdmissionRel->admission_name == 'Extension' && $ps == 'II' ?($clslvlObj->clslvl_num - 1):($clslvlObj->clslvl_num));

        // $ps = NULL;
        // $cs = $clslvlObj->clslvl_num;

        // if ($semObj->semester_name == 'Summer') {
        //     $ps = 'II';
        // }else if($semObj->semester_name == 'II'){
        //     $ps = 'I';
        // }else if ($semObj->semester_name == 'I') {
        //     if ($std->stdAdmissionRel->admission_name == 'Extension') {
        //         $ps = 'Summer';
        //     }else{
        //         $ps = 'II';
        //     }

        //     $cs -= 1;
        // }


        // // var_dump($ps,$cs);
        // // die();

        // $prevSemObj = \backend\models\Semester::findOne(['semester_name'=>$ps]);
        // $prevClslvlbj = \backend\models\ClassLevel::findOne(['clslvl_num'=>$cs,'clslvl_program_relId'=>$cGset->gs_prog_relId]);

        // // var_dump($prevSemObj->semester_name,$prevClslvlbj->clslvl_num);
        // // die();

        // // special case;
        // if(CCF::specialCaseCheckP($cGset,$std)){
        //     return ['-','-','-'];
        // }
        // $prevClslvlId = \backend\models\ClassLevel::findOne(['clslvl_num'=>$prevClslvlbj->clslvl_num,'clslvl_program_relId'=>$std->std_prog_relId])->id;
        // $prevSem = \backend\models\Semester::findOne(['semester_name'=>$prevSemObj->semester_name])->id;

        // $clslvlObj = \backend\models\ClassLevel::findOne($prevClslvlId);
        // $prevSemObj = \backend\models\Semester::findOne($prevSem);

        // // var_dump($clslvlObj->clslvl_num,$prevSemObj->semester_name);die();

        // $gset = CCF::getStudentGroupHistoryPublic($prevClslvlId,$prevSem,$std->id);

        $SGPA = NULL;
        $CGPA = NULL;
        $status = 'Pending';

        // var_dump($gset->gsClslvlRel->clslvl_num,$gset->gsSemRel->semester_name);die();

        $readmitted = new \backend\models\WStudentReadmissionHistory();
        $readmitted3 = $readmitted->checkReadmitted($cGset, $std);

        if ($readmitted3) {
            $status = 'Academic Dismissal(R)';
        }
        $exists = \backend\models\WGroupAssignment::findOne(['gas_gset_relId' => $gset->id, 'gas_std_relId' => $stdId]);
        if ($exists) {
            // check if grade submission is completed.
            $submissionCompleted = CCF::checkIfGradeSubmissionCompletedP($gset->id, $gset->gs_clslvl_relId, $gset->gs_clslvl_relId, $stdId);
            if ($submissionCompleted == 0) { //zero indicates there is no PENGING grade.
                $srd = $this->find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where([
                        'semreg_acadYear_relId' => $gset->gs_acadYear_relId,
                        'semreg_semester_relId' => $gset->gs_sem_relId,
                        'semreg_clslvl_relId' => $gset->gs_clslvl_relId,
                        'semreg_gradeCompleted' => 1,
                        'semreg_std_relId' => $stdId
                    ])
                    ->andWhere([
                        'IS NOT',
                        'srd2_grade_relId',
                        NULL
                    ])
                    ->one();
                if ($srd) {
                    $SGPA = $srd->srd2SemregRel->semreg_SGPA;
                    $CGPA = $srd->srd2SemregRel->semreg_CGPA;

                    $statusArray = CCF::getSemesterStatusP($SGPA, $CGPA, $gset->id, $std);

                    $ck = $this->checkIfKafiyaMade($gset, $std);
                    if ($ck) {
                        $status = $ck->ssshSssnRel;
                    } else {
                        if ($statusArray && is_array($statusArray) && isset($statusArray[1])) {
                            if ($statusArray[1] == 'd') {
                                // Default case.
                                $default = $statusArray[0];
                                if ($default) {
                                    $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $default->sssccd_sssn_relId]);
                                    if ($name1) {
                                        $status = $name1;
                                        // sssn_name
                                        if ($name1->sssn_pass_status != 1) {
                                            $ck = $this->checkIfKafiyaMade($gset, $std);
                                            if ($ck && count($ck) > 0) {
                                                $status = $ck->ssshSssnRel;
                                            }
                                        }
                                    }
                                }
                            } else if ($statusArray[1] == 'ac') {
                                $ac = $statusArray[0];
                                if ($ac && count($ac) > 0) {
                                    $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $ac->sssccAC_sssn_relId]);
                                    if ($name1) {
                                        $status = $name1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $CGPA = $CGPA == 0 ? $SGPA : $CGPA;

        if (!is_null($forChange)) {
            if ($cGset && ($cGset->id == $gset->id)) {
                return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status, is_object($status) ? $status->id : $status, true, is_object($status) ? $status->sssn_pass_status : $status];
            }
            // if current semester ,  show it by setting [3] = true, and [4] = pass_status,
            return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status, is_object($status) ? $status->id : $status];
        }
        if (is_object($cGset)) {
            if ($cGset->id == $gset->id) {
                return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status, true, is_object($status) ? $status->sssn_pass_status : $status];
            }
        }
        // if current semester ,  show it by setting [3] = true, and [4] = pass_status,
        return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status];
    }


    private function isGCStudent($gsetObject)
    {
        if (strtoupper($gsetObject->gsProgRel->program_name == 'MASTERS')) {
            return $gsetObject->gsClslvlRel->clslvl_num == 2;
        } else {
            return $gsetObject->gsClslvlRel->clslvl_num == 5;
        }
    }

    private function EligiblityMasters($studentObj, $cGsetObj, $eligiblityControlObject)
    {
        // ->andWhere(['srd2_group_relId'=>$cGsetObj->id])
        $semregObj = $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $studentObj->id,
                'semreg_acadYear_relId' => $cGsetObj->gs_acadYear_relId,
                'semreg_semester_relId' => $cGsetObj->gs_sem_relId,
                'semreg_gradeCompleted' => 1,
                'semreg_clslvl_relId' => $cGsetObj->gs_clslvl_relId,
            ])
            ->all();
        if ($semregObj && count($semregObj)) {
            $ss = $semregObj[0]->srd2SemregRel;
            $GradeScaleObj = new GradeScale();
            $invalidGrades = $GradeScaleObj->getInvalidGrades($studentObj, $cGsetObj);
            $invalidGradesArray = [];
            if ($invalidGrades && count($invalidGrades) > 0) {
                foreach ($invalidGrades as $IGkey) {
                    $invalidGradesArray[] = $IGkey->id;
                }
            }

            $remark = "";

            $obtainedInvalidGrades = $this->getInvalidGradesObtained($studentObj, $cGsetObj, $invalidGradesArray);

            if (is_array($obtainedInvalidGrades)) {
                $elig  = 'NO';
                foreach ($obtainedInvalidGrades as $IGCkey) {
                    $remark .= "{$IGCkey->srd2CourseRel->course_code} => {$IGCkey->srd2GradeRel->gs_ltr_grade}<br>";
                }
            }

            $cgpas = $this->getCGPAStatus($studentObj, $cGsetObj, $invalidGradesArray);


            // var_dump( $cgpas);die();
            if (is_array($cgpas) && count($cgpas) > 0) {
                $CGPA = $cgpas[2];
                $totalCredit = $cgpas[3];
                $catalogCreditSum = $cgpas[4];
                $pWorkCrdt = 0;
                $courseWorkCrdt = 0;

                $rrr = "";
                $IDD = explode('/', $studentObj->std_regId);
                foreach ($IDD as $REGkey) {
                    $rrr .= $REGkey . '_';
                }

                if (strtoupper($cGsetObj->gsProgRel->program_name) == 'MASTERS') {
                    $pWorkCrdt = $cgpas[6];
                    $courseWorkCrdt = $cgpas[3];

                    $remark .= "
                        <div class='container-fluid'>
                            <div class='container-fluid'><button class='btn btn-primary btn-xs pull-right' onclick='toggleDisplay(\"{$rrr}\")'>[:-:]</button></div>
                            <div class='container-fluid' style='display:none;' id='div_{$rrr}'>
                                Project Work : <b>{$pWorkCrdt}</b>
                                <br>Course Work : <b>{$courseWorkCrdt}</b>
                                <br>CGPA : <b>" . sprintf('%.02f', $cgpas[2]) . "/" . sprintf('%0.2f', $eligiblityControlObject->ge_min_CGPA) . "</b>
                                <br>Total Credit : <b>" . ($pWorkCrdt + $courseWorkCrdt) . "</b>
                                <br>Remaining Courses : <br><b>{$cgpas[5]}</b>
                        ";
                    if ($pWorkCrdt >= 18 && $courseWorkCrdt >= 30) {
                        $elig = 'YES';
                    } else {
                        $elig  = 'NO';
                    }
                }

                // catalog min crdthr check'
                if ($cgpas[4] > $cgpas[3]) {
                    $elig = 'NO';
                    $remark .= "<br>Minimum credit hour NOT satisfied.";
                    // return [sprintf("%.02f",$CGPA),$elig,$remark,$totalCredit,$obtainedInvalidGrades];
                }

                // catalog min CGPA check
                if ($eligiblityControlObject->ge_min_CGPA > $cgpas[2]) {
                    $elig = 'NO';
                    $remark .= "<br>Minimum CGPA NOT satisfied.";
                    // return [sprintf("%.02f",$CGPA),$elig,$remark,$totalCredit,$obtainedInvalidGrades];
                }

                $remark .= "</div></div>";
                return [sprintf("%.02f", $CGPA), $elig, $remark, $totalCredit, $obtainedInvalidGrades];
            }
        } else {
            return " <p class='bg-danger'>No record.</p> ";
        }
    }

    public function isEligible($studentObj, $cGsetObj)
    { //Student Object and Current Group Object
        // allow eligiblist check for

        // if (!$this->isGCStudent($cGsetObj)) {
        //     return " <p class='bg-danger'>Not GC Student, Check Eligiblity for GC students.</p> ";
        // }

        // checking for eleigiblity of UG goes over here.
        $eligiblityControlObject = \backend\models\GraduationEligiblity::find()
            ->where([
                'ge_adm_relId' => $cGsetObj->gs_adm_relId,
                'ge_prog_relId' => $cGsetObj->gs_prog_relId,
            ])
            ->one();

        $elig = 'NO';
        $remark = "";
        $CGPA = 0.00;
        $SGPA = 0.00;
        $totalCredit = 0;
        $obtainedInvalidGrades = [];
        if ($eligiblityControlObject && count($eligiblityControlObject) > 0) {

            // branc eleigiblity check for MASTERS to else option
            if (strtoupper($cGsetObj->gsProgRel->program_name) == 'MASTERS') {
                return $this->EligiblityMasters($studentObj, $cGsetObj, $eligiblityControlObject);
            }
            // eligbity check for Masters in another module::
            $semregObj = $this->find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'semreg_std_relId' => $studentObj->id,
                    'semreg_acadYear_relId' => $cGsetObj->gs_acadYear_relId,
                    'semreg_semester_relId' => $cGsetObj->gs_sem_relId,
                    'semreg_gradeCompleted' => 1,
                    'semreg_clslvl_relId' => $cGsetObj->gs_clslvl_relId,
                ])
                ->andWhere([
                    'srd2_group_relId' => $cGsetObj->id
                ])
                ->all();
            if ($semregObj && count($semregObj) > 0) {
                $ss = $semregObj[0]->srd2SemregRel;
                $GradeScaleObj = new GradeScale();
                if ($ss && count($ss) > 0) {
                    $CGPA = $ss->semreg_CGPA;
                    $totalCredit = $ss->semreg_totalCrdtHr;

                    // check for Invalid Grades.
                    $invalidGrades = $GradeScaleObj->getInvalidGrades($studentObj, $cGsetObj);
                    $invalidGradesArray = [];
                    if ($invalidGrades && count($invalidGrades) > 0) {
                        foreach ($invalidGrades as $IGkey) {
                            $invalidGradesArray[] = $IGkey->id;
                        }
                    }


                    $obtainedInvalidGrades = $this->getInvalidGradesObtained($studentObj, $cGsetObj, $invalidGradesArray);

                    if (is_array($obtainedInvalidGrades)) {

                        // echo "<pre>";
                        // var_dump($obtainedInvalidGrades);
                        // die();
                        $elig  = 'NO';
                        foreach ($obtainedInvalidGrades as $IGCkey) {
                            $remark .= "{$IGCkey->srd2CourseRel->course_code} => {$IGCkey->srd2GradeRel->gs_ltr_grade}<br>";
                        }
                        // No more check up is required, kick out the stduent from the loop.
                        // return [sprintf("%.02f",$CGPA),$elig,$remark,$totalCredit,$obtainedInvalidGrades];
                    }


                    // check for min CGPAS
                    $cgpas = $this->getCGPAStatus($studentObj, $cGsetObj, $invalidGradesArray);

                    if (is_array($cgpas) && count($cgpas) > 0) {

                        $CGPA = $cgpas[2];
                        $totalCredit = $cgpas[3];
                        $catalogCreditSum = $cgpas[4];
                        $pWorkCrdt = 0;
                        $courseWorkCrdt = 0;

                        $rrr = "";
                        $elig = 'YES';
                        $IDD = explode('/', $studentObj->std_regId);
                        foreach ($IDD as $REGkey) {
                            $rrr .= $REGkey . '_';
                        }

                        $remark .= "
                            <div class='container-fluid'>
                                <div class='container-fluid'><button class='btn btn-primary btn-xs pull-right' onclick='toggleDisplay(\"{$rrr}\")'>[:-:]</button></div>
                                <div class='container-fluid' style='display:none;' id='div_{$rrr}'>
                                    Major GPA : <b>" . sprintf('%.02f', $cgpas[0]) . "/" . sprintf('%0.2f', $eligiblityControlObject->ge_min_CGPA_major) . "</b>
                                    <br>CGPA : <b>" . sprintf('%.02f', $cgpas[2]) . "/" . sprintf('%0.2f', $eligiblityControlObject->ge_min_CGPA) . "</b>
                                    <br>Total Credit : <b>{$cgpas[3]}/<b><b>{$catalogCreditSum}</b>
                                    <br>Remaining Courses : <b>{$cgpas[5]}</b>
                                    
                        ";


                        // catalog min crdthr check'
                        if ($cgpas[4] > $cgpas[3]) {
                            $elig = 'NO';
                            $remark .= "<br>Minimum credit hour NOT satisfied.";
                            // return [sprintf("%.02f",$CGPA),$elig,$remark,$totalCredit,$obtainedInvalidGrades];
                        }

                        // catalog min CGPA check
                        if ($eligiblityControlObject->ge_min_CGPA > $cgpas[2]) {
                            $elig = 'NO';
                            $remark .= "<br>Minimum CGPA NOT satisfied.";
                            // return [sprintf("%.02f",$CGPA),$elig,$remark,$totalCredit,$obtainedInvalidGrades];
                        }

                        // check Major GPA
                        if (strtoupper($cGsetObj->gsProgRel->program_name) != 'MASTERS') {
                            // check MGPA and Common GPA
                            if ($eligiblityControlObject->ge_min_CGPA_major > $cgpas[1]) {
                                $elig = 'NO';
                                $remark .= "<br>Minimum Magor GPA NOT satisfied.";
                            }
                        }

                        $remark .= "</div></div>";
                        return [sprintf("%.02f", $CGPA), $elig, $remark, $totalCredit, $obtainedInvalidGrades];
                    }
                }
                // return ['---','X','---','Semester registration record NOT found.'];
            }
            $remark .= "Course NOT completed";
            return ['-', $elig, $remark, $totalCredit, $obtainedInvalidGrades];
            // return ['---','X','---','Not Completed Course'];
        } else {
            return ['-', 'X', '-', 'Eligiblity controll not known.'];
        }
    }


    public function getCGPAStatusP($studentObj, $cGsetObj, $invalidGradesArray = NULL)
    {
        return $this->getCGPAStatus($studentObj, $cGsetObj, NULL);
    }

    private function getCGPAStatus($studentObj, $cGsetObj, $invalidGradesArray = NULL)
    {
        $mGPA = 0.00;
        $cGPA = 0.00;
        $courseEntry = [];
        if (strtoupper($cGsetObj->gsProgRel->program_name) == 'MASTERS') {
            $ivg = $this->find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where(['semreg_std_relId' => $studentObj->id,])
                ->all();
            if ($ivg && count($ivg) > 0) {
                $GPAS = $this->separateMajorMasters($ivg, $studentObj, $cGsetObj);

                if (is_array($GPAS) && count($GPAS) > 0) {
                    // WONDE DON'T KNOW HOW TO HANDLE THIS:::
                    return $GPAS;
                }
            }
        } else {
            $ivg = $this->find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where(['semreg_std_relId' => $studentObj->id,])
                ->all();
            if ($ivg && count($ivg) > 0) {
                $GPAS = $this->separateMajor($ivg, $studentObj, $cGsetObj);

                if (is_array($GPAS) && count($GPAS) > 0) {
                    // WONDE DON'T KNOW HOW TO HANDLE THIS:::
                    return $GPAS;
                }
            }
        }
        return NULL;
    }


    private function separateMajorMasters($enrolledCourses, $studentObject, $cGset)
    {
        $magor = [];
        $common = [];
        $majorGPA = 0.00;
        $commonCGPA = 0.00;

        $cgPoint = 0;
        $cgtotalCrdt = 0;

        $mgPoint = 0;
        $mtotalCrdt = 0;

        $gPoint = 0.00;
        $totalCredit = 0;

        $groudGPA = 0.00;
        // check in catalog for major or not.

        $catalogCreditSum = 0;
        $projectWorkCreditSum = 0;
        $courseWorkCrdt = 0;

        $projectWorkCreditSumCompleted = 0;
        $courseWorkCrdtCompleted = 0;

        $remainingCourses = "";
        $remark = "";

        if ($enrolledCourses && count($enrolledCourses) > 0) {
            $catModel = new CourseCatalog();
            $catalogCourses = $catModel->getCatalogCourse2($cGset);
            $catalogCoursesArray = [];
            $projectCourseWork = [];
            $courseWork = [];

            if ($catalogCourses && count($catalogCourses) > 0) {
                foreach ($catalogCourses as $CCKey) {
                    if (strtoupper($cGset->gsProgRel->program_name) == 'MASTERS' && $CCKey->cocat_courseProject == 1) {
                        $projectCourseWork[] = $CCKey->cocatCourseRel->id;
                        $projectWorkCreditSum += $CCKey->cocatCourseRel->course_crdthr;
                    } else {
                        $courseWork[] = $CCKey->cocatCourseRel->id;
                        $courseWorkCrdt += $CCKey->cocatCourseRel->course_crdthr;
                    }

                    $catalogCoursesArray[] = $CCKey->cocatCourseRel->id;
                    $catalogCreditSum += $CCKey->cocatCourseRel->course_crdthr;
                }
            }

            $catalogCoursesArray = array_unique($catalogCoursesArray);

            foreach ($enrolledCourses as $ECkey) {
                $cocat = CCF::getCocat2P($cGset)->andWhere(['cocat_course_relId' => $ECkey->srd2_course_relId])->one();

                if (!($cocat && count($cocat) > 0)) {
                    continue;
                }
                if (!is_null($ECkey->srd2_grade_relId)) {
                    if ($ECkey->srd2GradeRel->gs_pass_status == 0) {
                        //Invalid Grade for the course:
                    } else {
                        // here there is valid grade for the course.
                        if ($cocat->cocat_courseProject == 1) {
                            if ($ECkey->srd2GradeRel->gs_gradePoint != 0) {
                                $courseWorkCrdtCompleted += $cocat->cocatCourseRel->course_crdthr;
                            } else {
                                $projectWorkCreditSumCompleted += $cocat->cocatCourseRel->course_crdthr;
                            }
                        } else {
                            $courseWorkCrdtCompleted += $cocat->cocatCourseRel->course_crdthr;
                        }

                        $catalogCoursesArray = array_diff($catalogCoursesArray, [$ECkey->srd2_course_relId]);
                    }
                }

                // if (is_null($ECkey->srd2_grade_relId) || ) {//course not graded yet.
                //     if ($cocat->cocat_courseProject == 1) {
                //         $projectWorkCreditSum -= $projectWorkCreditSum == 0?0:$cocat->cocatCourseRel->course_crdthr;
                //     }else{
                //         $courseWorkCrdt -= $courseWorkCrdt == 0?0:$cocat->cocatCourseRel->course_crdthr; 
                //     }
                // }else{
                //     if ($cocat->cocat_courseProject == 1) {
                //         if ($ECkey->srd2GradeRel->gs_gradePoint != 0) {
                //             $courseWorkCrdt += $CCKey->cocatCourseRel->course_crdthr; 
                //             $projectWorkCreditSum -= $CCKey->cocatCourseRel->course_crdthr; 
                //             $courseWorkCrdtCompleted += $courseWorkCrdtCompleted == 0?0:$cocat->cocatCourseRel->course_crdthr; 
                //         }else{
                //             $projectWorkCreditSumCompleted += $projectWorkCreditSumCompleted == 0?0:$cocat->cocatCourseRel->course_crdthr;
                //         }
                //         // $projectWorkCreditSumCompleted += $projectWorkCreditSumCompleted == 0?0:$cocat->cocatCourseRel->course_crdthr;
                //     }else{
                //         $courseWorkCrdtCompleted += $courseWorkCrdtCompleted == 0?0:$cocat->cocatCourseRel->course_crdthr; 
                //     }
                //     $catalogCoursesArray = array_diff($catalogCoursesArray, [$ECkey->srd2_course_relId]);
                // }
            }

            $semStat = CCF::getSemesterGrade($cGset->gs_clslvl_relId, $cGset->gs_sem_relId, $studentObject->id);

            if ($semStat && count($semStat) > 0) {
                if (is_array($semStat)) {
                    if (isset($semStat[1][2])) {
                        $CGPA = $semStat[1][2][0];
                        $totalPointSum = $semStat[1][2][1];
                        $totalCredit = $semStat[1][2][2];
                    } else {
                        $remark .= "{$semStat}";
                    }
                } else {
                    $remark .= "{$semStat}";
                }
            }

            $catalogCreditSum = $projectWorkCreditSumCompleted + $courseWorkCrdtCompleted;

            if ($catalogCoursesArray && count($catalogCoursesArray) > 0) {
                foreach ($catalogCoursesArray as $RCkey) {
                    $course = \backend\models\Course::findOne($RCkey);
                    $remainingCourses .= $course->course_code . ' => ' . $course->course_title . ' (' . $course->course_crdthr . ')<br>';
                }
            }
            return [$majorGPA, $commonCGPA, $CGPA, $totalCredit, $catalogCreditSum, $remainingCourses, $projectWorkCreditSumCompleted];
        }
    }

    private function separateMajor($enrolledCourses, $studentObject, $cGset)
    {
        $magor = [];
        $common = [];
        $majorGPA = 0.00;
        $commonCGPA = 0.00;

        $cgPoint = 0;
        $cgtotalCrdt = 0;

        $mgPoint = 0;
        $mtotalCrdt = 0;

        $gPoint = 0.00;
        $totalCredit = 0;

        $groudGPA = 0.00;
        // check in catalog for major or not.

        $catalogCreditSum = 0;
        $projectWorkCreditSum = 0;

        $remainingCourses = "";

        if ($enrolledCourses && count($enrolledCourses) > 0) {
            $catModel = new CourseCatalog();
            $catalogCourses = $catModel->getCatalogCourse2($cGset);
            $catalogCoursesArray = [];
            $projectCourseWork = [];
            if ($catalogCourses && count($catalogCourses) > 0) {
                foreach ($catalogCourses as $CCKey) {
                    if (strtoupper($cGset->gsProgRel->program_name) == 'MASTERS' && $CCKey->cocat_courseProject == 1) {
                        $projectCourseWork[] = $CCKey->cocatCourseRel->id;
                        $projectWorkCreditSum += $CCKey->cocatCourseRel->course_crdthr;
                    } else {
                        $catalogCoursesArray[] = $CCKey->cocatCourseRel->id;
                        $catalogCreditSum += $CCKey->cocatCourseRel->course_crdthr;
                    }
                }
            }

            $catalogCoursesArray = array_unique($catalogCoursesArray);
            foreach ($enrolledCourses as $ECkey) {
                $cocat = CCF::getCatalogCourse($cGset)->andWhere(['cocat_course_relId' => $ECkey->srd2_course_relId])->one();

                if (!($cocat && count($cocat) > 0)) {
                    continue;
                }
                if (is_null($ECkey->srd2_grade_relId)) {
                    continue;
                }

                if ($ECkey->srd2GradeRel->gs_pass_status == 0) {

                    $projectWorkCreditSum -= $projectWorkCreditSum == 0 ? 0 : $cocat->cocatCourseRel->course_crdthr;
                    continue;
                }

                if (strtoupper($cGset->gsProgRel->program_name) == 'MASTERS' && $cocat->cocat_courseProject == 1) {

                    $catalogCoursesArray = array_diff($catalogCoursesArray, [$ECkey->srd2_course_relId]);
                    continue;
                }

                if (in_array($ECkey->srd2_course_relId, $catalogCoursesArray)) {
                    // unset($catalogCoursesArray[])
                    if ($ECkey->srd2GradeRel->gs_ltr_grade != 'F') {
                        $catalogCoursesArray = array_diff($catalogCoursesArray, [$ECkey->srd2_course_relId]);
                    }
                    if (strtoupper($cGset->gsProgRel->program_name) == 'MASTERS' && $ECkey->srd2GradeRel->gs_pass_status == 1) {
                        $catalogCoursesArray = array_diff($catalogCoursesArray, [$ECkey->srd2_course_relId]);
                    }
                }
                if ($cocat && count($cocat) > 0 && $cocat->cocat_courseMajor == 1) {
                    $major[] = $cocat->cocatCourseRel->id;
                    $mtotalCrdt += $ECkey->srd2_corptd_flag == 1 ? 0 : $ECkey->srd2CourseRel->course_crdthr;
                    $mgPoint += ($ECkey->srd2CourseRel->course_crdthr) * ($ECkey->srd2GradeRel->gs_gradePoint);
                } else {
                    $common[] = $cocat->cocatCourseRel->id;
                    $cgtotalCrdt += $ECkey->srd2_corptd_flag == 1 ? 0 : $ECkey->srd2CourseRel->course_crdthr;
                    $cgPoint += ($ECkey->srd2CourseRel->course_crdthr) * ($ECkey->srd2GradeRel->gs_gradePoint);
                }
            }

            $gPoint = $mgPoint + $cgPoint;
            $totalCredit = $cgtotalCrdt + $mtotalCrdt;

            // calculate GPAs
            $majorGPA = $mgPoint / $mtotalCrdt;
            $commonCGPA = $cgtotalCrdt > 0 ? $cgPoint / $cgtotalCrdt : '-';
            $groudGPA = $gPoint / ($totalCredit);

            if ($catalogCoursesArray && count($catalogCoursesArray) > 0) {
                foreach ($catalogCoursesArray as $RCkey) {
                    $course = \backend\models\Course::findOne($RCkey);
                    $remainingCourses .= $course->course_code . ' => ' . $course->course_title . ' (' . $course->course_crdthr . ')<br>';
                }
            }
            return [$majorGPA, $commonCGPA, $groudGPA, $totalCredit, $catalogCreditSum, $remainingCourses, $projectWorkCreditSum];
        }
    }

    private function getInvalidGradesObtained($studentObj, $cGsetObj, $invalidGrades)
    {

        $vaArray = [];
        $va = \backend\models\GradeScale::find()->where(['NOT IN', 'id', $invalidGrades])->all();
        if ($va && count($va) > 0) {
            foreach ($va as $VAkey) {
                $vaArray[] = $VAkey->id;
            }
        }
        $sst =  $this->find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $studentObj->id,
            ])
            ->andWhere([
                'srd2_grade_relId' => $invalidGrades
            ])
            ->andWhere(['NOT IN', 'srd2_grade_relId', $vaArray])
            ->all();

        return $sst;
    }

    public function checkContinousWrning($prevGsetObj, $stdObj)
    {
        if (is_object($prevGsetObj) && is_object($stdObj)) {
            $semStat = $this->getSemregRecord($stdObj->id, $prevGsetObj->gs_clslvl_relId, $prevGsetObj->gs_sem_relId, $prevGsetObj, 1);
            $probationg = $semStat[2] == 'Probation 1' || $semStat[2] == 'Probation 2' || $semStat[2] == 'Warning';
            if ((strtoupper($prevGsetObj->gsProgRel->program_name) == 'MASTERS') && ($semStat[2] == '2nd Warning')) {
                // warning - waning2 is valid flow for MASTERS program
                // die('Prev_MASTER : {$semStat[2]}');
                return false;
            }
            return (isset($semStat[5]) && ($semStat[5] == 2 || $probationg));
        }
        return false;
    }

    public function updateStatus($groupId)
    {
        $cGsetObj = WGroupSetup::findOne($groupId);
        $students = $cGsetObj->wGroupAssignments;
        foreach ($students as $keyGas) {
            $this->getSemregRecord($keyGas->gas_std_relId, $cGsetObj->gs_clslvl_relId, $cGsetObj->gs_sem_relId, $cGsetObj, NULL, NULL);
        }
    }

    public function getSemregRecord($stdId, $clslvlId, $semesterId, $cGset, $forChange = NULL, $forReadmision = NULL, $acadYearId = NULL)
    {
        // Get students group for that semester and class level.

        // echo "Call to getSemregRecord safe...<br>";
        // die();

        $SGPA = '-';
        $CGPA = '-';
        $status = 'Pending';

        $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semesterId, $stdId, $acadYearId);
        $student = \backend\models\Student::findOne($stdId);
        $gsModel = new WGroupSetup();
        $srd = null;

        if ($gset) {
            $semregObj = null;
            // var_dump($gset, $stdId);
            // die();
            $exists = \backend\models\WGroupAssignment::findOne(['gas_gset_relId' => $gset->id, 'gas_std_relId' => $stdId]);
            if ($exists) {
                // var_dump($exists);
                // exit;

                $submissionCompleted = CCF::checkIfGradeSubmissionCompletedP($gset->id, $clslvlId, $semesterId, $stdId);
                if ($submissionCompleted == 0) { //zero indicates there is no PENGING grade.
                    // 'semreg_clslvl_relId' => $gset->gs_clslvl_relId,
                    $srd = self::find()
                        ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                        ->where([
                            'semreg_acadYear_relId' => $gset->gs_acadYear_relId,
                            'semreg_semester_relId' => $gset->gs_sem_relId,
                            'semreg_gradeCompleted' => 1,
                            'semreg_std_relId' => $stdId
                        ])
                        ->andWhere([
                            'IS NOT',
                            'srd2_grade_relId',
                            NULL
                        ])
                        ->one();

                    // var_dump($srd);exit;

                    if ($srd) {
                        $SGPA = $srd->srd2SemregRel->semreg_SGPA;
                        $CGPA = $srd->srd2SemregRel->semreg_CGPA;


                        $ck = $this->checkIfKafiyaMade($gset, $student, $forReadmision);

                        $semregObj = $srd->srd2SemregRel;
                        if ($ck) {
                            $status = $ck->ssshSssnRel;
                        } else {
                            $statusArray = CCF::getSemesterStatusP($SGPA, $CGPA, $gset->id, $student);
                     
                            if ($statusArray && is_array($statusArray) && isset($statusArray[1])) {
                                if ($statusArray[1] == 'd') {
                                    // Default case.
                                    $default = $statusArray[0];
                                    if ($default) {
                                        // var_dump("default case:", $default->sssccdSssnRel);
                                        $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $default->sssccd_sssn_relId]);

                                        if ($name1) {
                                            if ($name1->sssn_pass_status == 2) { //status if warning, so check for continous warning case.
                                                if ((strtoupper($gset->gsProgRel->program_name) == 'MASTERS') && ($name1->sssn_name == '2nd Warning')) {
                                                } else {
                                                    if ($this->checkContinousWrning($gsModel->getPrevStudentGset($student, $gset), $student)) {
                                                        $name1 = \backend\models\StudentSemesterStatusName::findOne(['sssn_pass_status' => 3, 'sssn_name' => 'Academic Dismissal']);
                                                    }
                                                }
                                            }
                                            $status = $name1;
                                            // sssn_name
                                            if ($name1->sssn_pass_status != 1) {
                                                $ck = $this->checkIfKafiyaMade($gset, $student);
                                                if ($ck && count($ck) > 0) {
                                                    $status = $ck->ssshSssnRel;
                                                }
                                            }
                                        }
                                    }
                                } else if ($statusArray[1] == 'ac') {
                                    $ac = $statusArray[0];
                                    if ($ac && count($ac) > 0) {
                                        $name1 = \backend\models\StudentSemesterStatusName::findOne(['id' => $ac->sssccAC_sssn_relId]);

                                        if ($name1) {
                                            if ($name1->sssn_pass_status == 2) { //status if warning, so check for continous warning case.
                                                if ((strtoupper($gset->gsProgRel->program_name) == 'MASTERS') && ($name1[2] == '2nd Warning')) {
                                                } else {
                                                    if ($this->checkContinousWrning($gsModel->getPrevStudentGset($student, $gset), $student)) {
                                                        // case if Kafiya made for MSTERS students
                                                        $name1 = \backend\models\StudentSemesterStatusName::findOne(['sssn_pass_status' => 3, 'sssn_name' => 'Academic Dismissal']);
                                                    }
                                                }
                                            }
                                            $status = $name1;
                                        }
                                    }
                                }
                            } else {
                                // case 
                                var_dump($semregObj->semregLastSsnRel);
                                die();
                                if ($semregObj->semreg_last_ssn_relId) {
                                    $status = $semregObj->semregLastSsnRel;
                                }
                            }
                        }
                        // var_dump($status);die();
                        if (($semregObj->semregStdRel->std_semester_status != 'GRADUATED') && !is_string($status) && $status && $semregObj->semreg_last_ssn_relId != $status->id) {
                            $semregObj->semreg_last_ssn_relId = $status->id;
                            $semregObj->update();
                        }
                    }
                }
            }

            $CGPA = $CGPA == 0 ? $SGPA : $CGPA;

            if (!is_null($forChange)) {
                if ($cGset && ($cGset->id == $gset->id)) {
                    return [
                        $SGPA,
                        $CGPA,
                        is_object($status) ? $status->sssn_name : $status,
                        is_object($status) ? $status->id : $status,
                        true,
                        is_object($status) ? $status->sssn_pass_status : $status
                    ];
                }
                // if current semester ,  show it by setting [3] = true, and [4] = pass_status,
                return [
                    $SGPA,
                    $CGPA,
                    is_object($status) ? $status->sssn_name : $status,
                    is_object($status) ? $status->id : $status
                ];
            }
            if (is_object($cGset)) {
                if ($cGset->id == $gset->id) { //in this case update student profile
                    // $semregObj = $srd->srd2SemregRel;
                    $stat = is_object($status) ? $status->sssn_name : $status;
                    // if ($semregObj->semregStdRel->std_semester_status != $stat) {
                    //     $semregObj->semregStdRel->std_semester_status = $stat;
                    //     $semregObj->semregStdRel->update();
                    // }

                    if (is_object($status)) {
                        if ($semregObj->semreg_last_ssn_relId != $status->id) {
                            $semregObj->semreg_last_ssn_relId = $status->id;
                            $semregObj->update();
                        }
                    }
                    return [$SGPA, $CGPA, $stat, true, is_object($status) ? $status->sssn_pass_status : $status];
                }
            }
            // if current semester ,  show it by setting [3] = true, and [4] = pass_status,
            return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status];
        }
        return [$SGPA, $CGPA, is_object($status) ? $status->sssn_name : $status];
    }

    private function checkIfKafiyaMade($gsetObject, $stdObject, $forReadmision = NULL)
    {
        // return true;
        if (!is_null($forReadmision)) {
            return \backend\models\StudentSemesterStatusHistory::find()
                ->where([
                    'sssh_acadYear_relId' => $gsetObject->gs_acadYear_relId,
                    'sssh_sem_relId' => $gsetObject->gs_sem_relId,
                    'sssh_clslvl_relId' => $gsetObject->gs_clslvl_relId,
                    'sssh_std_relId' => $stdObject->id,
                    'sssh_approved' => 1
                ])
                ->innerJoin('student_semester_status_name', 'student_semester_status_name.id = student_semester_status_history.sssh_sssn_relId')
                ->andWhere(['student_semester_status_name.sssn_pass_status' => [1, 2, 3]])
                ->one();
        }
        // ->andWhere(['student_semester_status_name.sssn_pass_status'=>1])
        return \backend\models\StudentSemesterStatusHistory::find()
            ->where([
                'sssh_acadYear_relId' => $gsetObject->gs_acadYear_relId,
                'sssh_sem_relId' => $gsetObject->gs_sem_relId,
                'sssh_clslvl_relId' => $gsetObject->gs_clslvl_relId,
                'sssh_std_relId' => $stdObject->id,
                'sssh_approved' => 1
            ])
            ->innerJoin('student_semester_status_name', 'student_semester_status_name.id = student_semester_status_history.sssh_sssn_relId')
            ->one();
        // return ($status && count($status) > 0);
    }
    public function lockGrade($coffId, $courseId, $option, $single = NULL)
    {
        if (Yii::$app->user->can('Registrar')) {
            $result =
                is_null($single) ?
                Yii::$app->db->createCommand()->update('semester_registration_2_detail', ['srd2_gradeLocked' => $option], ['srd2_coff_relId' => $coffId, 'srd2_course_relId' => $courseId, 'srd2_gradeApproved' => 1])->execute()
                :
                Yii::$app->db->createCommand()->update('semester_registration_2_detail', ['srd2_gradeLocked' => $option], ['id' => $coffId, 'srd2_course_relId' => $courseId, 'srd2_gradeApproved' => 1])->execute();

            return 1;
        }
        return 'Error While Locking Grade, INVALID_ROLE_ACTION.';
    }

    public static function deleteEnrolledCourses($gsetId, $stdId)
    {
        $gsetObect = WGroupSetup::findOne($gsetId);
        $stdObject = Student::findOne($stdId);
        $csscc = SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_acadYear_relId' => $gsetObect->gs_acadYear_relId,
                'semreg_semester_relId' => $gsetObect->gs_sem_relId,
                'semreg_std_relId' => $stdObject->id,
            ])
            ->andWhere([
                'srd2_group_relId' => $gsetObect->id
            ])
            ->all();
        if ($csscc && count($csscc) > 0) {
            foreach ($csscc as $key) {
                SemesterRegistration2Detail::findOne($key->id)->delete();
            }
        }
    }

    // Get list of course alergic students
    // 
    public static function getCaStudets()
    {
        $fgraade = GradeScale::findOne(['gs_ltr_grade' => 'F']);
        $modelGs = new GradeScale();
        if (!($fgraade)) {
            return 0;
        }
        $max = [];
        $semreg =  SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_grade_relId' => $fgraade->id,
            ])
            ->andWhere(['<>', 'srd2_corptd_flag', 0])
            ->all();
        if ($semreg && count($semreg) > 0) {
            $invalidGrades = $modelGs->getInvalidGrades($semreg[0]->srd2SemregRel->semregStdRel, $semreg[0]->srd2GroupRel);
            $invalidGradesArray = [];
            if ($invalidGrades && count($invalidGrades) > 0) {
                foreach ($invalidGrades as $keyWT) {
                    $invalidGradesArray[] = $keyWT->id;
                }
            }
            foreach ($semreg as $SRD2key) {
                $clrd = SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where(['NOT IN', 'srd2_grade_relId', $invalidGradesArray])
                    ->andWhere(['srd2_course_relId' => $SRD2key->srd2_course_relId])
                    ->andWhere(['semreg_std_relId' => $SRD2key->srd2SemregRel->semregStdRel->id])
                    ->one();
                if ($clrd) {
                    continue;
                }
                $max[] = $SRD2key;
            }
        }
        return $max;
    }

    public function getRegistrarCAlert()
    {
        $fgraade = GradeScale::findOne(['gs_ltr_grade' => 'F']);
        if (!($fgraade)) {
            return NULL;
        }
        $modelGs = new GradeScale();
        $max = 0;
        $semreg =  SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'srd2_grade_relId' => $fgraade->id,
            ])
            ->andWhere(['<>', 'srd2_corptd_flag', 0])
            ->all();
        if ($semreg && count($semreg) > 0) {
            $invalidGrades = $modelGs->getInvalidGrades($semreg[0]->srd2SemregRel->semregStdRel, $semreg[0]->srd2GroupRel);
            $invalidGradesArray = [];
            if ($invalidGrades && count($invalidGrades) > 0) {
                foreach ($invalidGrades as $keyWT) {
                    $invalidGradesArray[] = $keyWT->id;
                }
            }
            foreach ($semreg as $SRD2key) {
                $clrd = SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where(['NOT IN', 'srd2_grade_relId', $invalidGradesArray])
                    ->andWhere(['srd2_course_relId' => $SRD2key->srd2_course_relId])
                    ->andWhere(['semreg_std_relId' => $SRD2key->srd2SemregRel->semregStdRel->id])
                    ->all();
                if ($clrd && count($clrd) > 0) {
                    continue;
                }
                $max++;
            }
        }

        return $max;
    }

    // get enrolled courses by the student
    public static function getEnrolledCourses($stdObject, $gsetObect)
    {
        if (is_null($stdObject) || is_null($gsetObect)) {
            return NULL;
        } else {
            return SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'semreg_acadYear_relId' => $gsetObect->gs_acadYear_relId,
                    'semreg_semester_relId' => $gsetObect->gs_sem_relId,
                    'semreg_std_relId' => $stdObject->id,
                ])
                ->andWhere([
                    'srd2_group_relId' => $gsetObect->id
                ])
                ->all();
        }
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration_2_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['srd2_semreg_relId', 'srd2_course_relId', 'srd2_group_relId', 'srd2_updated_by', 'srd2_updated_at', 'srd2_created_at', 'srd2_created_by'], 'required'],
            [['srd2_semreg_relId', 'srd2_coff_relId', 'srd2_course_relId', 'srd2_grade_relId', 'srd2_coadd_flag', 'srd2_corptd_flag', 'srd2_codrpd_relId', 'srd2_corProject', 'srd2_enrollApproved', 'srd2_enrollApproved_by', 'srd2_group_relId', 'srd2_updated_by', 'srd2_created_by'], 'integer'],
            [['srd2_enrolApprovrd_at', 'srd2_updated_at', 'srd2_created_at'], 'safe'],
            [['srd2_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\CourseOffering2::class, 'targetAttribute' => ['srd2_coff_relId' => 'id']],
            [['srd2_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Course::class, 'targetAttribute' => ['srd2_course_relId' => 'id']],
            [['srd2_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['srd2_created_by' => 'id']],
            [['srd2_enrollApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['srd2_enrollApproved_by' => 'id']],
            [['srd2_grade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\GradeScale::class, 'targetAttribute' => ['srd2_grade_relId' => 'id']],
            [['srd2_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\WGroupSetup::class, 'targetAttribute' => ['srd2_group_relId' => 'id']],
            [['srd2_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['srd2_semreg_relId' => 'id']],
            [['srd2_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::class, 'targetAttribute' => ['srd2_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'srd2_semreg_relId' => Yii::t('rms', 'Srd2 Semreg Rel ID'),
            'srd2_coff_relId' => Yii::t('rms', 'Srd2 Coff Rel ID'),
            'srd2_course_relId' => Yii::t('rms', 'Srd2 Course Rel ID'),
            'srd2_grade_relId' => Yii::t('rms', 'Srd2 Grade Rel ID'),
            'srd2_coadd_flag' => Yii::t('rms', 'Srd2 Coadd Flag'),
            'srd2_corptd_flag' => Yii::t('rms', 'Srd2 Corptd Flag'),
            'srd2_codrpd_relId' => Yii::t('rms', 'Srd2 Codrpd Rel ID'),
            'srd2_corProject' => Yii::t('rms', 'Srd2 Cor Project'),
            'srd2_enrollApproved' => Yii::t('rms', 'Srd2 Enroll Approved'),
            'srd2_enrollApproved_by' => Yii::t('rms', 'Srd2 Enroll Approved By'),
            'srd2_enrolApprovrd_at' => Yii::t('rms', 'Srd2 Enrol Approvrd At'),
            'srd2_group_relId' => Yii::t('rms', 'Srd2 Group Rel ID'),
            'srd2_updated_by' => Yii::t('rms', 'Srd2 Updated By'),
            'srd2_updated_at' => Yii::t('rms', 'Srd2 Updated At'),
            'srd2_created_at' => Yii::t('rms', 'Srd2 Created At'),
            'srd2_created_by' => Yii::t('rms', 'Srd2 Created By'),
        ];
    }

    public function getHodGradeApprovalRequest()
    {
        if (Yii::$app->user->can('DepartmentHead')) {
            $this->__constructor();
            // ->andWhere([
            //         'semreg_acadYear_relId'=>$this->acadYearId,
            //         'semreg_semester_relId'=>$this->semId,
            //     ])
            $instdept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            return $this->find()
                ->innerJoin('course', 'course.id = semester_registration_2_detail.srd2_course_relId')
                ->where([
                    'course.course_dept_relId' => $instdept->inst_dep_relId
                ])
                ->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
                ->andWhere(['OR', ['srd2_gradeApproved' => 0], ['srd2_gradeApproved' => NULL]])
                ->count();
        }
    }

    public function RegistrarGradeLockNotification()
    {
        $this->__constructor();
        // ->andWhere([
        //         'semreg_acadYear_relId'=>$this->acadYearId,
        //         'semreg_semester_relId'=>$this->semId,
        //     ])
        return $this->find()
            ->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
            ->andWhere(['srd2_gradeApproved' => 1])
            ->andWhere(['srd2_gradeLocked' => 0])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2CoffRel()
    {
        return $this->hasOne(\backend\models\CourseOffering::class, ['id' => 'srd2_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2CourseRel()
    {
        return $this->hasOne(\backend\models\Course::class, ['id' => 'srd2_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2CreatedBy()
    {
        return $this->hasOne(\common\models\User1::class, ['id' => 'srd2_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2EnrollApprovedBy()
    {
        return $this->hasOne(\Common\models\User1::class, ['id' => 'srd2_enrollApproved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2GradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'srd2_grade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2GroupRel()
    {
        return $this->hasOne(\backend\models\WGroupSetup::class, ['id' => 'srd2_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2SemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'srd2_semreg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2Reassessments()
    {
        return $this->hasMany(\backend\models\ReassessmentHistory::class, ['ras_srd2_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThesisTitles()
    {
        return $this->hasMany(\backend\models\ThesisTitle::class, ['tt_srd2_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrd2UpdatedBy()
    {
        return $this->hasOne(\Common\models\User1::class, ['id' => 'srd2_updated_by']);
    }
}
