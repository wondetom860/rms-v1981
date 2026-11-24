<?php

if ($model) {
    $deptName = "-";
    $clslvl = "-";
    $progName = "-";
    $admission = "-";
    if (isset($printParams['semreg_clslvl_relId']) && !empty($printParams['semreg_clslvl_relId'])) {
        $clslvl = $model[0]->semregClslvlRel->getClslvlDisp();
        $admission = $model[0]->semregStdRel->stdAdmissionRel->admission_name;
        $progName = $model[0]->semregClslvlRel->clslvlProgramRel->program_name;
    }

    // display department offered the coure
    if (isset($printParams['std_dept_relId']) && !empty($printParams['std_dept_relId'])) {
        $dept = \backend\models\Department::findOne(intval($printParams['std_dept_relId']));
        // $adm = \backend\models\Admission::findOne(intval($printParams['std']));
        $deptName = $dept ? $dept->dept_name : '-';
    }

    // echo "<br>";

    // display program offered the coure
    if (isset($printParams['std_prog_relId']) && !empty($printParams['std_prog_relId'])) {
        $prog = \backend\models\Program::findOne(intval($printParams['std_prog_relId']));
        $progName = $prog ? $prog->program_name : '-';
    }

    echo "<h4 style='text-align: center;'>Active Students</h4>";
    echo "<table class='table table-condensed table-striped table-hover table-bordered'>";
    echo "<caption style='color: #000; font-weight: normal; text-align: center; font-size: 10pt; padding-left: 9px;'>
            <b>Institution Name</b>: <u>" . Yii::$app->params['client'] . "</u>&nbsp;&nbsp;&nbsp;
            <b>Department</b>: <u>{$deptName}</u><br>
            <b>Cumpus Name</b>: <u>" . Yii::$app->params['college'] . "</u>&nbsp;&nbsp;
            <b>Program</b>: <u>{$progName}</u><br>
            <b>Admission Type</b>: <u>{$admission}</u>&nbsp;&nbsp;
            <b>Class Level</b>: <u>{$clslvl}</u>
        </caption>";
    echo "<thead>
            <tr>
                <th>#</th>
                <th>Reg. ID</th>
                <th>Student Name</th>
                <th>Sex</th>
                <th>Profession</th>
                <th>Nationality</th>
                <th>Date of Birth</th>
                <th>Registration Date</th>
                <th>Total Credit Hr.</th>
                <th>Total CGPA</th>
                <th>Remark</th>
            </tr>
        </thead><tbody>";
    $count = 0;
    foreach ($model as $semreg) {
        $std = $semreg->semregStdRel;
        $sr = $std->getCreditHourSumCalculated();
        $reg_date = date_format(date_create($std->std_created_at), "d-m-Y");
        $dd = date_create($std->stdUserRel->dob);
        $dob = $dd ? date_format($dd, "d-m-Y"):"-";
        $totalCreditHr = sprintf("%0.2f", $sr[0]);
        $cgpa = sprintf("%0.2f", $sr[1]);
        echo "<tr>
            <td>" . ++$count . "</td>
            <td>{$std->std_regId}</td>
            <td>{$std->getFullName()}</td>
            <td>{$std->stdUserRel->gender}</td>
            <td>{$std->std_type}</td>
            <td>{$std->stdUserRel->nationalityRel->name}</td>
            <td>{$dob}</td>
            <td>{$reg_date}</td>
            <td>{$totalCreditHr}</td>
            <td>{$cgpa}</td>
            <td></td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='bg-warning'>InvaliD request</p>";
}
