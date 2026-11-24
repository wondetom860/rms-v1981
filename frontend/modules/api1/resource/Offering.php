<?php

namespace frontend\modules\api1\resource;

use backend\models\CourseOffering2 as CourseOfferingModel;


class Offering extends CourseOfferingModel
{
    public function fields()
    {
        return [
            "id" => 'id',
            "AcademicYear" => function ($model) {
                return $model->coff2detAcProgRel->getAcadYearHeader();
            },
            "Department" => function ($model) {
                return $model->coff2GroupRel->gsDeptRel->dept_name;
            },
            "ClassLevel" => function ($model) {
                return $model->coff2detAcProgRel->getClassLevel();
            },
            "Specialization" => function ($model) {
                return $model->coff2detAcProgRel->spec_relId ? $model->coff2detAcProgRel->specRel->spec_name : null;
            },
            'Courses' => function ($model) {
                $courses = [];
                foreach ($model->courseOffering2Details as $offering) {
                    $course = $offering->coff2detCourseRel;
                    $courses[] = [
                        'course_offering_id' => $offering->id,
                        'course_code' => $course->course_code,
                        'course_title' => $course->course_title,
                        'Instrcutor' => $offering->coff2detInstRel ? $offering->coff2detInstRel->getFullName() : null,
                        'TA' => $offering->coff2detTARel ? $offering->coff2detTARel->getFullName() : "NA",
                        "enrollsCount" => $offering->getEnrollsCount(),
                    ];
                }
                return $courses;
            }
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(CourseOffering::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(CourseOffering::class, ['position_head_relId' => 'id']);
    // }
}
