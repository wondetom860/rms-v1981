<?php

use backend\models\Course;

/**
 * @var backend\models\Course $eqCourses
 * @var frontend\modules\TRANSTUD\models\ExemptedCourse $model
 */
$eq_c = '';
if ($eqCourses) {
    foreach ($eqCourses as $eqCourseId) {
        $eqCourse = Course::findOne($eqCourseId);
        if ($eqCourse) {
            $btn = "<button class='btn btn-xs btn-warning pull-right' style='padding: 2px; margin: auto; height: 20px;' onclick='removeEquivCourse({$model->id},{$eqCourse->id});return false;' title='Remove from equivlent courses'>&times;</button>";
            $eq_c .= "<p style='padding: 2px;' title='{$eqCourse->course_code}'><a href='/course/view?id={$eqCourse->id}'>{$eqCourse->course_title}</a>{$btn}</p>";
            // return "<span class='label bg-default' title='{$c->course_title}'>{$c->course_code}</span>";
        }
    }
}

echo $eq_c;
