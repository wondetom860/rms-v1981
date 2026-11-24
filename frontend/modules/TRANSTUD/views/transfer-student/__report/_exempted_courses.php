<?php

use backend\models\Course;
use yii\helpers\Html;

if (isset($model)) {
    $exemptedCourses = $model->exemptedCourses;
    $btnAdd = '';
    if ($model->approved == 1) {
        if (Yii::$app->user->can('Registrar')) {
            $btnAdd = '<button class="btn btn-xs btn-warning pull-right" title="Cancel Approval for this record" onclick="changeApproveStatus(' . $model->id . ',2);return false;">Cancel</button>';
        }
    } else {
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')) {
            $btnAdd = Html::a(Yii::t('rms', 'Add Exempted Course'), ['#'], ['class' => 'btn btn-success btn-xs pull-right', 'onclick' => "addExemptedCourses({$model->id}); return false;"]);
        }
        if (Yii::$app->user->can('Registrar') && ($model->approved != 1)) {
            $btnAdd .= '<button class="btn btn-xs btn-primary pull-right" style="margin: 0px 4px;" title="Approve this record" onclick="changeApproveStatus(' . $model->id . ',1);return false;">Approve</button>';
        }
    }

    echo "<h4 style='text-align: center;'>" . Yii::t('rms', 'Exempted Courses') . "{$btnAdd}</h4>";
    if (!empty($exemptedCourses)) {
        echo '<table class="table table-bordered">';
        echo "
            <thead style='background-color: #679693ff;'>
            <tr>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Course Hr.</th>
                <th>Grade</th>
                <th>Grade Scale</th>
                <th>Grade Point</th>
                <th>Equivalent Course(s)</th>
                <th>Option</th>
                <th>Remark</th>
            </tr>
            </thead><tbody>";
        $count = 0;
        $course_hour_sum = 0;
        $grade_point_sum = 0;
        foreach ($exemptedCourses as $course) {
            $gps = sprintf('%.2f', $course->grade_point_sum);
            $btnAddEquivalent = '';
            $remark = $course->approved ? Yii::t('rms', 'Approved') : Yii::t('rms', 'Pending Approval');
            if ($course->approved != 1) {
                $btnAddEquivalent = "<button class='btn btn-primary pull-right btn-xs' title='Add equivalent Course from main catalog' onclick='addEquivalentCourse({$course->id});return false;'>Add</button>";
            } else {
            }
            $eqCourses = json_decode($course->equivalent_course_ids, true);
            $eq_c = '';
            if ($eqCourses) {
                foreach ($eqCourses as $eqCourseId) {
                    $eqCourse = Course::findOne($eqCourseId);
                    if ($eqCourse) {
                        $btn = ($course->approved != 1) ? "<button class='btn btn-xs btn-warning pull-right' style='padding: 2px; margin: auto; height: 20px;' onclick='removeEquivCourse({$course->id},{$eqCourse->id});return false;' title='Remove from equivlent courses'>&times;</button>" : '';
                        $eq_c .= "<p style='font-size: 9pt; padding: 2px;' title='{$eqCourse->course_code}'><a href='/course/view?id={$eqCourse->id}'>{$eqCourse->course_title}</a>{$btn}</p>";
                        // return "<span class='label bg-default' title='{$c->course_title}'>{$c->course_code}</span>";
                    }
                }
            }

            $course_hour_sum += $course->course_hours;
            $grade_point_sum += $course->grade_point_sum;
            echo "<tr>";
            echo '<td>' . ++$count . '</td>';
            echo '<td>' . Html::a(Html::encode($course->course_code), ['/TRANSTUD/exempted-course/view', 'id' => $course->id], ['target' => '_blank']) . '</td>';
            echo '<td>' . Html::encode($course->course_name) . '</td>';
            echo '<td style="text-align:center; font-weight: bold;">' . Html::encode($course->course_hours) . '</td>';
            echo '<td style="text-align:center; font-weight: bold;">' . Html::encode($course->grade_obtained) . '</td>';
            echo '<td style="text-align:center; font-weight: bold;">' . Html::encode($course->grade_point) . '</td>';
            echo '<td style="text-align:center; font-weight: bold;">' . Html::encode($gps) . '</td>';
            echo '<td>' . "{$eq_c}</td>";
            echo "<td>{$btnAddEquivalent}</td>";
            echo '<td></td>';
            echo "</tr>";
            // $gpa
        }

        $gpa = $course_hour_sum > 0 ? sprintf('%.2f', ($grade_point_sum / $course_hour_sum)) : '0.00';
        echo "<tr style='font-weight: bold; background-color: #e5a6a6ff; font-style: italic;'>
                <td style='text-align: center; text-decoration: underline;' colspan=3>Total</td>
                <td style='text-align: center; text-decoration: underline;'>{$course_hour_sum}</td>
                <td style='text-align: center; text-decoration: underline;' colspan=2>{$gpa}</td>
                <td style='text-align: center; text-decoration: underline;'>{$grade_point_sum}</td>
            </tr>";
        echo '</tbody></table>';
    } else {
        echo '<p>' . Yii::t('rms', 'No exempted courses found.') . '</p>';
    }
} else {
    echo '<p>' . Yii::t('rms', 'Model not set.') . '</p>';
}
