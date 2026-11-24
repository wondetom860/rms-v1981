<?php

namespace frontend\modules\api1\resource;

use backend\models\CourseOffering2Detail;
use backend\models\Instructor as InstructorModel;


class Instructor extends InstructorModel
{
    public function fields()
    {
        return [
            "id" => 'id',
            "INST_DEPT" => function ($model) {
                return [
                    'DEPT. ID' => $model->inst_dep_relId,
                    'DEPT. CODE' => $model->instDepRel->dept_code,
                    "DEPT. NAME" => $model->instDepRel->dept_name,
                ];
            },
            "FullName" => function ($model) {
                return $model->getFullName();
            },
            "AcademicRank" => function ($model) {
                return $model->getAcrRankDisp();
            },
            "AcademicQualification" => function ($model) {
                return $model->getAcqQualDisp();
            },
            'offerings' => function ($model) {
                $offs = [];
                foreach (CourseOffering2Detail::find()->where(['coff2det_inst_relId' => $model->id])->all() as $offering) {
                    $course = $offering->coff2detCourseRel;
                    $acp = $offering->coff2detCoff2Rel->coff2detAcProgRel;
                    $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
                    $offs[] = [
                        'course_offering_id' => $offering->id,
                        'course_code' => $course->course_code,
                        'course_title' => $course->course_title,
                        'academic_year' => $offering->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc . '-' . $offering->coff2detCoff2Rel->coff2SemRel->semester_name,
                        'ClassLevel' => $acp->getClassLevel(),
                        'Program' => $acp->getProgramHeader(),
                        'Section' => $gset->getGsDisp() . "({$gset->gsGroupRel->group_name})",
                    ];
                }
                return $offs;
            }
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(Instructor::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(Instructor::class, ['position_head_relId' => 'id']);
    // }
}
