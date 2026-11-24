<?php

namespace frontend\modules\api1\resource;

use backend\models\Student as StudentModel;


class Student extends StudentModel
{
    public function fields()
    {
        return [
            "StudentID" => 'id',
            "REG_NO" => 'std_regId',
            "full_name" => function ($model) {
                return $model->getFullName();
            },
            "gender" => function ($model) {
                return $model->stdUserRel->gender;
            },
            "year_admission" => function ($model) {
                return $model->getYearOfAdmission();
            },
            'program' => function ($model) {
                $admission = $model->stdAdmissionRel ? $model->stdAdmissionRel->admission_name : null;
                $program =  $model->stdProgRel ? $model->stdProgRel->program_name : null;

                return $admission && $program ? "$program($admission[0])" : null;
            },
            'department' => function ($model) {
                $dept = $model->stdDeptRel;
                return $dept ? $dept->dept_name : null;
            },
            'specialization' => function ($model) {
                return $model->stdSpecRel ? $model->stdSpecRel->spec_name : null;
            },
            'total_credits' => function ($model) {
                $totalCredits = $model->getCreditHourSum();
                return ['GPS' => sprintf("%.3f", $totalCredits[0]), 'CGPA' => sprintf("%.3f", $totalCredits[1])];
            },
            'enrolls' => function ($model) {
                $semregs = $model->semesterRegistrations;
                $enrolls = [];
                if (count($semregs) > 0) {
                    foreach ($semregs as $semreg) {
                        $enrolls[] = [
                            'id' => $semreg->id,
                            'acadYear' => $semreg->semregAcadYearRel ? $semreg->semregAcadYearRel->sch_year_desc : null,
                            'semester' => $semreg->semregSemesterRel ? $semreg->semregSemesterRel->semester_name : null,
                            'ClassLevel' => $semreg->semregClslvlRel ? $semreg->semregClslvlRel->clslvl_num : null,
                            'courses' => $semreg->getSemesterRegistration2Details()
                                ->joinWith('srd2CourseRel', false, 'INNER JOIN')
                                ->joinWith('srd2GradeRel', false, 'LEFT JOIN')
                                ->select(['course_code', 'course_title', 'srd2_total_mark as TotalMark', 'srd2_corptd_flag as Repeated', 'gs_ltr_grade as LtrGrade', 'srd2_grade_point_sum as GPS'])
                                ->asArray()
                                ->all(),
                            'courses_count' => count($semreg->getSemesterRegistration2Details()->all()),
                            'SGPA' => sprintf("%.3f", $semreg->semreg_SGPA),
                            'CGPA' => sprintf("%.3f", $semreg->semreg_CGPA),
                            'semester_status' => $semreg->semesterLastSsnRel ? $semreg->semesterLastSsnRel->sssn_name : null,
                        ];
                    }
                }
                return $enrolls;
            },
            // 'prerequisite-for' => function($model){
            //     return $model->getPrerequisiteFor()->select(['id', 'Student_code', 'Student_title'])->asArray()->all();
            //     // return $model->getPreRequisiteForStudents();
            // },
            // 'equivalent-Students' => function ($model) {
            //     return $model->getEquivalentStudents();
            // },
        ];
    }

    public function extraFields()
    {
        return [
            'enrolls' => function ($model) {
                $semregs = $model->getStdSemRegs()->all();
                $enrolls = [];
                if (count($semregs) > 0) {
                    foreach ($semregs as $semreg) {
                        $enrolls[] = [
                            'id' => $semreg->id,
                            'acadYear' => $semreg->semregAcadYearRel ? $semreg->semregAcadYearRel->school_year_desc : null,
                            'semester' => $semreg->semregSemRel ? $semreg->semregSemRel->sem_name : null,
                            'ClassLevel' => $semreg->semregClslvlRel ? $semreg->semregClslvlRel->clslvl_num : null,
                            'courses_count' => function ($model) {
                                return $model->getSemregSrd2s()->count();
                            },
                            'SGPA' => sprintf("%.3f", $semreg->semreg_SGPA),
                            'CGPA' => sprintf("%.3f", $semreg->semreg_CGPA),
                            'semester_status' => $semreg->semesterLastSsnRel ? $semreg->semesterLastSsnRel->sssn_name : null,
                        ];
                    }
                }
                return $enrolls;
            },
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(Student::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(Student::class, ['position_head_relId' => 'id']);
    // }
}
