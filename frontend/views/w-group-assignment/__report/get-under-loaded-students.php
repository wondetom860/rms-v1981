<?php

use backend\models\Student;
use backend\models\WGroupSetup;

if (isset($listModel) && isset($listModel[0])) {
    // var_dump($listModel);//unenrolled gass
    $gset = WGroupSetup::findOne($listModel[0]['gas_gset_relId']);
    echo "<table class='table table-bordered table-hover table-sm table-condensed' style='font-size: 9pt;'>";
    echo "<caption style='text-align:center; color:#000;'>
        <h3>Max. Min. Semester Load, Semester Registration and Credit deficiency report<br></h3>
        ACY: <b><u>{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}</u></b>
    </caption>";
    echo "<thead style='background-color: #8D0;'>
            <th>#</td>
            <th>Reg. ID</td>
            <th>Full Name</td>
            <th>Type</td>
            <th>Sponsorship</td>
            <th>Department</td>
            <th>Class Lvl.</td>
            <th>Load bound</td>
            <th>Credits</td>
            <th>Remark</td>
        </thead><tbody>";
    $count = 0;
    foreach ($listModel as $unEnrolledGas) {
        $std = Student::findOne($unEnrolledGas['gas_std_relId']);
        $gset = WGroupSetup::findOne($unEnrolledGas['gas_gset_relId']);
        // courses offered:
        $coff2s = $gset->getCredits($std);
        $up = $coff2s[2] ? 'P' : 'U';
        echo "<tr>
            <td>" . ++$count . "</td>
            <td>{$std->std_regId}</td>
            <td>{$std->getFullName()}</td>
            <td>{$std->stdUserRel->user_type}</td>
            <td>{$std->stdScholarshpTypeRel->schType_name}</td>
            <td>{$gset->gsDeptRel->dept_code}</td>
            <td>{$gset->gsGroupRel->group_name} : {$gset->getGsDisp(1)}</td>
            <td>{$coff2s[0]}</td>
            <td>{$coff2s[1]}</td>
            <td style='text-align:center;'>{$up}</td>
        </tr>";
    }
    echo "<tr><td colspan=8 style='text-align: center; font-style:italic'>Key: U=Under-Load(Below min. semester load), P=Possible(Min. semester load satisfied but not yet register.)</td></tr>";
    echo "</tbody></table>";
}
