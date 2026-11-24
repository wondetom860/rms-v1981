<?php

use backend\models\CourseCatalog;
use backend\models\Student;

if (isset($list)) {
    $catModel = new CourseCatalog();
    // border=1 cellspacing=2 style='width: 100%'
    // class='table table-bordered table-condensed table-sm table-striped'  style='font-size: 9pt;'
    echo "<table border=1 cellspacing=2 cellpad=2 style='width: 100%'>";
    echo "<thead style='background-color:rgb(228, 221, 221)'>
            <tr>
                <th rowspan=2>#</th>
                <th rowspan=2>REG. ID</th>
                <th rowspan=2>NAME</th>
                <th rowspan=2>GENDER</th>
                <th rowspan=2>PROFESSION</th>
                <th rowspan=2>DEPARTMENT</th>
                <th rowspan=2>PROGRAM</th>
                <th colspan=7 style='text-align: center;'>GRADES</th>
                <th rowspan=2>COURSES</th>
                <th rowspan=2>THESIS GRADE</th>
                <th rowspan=2>GPS</th>
                <th rowspan=2>CGPA</th>
                <th rowspan=2>REMARK</th>
            </tr>
            <tr style='text-align: center;'>
                <th style='text-align:  center;'>A+</th>
                <th style='text-align:  center;'>A</th>
                <th style='text-align:  center;'>A-</th>
                <th style='text-align:  center;'>B+</th>
                <th style='text-align:  center;'>B</th>
                <th style='text-align:  center;'>B-</th>
                <th style='text-align:  center; background-color: red;'>NG|F</th>
            </tr>
        </thead><tbody>";
    $count = 0;
    foreach ($list as $key => $value) {
        $student = Student::findOne($value['ID']);
        $student->calculateGPAs();
        $lastSemreg = $student->getLastSemreg();
        $cgpa = $lastSemreg->semreg_last_ssn_relId ? sprintf("%0.3f", $lastSemreg->semreg_CGPA) : '-';
        $gps = $value['GPSUM'] ? sprintf("%0.2f", $value['GPSUM']) : '-';
        $projectCourseGrade = "-";
        $projectCourseSrd2 = NULL;
        foreach ($lastSemreg->semesterRegistration2Details as $srd2) {
            if (is_project($srd2, $catModel)) {
                // var_dump($srd2);exit;
                $projectCourseSrd2 = $srd2;
                $projectCourseGrade = $srd2->srd2GradeRel->gs_ltr_grade ?? "-";
                break;
            }
        }

        $ngFs = "-";
        $unclearedFs = $lastSemreg->semesterRegistration2Details[0]->getNgFs($student, $student->stdGroupReal);
        $ngFs = $unclearedFs ? count(explode('-', $unclearedFs)) : '-';
        // explode()
        // $remark = $unclrd[1];
        // $ngF = $unclrd[0];
        // $ng = $ngF['ngs'] ?? 0;
        // $f = $ngF['Fs'] ?? 0;

        // $ngFs = "{$ng}|{$f}";


        $prof = strtoupper($value['PROFESSION'] == 'FOREGN' ? "FOREIGN" : $value['PROFESSION']);
        echo "<tr>
            <td>" . ++$count . "</td>
            <td>{$value['REGID']}</td>
            <td>" . strtoupper($value['FULL_NAME']) . "</td>
            <td>" . strtoupper($value['GENDER']) . "</td>
            <td>{$prof}</td>
            <td>" . strtoupper($value['DEPARTMENT']) . "</td>
            <td>" . strtoupper($value['PROGRAM']) . "</td>
            <td style='text-align: center;'>{$value['GR_A_PLUS']}</td>
            <td style='text-align: center;'>{$value['GR_A']}</td>
            <td style='text-align: center;'>{$value['GR_A_MINUS']}</td>
            <td style='text-align: center;'>{$value['GR_B_PLUS']}</td>
            <td style='text-align: center;'>{$value['GR_B']}</td>
            <td style='text-align: center;'>{$value['GR_B_MINUS']}</td>
            <td style='text-align: center;'>{$ngFs}</td>
            <td style='text-align: center;'>{$value['TOTAL_NUMBER_OF_COURSES_ENROLLED']}</td>
            <td style='text-align: center;'>{$projectCourseGrade}</td>
            <td style='text-align: center;'>{$gps}</td>
            <td style='text-align: center;'>{$cgpa}</td>
            <td style='text-align: center;'>-</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='bg-warning'>Invalid request<p>";
    return;
}

function is_project($srd2, $catModel)
{
    foreach (getCatalogEntries($srd2, $catModel) as $cocat) {
        if ($cocat->cocat_courseProject == 1) {
            return true;
        }
    }
    return false;
}
function getCatalogEntries($srd2, $catModel)
{
    $catalogCourses = $catModel->getCatalogCourse2($srd2->srd2GroupRel);
    $courseCatalogEntries = array_filter($catalogCourses, function ($tt) use ($srd2) {
        return $tt->cocat_course_relId == $srd2->srd2_course_relId;
    });

    return $courseCatalogEntries;
}

?>

<style>
    th,
    td {
        padding-top: 1px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 4px;
    }
</style>