<?php

namespace backend\models;

use yii;
use backend\models\Student;

class PreRequisitController extends \yii\base\Model
{
    // returns NULL or the pre-requisit course of the course.
    // Takes the main course id as a parameter @course instance
    // For semester registration purpose.

    public function safeOfPrerequisit($course, $stdObj, $gsetObject)
    {
        $pass = TRUE;
        $message = "";
        $preqCourses = self::hasPrerequisit($course->id, $stdObj, $gsetObject);
        // var_dump($preqCourses,$course);exit;
        // if ($course->course_code == "ECNG 4111") {
        //     var_dump($stdObj->std_group_realId ,$gsetObject->id);
        //     exit;
        // }
        if ($preqCourses && count($preqCourses) > 0) {

            foreach ($preqCourses as $keypreqId) {
                $preqCourse = $keypreqId->preqPreqCourseRel;
                // transfer courses has equivalent courses.
                $pEnrol = self::enrolledPreqCourse($preqCourse->id, $stdObj);
                if ($pEnrol && count($pEnrol)) {
                    foreach ($pEnrol as $key) {
                        if ($key->srd2GradeRel && self::gradeValid($key->srd2GradeRel->gs_ltr_grade)) {
                            $pass = $pass == FALSE ? FALSE : TRUE;
                        } else {
                            // check if cleared.
                            if ($key->clearedFailGrade()) {
                                $pass = TRUE;
                            } else {
                                // check if equivalent course is enrolled and graded.
                                if (self::checkIfEquivalentEnrolled($key->srd2CourseRel, $stdObj)) {
                                    $pass = TRUE;
                                } else {
                                    $key = $key->srd2GradeRel ? $key->srd2GradeRel->gs_ltr_grade : '-';
                                    $message .= "{$course->course_title} has pre-requisit course <i><b title='{$preqCourse->getCourseCredits()}'> {$preqCourse->course_title}</b></i>. Grade Obtained: {$key}<br>";
                                    $pass = FALSE;
                                }
                            }
                        }
                    }
                } else {
                    // Prerequisite not enrolled, check whether its equivalen is or not.
                    if (self::checkIfEquivalentEnrolled($course, $stdObj)) {
                        $pass = TRUE;
                    } else {
                        $message .= "{$course->course_title} has Pre-requisit course <i title='{$preqCourse->course_code}'><b>{$preqCourse->course_title}</b></i>, Course Not Enrolled<br>";
                        $pass = FALSE;
                    }
                }
            }
        } else {
            $pass = TRUE;
        }
        return [$pass, $message];
    }

    public static function checkIfEquivalentEnrolled($course, $stdObj)
    {
        $equivCourseIds = $course->course_equivalet ? array_values(json_decode($course->course_equivalet, true)) : NULL;
        if ($equivCourseIds) {
            return SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->innerJoinWith('srd2GradeRel')
                ->where([
                    'semester_registration_2.semreg_std_relId' => $stdObj->id,
                    'semester_registration_2_detail.srd2_course_relId' => $equivCourseIds
                ])
                ->andWhere([
                    'gs_pass_status' => 1
                ])
                ->one();
        } else {
            return false;
        }
    }
    public static function hasPrerequisit($courseId, $stdObject, $gsetObject)
    {

        $std = $stdObject;

        $gset = $gsetObject;
        $preCo =  \backend\models\CoursePrerequisit::find()
            ->where([
                'preq_main_course_relId' => $courseId
            ])
            ->andWhere([
                'OR',
                ['preq_adm_relId' => $gsetObject->gs_adm_relId],
                ['preq_adm_relId' => NULL]
            ])
            ->andWhere([
                'OR',
                ['preq_prog_relId' => $gsetObject->gs_prog_relId],
                ['preq_prog_relId' => NULL]
            ])
            ->andWhere([
                'OR',
                ['preq_dept_relId' => $gset->gs_dept_relId],
                ['preq_dept_relId' => NULL]
            ]);

        if ($gset->gs_spec_relId) {
            $preCo->andWhere(['OR', ['preq_spec_relId' => $gset->gs_spec_relId], ['preq_spec_relId' => NULL]]);
        }

        return $preCo->andWhere(' preq_preq_course_relId IS NOT NULL ')->all();
    }

    // returns NULL or the pre-requisit course of the course.
    // Takes the main course id as a parameter @courseId 
    public static function hasPrerequisit2($cocatObj)
    {
        $preCo =  \backend\models\CoursePrerequisit::find()
            ->where([
                'preq_main_course_relId' => $cocatObj->cocat_course_relId
            ])
            ->andWhere([
                'OR',
                ['preq_adm_relId' => $cocatObj->cocat_admission_relId],
                ['preq_adm_relId' => NULL]
            ])
            ->andWhere([
                'OR',
                ['preq_prog_relId' => $cocatObj->cocat_program_relId],
                ['preq_prog_relId' => NULL]
            ])
            ->andWhere([
                'OR',
                ['preq_dept_relId' => $cocatObj->cocat_dept_relId],
                ['preq_dept_relId' => NULL]
            ]);

        if ($cocatObj->cocat_spec_relId) {
            $preCo->andWhere(['OR', ['preq_spec_relId' => $cocatObj->cocat_spec_relId], ['preq_spec_relId' => NULL]]);
        }
        return $preCo->groupBy('preq_preq_course_relId')->all();
    }

    // checks if the course is enrolled or not.
    // returns the course object if enrolled or NULL else.
    public static function enrolledPreqCourse($courseId, $stdObject)
    {
        // check if $courseId or its equivalent is enrolled.
        $course = Course::findOne($courseId);
        $equivCourseIds = $course->course_equivalet ? array_values(json_decode($course->course_equivalet, true)) : [];
        $courseIds = array_merge($equivCourseIds, [$courseId]);
        return SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semester_registration_2.semreg_std_relId' => $stdObject->id,
                'semester_registration_2_detail.srd2_course_relId' => $courseIds
            ])
            ->all();
    }

    // Checks if the grade obtained for a course is valid or not.
    // returns True or false.
    public static function gradeValid($grade)
    {
        return !(is_null($grade) || $grade == 'NG' || $grade == 'I' || $grade == 'F');
    }
}
