<?php
/**
 * @var backend\models\CourseOffering2 $model
 */
use backend\models\CourseCatalog;

if (isset($model)) {

    $offeringDetails = $model->courseOffering2Details;
    // $addBtn = "<a href='#' class='btn btn-primary btn-xs pull-right' onclick='addOffering({$model->id});return false;'>Add Course</a>";
    if ($offeringDetails) {
        echo "<table class='table table-condensed table-sm table-bordered table-hover' style='font-size:9pt;'>";
        echo "<caption style='font-weight:bolder; color:#000; text-align: center; background-color: #8EF;'>
                    <h4>Courses Offered</h4>
                    <p>Academic Year: {$model->coff2AcadYearRel->sch_year_desc} | Semester: {$model->coff2SemRel->semester_name} | Group: {$model->coff2GroupRel->gsGroupRel->group_name} | Program: {$model->coff2GroupRel->gsProgRel->program_name} | Department: {$model->coff2GroupRel->gsDeptRel->dept_name} | Class Level: {$model->coff2GroupRel->gsClslvlRel->getClslvlDisp()} | Admission: {$model->coff2GroupRel->gsAdmRel->admission_name}</p>
            </caption>";
        echo "<thead style='background-color:#8D0;'>
                    <th>#</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Department</th>
                    <th>Credit Hrs.</th>
                    <th>Type</th>
                    <th>Major?</th>
                    <th>Remark</th>
                </thead><tbody>";
        $count = 0;
        $sum1 = $sum2 = $sum3 = $sum4 = 0;
        foreach ($offeringDetails as $offeringDetail) {
            $course = $offeringDetail->coff2detCourseRel;
            $cocat = $offeringDetail->coff2detCocatRel;
            if ($cocat) {
                // $course = $cocat->cocatCourseRel;
                $type = $course->course_entry_type ? "Regular" : "Transfer";
                $crdt = $course->getCourseCredits();
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
                        <td>{$type}</td>
                        <td>{$major}</td>
                        <td></td>
                    </tr>";
            }
        }
        echo "<tr style='text-align:center; font-weight:bold; font-style:italic; background-color:#ACE;'>
                    <td colspan=4>Total Credit</td>
                    <td>{$sum1}-{$sum2}-{$sum3}/{$sum4}</td>
                    <td></td>
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
