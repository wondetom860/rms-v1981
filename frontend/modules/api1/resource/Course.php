<?php

namespace frontend\modules\api1\resource;

use backend\models\Course as CourseModel;


class Course extends CourseModel
{
    public function fields()
    {
        return [
            "COURSE_ID" => 'id',
            "COURSE_CODE" => 'course_code',
            "COURSE_TITLE" => 'course_title',
            "course_credits" => function ($model) {
                return $model->getCourseCredits();
            },
            "course_dept" => function ($model) {
                $dept = $model->courseDeptRel;
                return $dept ? $dept->dept_name : null;
            },
            'type' => function ($model) {
                return $model->getCourseEntryType();
            },
            'grading' => function ($model) {
                return $model->getCourseTypeName();
            },
            // 'prerequisite-for' => function($model){
            //     return $model->getPrerequisiteFor()->select(['id', 'course_code', 'course_title'])->asArray()->all();
            //     // return $model->getPreRequisiteForCourses();
            // },
            'equivalent-courses' => function($model){
                return $model->getEquivalentCourses();
            },
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(Course::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(Course::class, ['position_head_relId' => 'id']);
    // }
}
