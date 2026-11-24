<?php

use frontend\modules\GC\models\GcStudent;

if (isset($model) && isset($students)) {
    echo "<div><table class='table table-bordered table-sm table-hover'>";
    echo "<caption style='color: #000; font-weight:nor,mal;'>
            <h3 style='text-align:center;'>Import from GC Academic Programs</h3>
        </caption>";
    echo "<thead>";
    echo "<tr style='background-color: #DAF;'>
            <td>#</td>
            <td>REG.ID</td>
            <td>Student Name</td>
            <td>Last Sem. Reg.</td>
            <td>SGPA</td>
            <td>CGPA</td>
            <td>Status</td>
            <td>Option</td>
        </tr>";
    echo "</thead><tbody>";
    $count = 0;
    foreach ($students as $gas) {
        if(GcStudent::alreadyImported($gas->gas_std_relId)){continue;}
        $student = $gas->gasStdRel;
        $lastSemreg = $student->getLastSemreg();
        $lastEntry = "{$lastSemreg->semregAcadYearRel->sch_year_desc}-{$lastSemreg->semregSemesterRel->semester_name}/{$lastSemreg->semregClslvlRel->getClslvlDisp()}";
        $option = "<input type='checkbox' onchange='addToGcList({$student->id});return false;' title='Add to GC list'/>";
        $sgpa = sprintf("%0.3f", $lastSemreg->semreg_SGPA);
        $cgpa = sprintf("%0.3f", $lastSemreg->semreg_CGPA);
        $last_status = $lastSemreg->semesterLastSsnRel ? $lastSemreg->semesterLastSsnRel->sssn_name : $student->std_semester_status;

        echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$student->std_regId}</td>
                <td>{$student->getFullName()}</td>
                <td>{$lastEntry}</td>
                <td>{$sgpa}</td>
                <td>{$cgpa}</td>
                <td>{$last_status}</td>
                <td>{$option}</td>
            </tr>";
    }
    echo "</tbody></table>";
    echo "<p class='bg-info' style='padding: 12px;'>
        Selected: <small class='bg-success' id='selected_count'>0</small>
        <button class='btn btn-xs btn-primary pull-right' onclick='submitGcList({$model->id});return false;' title='save selected gc list'>Submit</button>
        <br>
    </p></div>";
} else {
    echo "<p class='bg-warning'>Invalid Request</p>";
}
