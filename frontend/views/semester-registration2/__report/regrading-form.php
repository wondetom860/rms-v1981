<?php
if (isset($model)) {
    echo "<h4 style='text-align: center;'>Course Regrading Form: <small>Please choose the course to regrade</small></h4>";
    echo "<table class='table table-sm table-condensed table-bordered table-hover' style='font-size: 9pt;'>";
    $total = 0;
    $sgpa = sprintf("%0.3f", $model->semreg_SGPA);
    $cgpa = sprintf("%0.3f", $model->semreg_CGPA);
    foreach ($model->semesterRegistration2Details as $srd2) {
        // check catalog entry
        $below_min_grade = $srd2->catalogMinPassGradeNotAchieved();
        $reqButton = !$srd2->notRequested() && (($srd2->srd2GradeRel->gs_gradePoint <= 2.5) || $below_min_grade)  ? "<button class='btn btn-xs btn-warning btn-warning' onclick='initiateRegrade({$srd2->id}); return false;'>Regrade</button>" : '-';
        $tot_mark = sprintf("%0.2f", $srd2->srd2_total_mark);
        echo "<tr>
                <td style='white-space: nowrap'>{$srd2->srd2SemregRel->getSemregHeader()}</td>
                <td style='white-space: nowrap'>{$srd2->srd2CourseRel->getCourseCredits()}</td>
                <td style='white-space: nowrap'>{$srd2->srd2SemregRel->semregClslvlRel->getClslvlDisp()}</td>
                <td>{$srd2->srd2CourseRel->course_title}</td>
                <td>{$tot_mark}</td>
                <td>{$srd2->srd2GradeRel->gs_ltr_grade}</td>
                <td>{$reqButton}</td>
        </tr>";
    }
    echo "<tr>
            <td colspan=4 style='text-align: center; font-weight: bold;'>Total</td>
            <td style='text-align: center; font-weight: bold;'>SGPA={$sgpa}</td>
            <td style='text-align: center; font-weight: bold;'>CGPA={$cgpa}</td>
            <td></td>
        </tr>";
    echo "</table>";
} else {
    echo "<p class='bg-warning'>Invalid access</p>";
    return;
}

?>

<script>
    window.initiateRegrade = (srd2Id) => {
        if (confirm('Are you sure to initiate regrading process?')) {
            $.post('/reassessment/request-regrading', {
                srd2Id: srd2Id
            })
        }
    }
</script>