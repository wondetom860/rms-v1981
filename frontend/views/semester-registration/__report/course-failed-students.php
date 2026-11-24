<?php

if (isset($students) && isset($course)) {
    $srd2s = $students;
    if(count($srd2s) < 1){
        echo "<p class='bg-warning'>None</p>";
        return;
    }
    // $course = $srd2s[0]->srd2CourseRel;
    echo "<table border='1' cellspacing='0' cell-padding='3' style='font-size: 9pt;' class='table table-condensed table-hover table-bordered table-sm'>";
    echo "<caption style='font-weight: bold; color: #000; text-align: center;'>
            <h4>Course Failed Students</h4>
            Course Title : {$course->course_title}: ({$course->getCourseCredits()})
    </caption>";
    echo "<thead>
            <th>#</th>
            <th>Reg. ID</th>
            <th>Student's Name</th>
            <th>Registration ACY</th>
            <th>Total Mark(100%)</th>
            <th>Grade</th>
            <th>Remark</th>
    </thead><tbody>";
    $count = 0;
    foreach ($srd2s as $srd2) {
        // if(!$srd2->catalogMinPassGradeNotAchieved()){continue;}
        $srd2->updateSrd3();
        // $srd2->refresh();
        if($srd2->srd2_pass_status == 1){continue;}
        $grade = $srd2->srd2_grade_relId ? $srd2->srd2GradeRel->gs_ltr_grade : '-';
        $semreg = $srd2->srd2SemregRel;
        $rem = $srd2->clearedFailGrade() ? 'Cleared' : '';
        $sACY = $semreg->semregAcadYearRel->sch_year_desc."-".$semreg->semregSemesterRel->semester_name;
        echo "<tr>
            <td>".++$count."</td>
            <td>".$semreg->semregStdRel->std_regId."</td>
            <td>{$semreg->semregStdRel->getFullName()}<b>({$semreg->semregStdRel->stdDeptRel->dept_code})</b></td>
            <td>{$sACY}</td>
            <td>{$srd2->srd2_total_mark}</td>
            <td style='text-align: center;'>{$grade}</td>
            <td>{$rem}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='bg-danger'>Invalid request</p>";
}
