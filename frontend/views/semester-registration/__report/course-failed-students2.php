<?php

use backend\models\Semester;
use backend\models\SemesterRegistration2Detail;

if (isset($students) && isset($course)) {
    $srd2s = $students;
    if(count($srd2s) < 1){
        echo "<p class='bg-warning'>None</p>";
        return;
    }
    // $course = $srd2s[0]->srd2CourseRel;
    echo "<table border='1' cellspacing='0' cell-padding='3' style='font-size: 9pt;' class='table table-condensed table-hover table-bordered table-sm'>";
    echo "<caption style='font-weight: bold; color: #000; text-align: center;'>
            <h4>Course Failed Students (With Repeat count>=1)</h4>
    </caption>";
    echo "<thead>
            <th>#</th>
            <th>Reg. ID</th>
            <th>Student's Name</th>
            <th>Course Credit</th>
            <th>Course Title</th>
            <th>Rptd</th>
            <th>Pre-requisite for</th>
    </thead><tbody>";
    $count = 0;
    foreach ($srd2s as $index => $value) {
        $srd2 = SemesterRegistration2Detail::findOne($value['SRD2_ID']);
        $course = $srd2->srd2CourseRel;
        $std = $srd2->srd2SemregRel->semregStdRel;
        $credit = $course->getCourseCredits();
        $preq_for = $course->getPreRequisiteForCourses();
        echo "<tr>
            <td>".++$count."</td>
            <td>".$value['REGID']."</td>
            <td>{$std->getFullName()}<b>({$std->stdDeptRel->dept_code})</b></td>
            <td>{$credit}</td>
            <td>{$course->course_title}</td>
            <td style='text-align: center;'>{$value['RPT_COUNT']}</td>
            <td>{$preq_for}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='bg-danger'>Invalid request</p>";
}
