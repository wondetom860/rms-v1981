<?php
/**
 * @var backend\models\CourseOfferingTemp $model
 */
use backend\models\CourseCatalog;

if (isset($model)) {

    $courses = json_decode($model->cot_cocat_IDS, true);

    if ($courses) {
        echo "<table class='table table-condensed table-sm table-bordered table-hover' style='font-size:9pt;'>";
        echo "<thead style='background-color:#8D0;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Department</th>
                    <th>Credit Hrs.</th>
                    <th>Major?</th>
                    <th>Entry</th>
                    <th>Remark</th>
                </thead><tbody>";
        $count = 0;
        $sum1 = $sum2 = $sum3 = $sum4 = 0;
        foreach ($courses as $cocatKey) {
            $cocat = CourseCatalog::findOne($cocatKey);
            if ($cocat) {
                $course = $cocat->cocatCourseRel;
                $crdt = $course->getCourseCredits();
                $entry = $course->getCourseEntryType();
                $sum1 += $course->course_crdthr;
                $sum2 += $course->course_lecture_hour;
                $sum3 += $course->course_lab_hour;
                $sum4 += $course->course_tutorial_hour;
                $major = $cocat->cocat_courseMajor ? "YES" : "NO";
                echo "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$course->course_code}</td>
                        <td>{$course->course_title}</td>
                        <td>{$course->courseDeptRel->dept_name}</td>
                        <td>{$crdt}</td>
                        <td>{$major}</td>
                        <td>{$entry}</td>
                        <td></td>
                    </tr>";
            }
        }
        echo "<tr style='text-align:center; font-weight:bold; font-style:italic; background-color:#ACE;'>
                    <td colspan=4>Total Credit</td>
                    <td>{$sum1}-{$sum2}-{$sum3}/{$sum4}</td>
                    <td></td>
                    <td></td>
                </tr>";
        echo "</tbody></table>";
    } else {
        echo "<p class='bg-warning'>No course offered</p>";
    }
} else {
    echo "<p class='bg-danger'>No record</p>";
}
