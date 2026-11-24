<?php

echo "<h4 style='text-align:center;'>Course Enrollments</h4>";

if(isset($model)){
    $semregs = $model->offeringDetailRel->getEnrolls();

    if($semregs){
        echo "<table class='table table-bordered table-striped' style='width:100%; font-size: 9pt;'>";
        echo "<tr style='background-color:#ACE;'>
                <th>#</th>
                <th>Reg. ID</th>
                <th>Student Name</th>
                <th>Section</th>
                <th>Phone</th>
                <th>Option</th>
                </tr><tbody>";
        $count = 0;
        foreach ($semregs as $semreg) {
            $std = $semreg->srd2SemregRel->semregStdRel;
            $acp = $model->offeringDetailRel->coff2detAcProgRel;
            $btnOption = "";
            $assigned = $model->assigned($std->id);
            if(is_null($assigned)){
                $btnOption = "<input type='checkbox' id='chkbx_001_{$std->id}' onchange='assignStudent({$std->id},{$model->id});return false;'/>";
            }else{
                $btnOption = "<small class='bg-warning'>{$assigned->getHeaderText2()}</small>";
            }
            // $clslll = $std->stdClslvlRel;
            echo "<tr>
                    <td>".++$count."</td>
                    <td>{$std->std_regId}</td>
                    <td>{$std->getFullName()}</td>
                    <td>{$std->stdGroupRel->getGsDisp()}</td>
                    <td>{$std->stdUserRel->phone_mobile}</td>
                    <td style='text-align: center'>{$btnOption}</td>
                </tr>";
        }
        echo "</tbody></table>";
    }else{
        echo "No enrollment found for this course.";
    }
}


?>