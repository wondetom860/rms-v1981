<?php

namespace frontend\modules\api1\resource;

use frontend\modules\AcademicCalendar\models\AcActivePrograms as ProgramModel;

// AcActivePrograms
class Program extends ProgramModel
{
    public function fields()
    {
        return [
            "id" => 'id',
            "AcademicYear" => function ($model) {
                return $model->getAcadYearHeader();
            },
            "Department" => function ($model) {
                return $model->deptRel->dept_name;
            },
            "ClassLevel" => function ($model) {
                return $model->getClassLevel();
            },
            "Specialization" => function ($model) {
                return $model->spec_relId ? $model->specRel->spec_name : null;
            },
            "sections" => function ($model) {
                $secs = [];
                foreach ($model->groupSetups as $gset) {
                    $secs[] = [
                        "SectionId" => $gset->id,
                        'SectionName' => $gset->gsGroupRel->group_name,
                        'ClassLevel' => $gset->gsClslvlRel->getClslvlDisp(),
                        'SectionDetail' => $gset->getGsDisp(),
                        "StudentsCount" => count($gset->wGroupAssignments),
                    ];
                }
                return $secs;
            },
            "SectionsCount" => function ($model) {
                return count($model->groupSetups);
            },
            'TotalStudents' => function ($model) {
                $cc = 0;
                foreach ($model->groupSetups as $gset) {
                    $cc += count($gset->wGroupAssignments);
                }

                return $cc;
            },
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(Program::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(Program::class, ['position_head_relId' => 'id']);
    // }
}
