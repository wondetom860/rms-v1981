<?php

if(isset($model)){
    echo "<table class='table table-bordered table-sm table-hover'>";
    echo "<caption style='color: #000; font-weight:nor,mal;'>
            <h3 style='text-align:center;'>Import from GC Academic Programs</h3>
        </caption>";
    echo "<thead>";
    echo "<tr style='background-color: #DAF;'>
            <td>#</td>
            <td>Academic Year</td>
            <td>Program</td>
            <td>Department</td>
            <td>Specialization</td>
            <td>Department</td>
            <td>Option</td>
        </tr>";
    echo "</thead><tbody>";
    $count = 0;
    foreach ($model as $acp) {
        $option = "<button class='btn btn-xs btn-success' onclick='importfromacp({$acp->id});return false;'>Import</button>";
        $spec = $acp->specRel ? $acp->specRel->spec_name : '-';
        echo "<tr>
                <td>".++$count."</td>
                <td>{$acp->academicYearRel->getAcHeader()}</td>
                <td>{$acp->getProgramHeader()}</td>
                <td style='padding-left: 16px;'>{$acp->deptRel->dept_code}</td>
                <td>{$spec}</td>
                <td></td>
                <td>{$option}</td>
            </tr>";
    }
    echo "</tbody></table>";
}else{
    echo "<p class='bg-warning'>No program found</p>";
}


?>