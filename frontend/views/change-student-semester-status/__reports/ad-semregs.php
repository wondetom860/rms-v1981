<?php

if (isset($semregs) && count($semregs) > 0) {
    $ayObj = $semregs[0]->semregAcadYearRel;
    $semObj = $semregs[0]->semregSemesterRel;
    $clslvlObj = $semregs[0]->semregClslvlRel;
    $progObj = $clslvlObj->clslvlProgramRel;
    echo "<table class='table table-sm table-bordered table-condensed tabble-hover'>";
    echo "<caption style='font-weight:bold; color: #000'>
            <center>
                <b><u>List of Students with Academic Defficiency</u></b><br>

                <i><u>{$semObj->semester_name}</u></i> Semester of Academic Year <i><u>{$ayObj->sch_year_desc}</u></i><br>

                Program : <u>{$progObj->program_name} - {$clslvlObj->getClslvlDisp()}</u>
            </center>
    </caption>";
    echo "<thead style='background-color: #8D0;'>
        <th>#</th>
        <th>Reg. ID</th>
        <th>Full Name</th>
        <th>SGPA</th>
        <th>CGPA</th>
        <th>Prev. Status</th>
        <th colspan=2>Change to</th>
    </thead>";
    $count = 0;
    foreach ($semregs as $semreg) {
        $std = $semreg->semregStdRel;
        $requested = checkIfRequested($std, $ayObj->id, $semObj->id);
        if ($requested) {
            continue;
        }
        $sgpa = sprintf("%0.3f", $semreg->semreg_SGPA);
        $cgpa = sprintf("%0.3f", $semreg->semreg_CGPA);
        $status = $semreg->semregLastSsnRel ? $semreg->semregLastSsnRel->sssn_name : 'Pending';
        $statusId = $semreg->semregLastSsnRel ? $semreg->semreg_last_ssn_relId : 0;
        $ops = getOptions2($std->id);
        $option = "<select data-s='WONDE-TOM' class='form-control' title='select action' 
                        onChange='changeStatus({$std->id},$(this).val(),{$statusId})'>{$ops}
                    </select>";
        $btn = "<p class='btn pull-right btn-xs btn-warning' id='change_stat_{$std->id}'>change</p>";
        echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$std->std_regId}</td>
                <td>{$std->getFullName()}</td>
                <td>{$sgpa}</td>
                <td>{$cgpa}</td>
                <td>{$status}</td>
                <td>{$option}</td>
                <td>{$btn}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='bg-warning'>No record found.</p>";
}

function checkIfRequested($key, $acadYearId, $semId)
{
    if (!is_null($acadYearId) && !is_null($semId)) {
        return \backend\models\StudentSemesterStatusHistory::find()
            ->where([
                'sssh_std_relId' => $key['id'],
                'sssh_acadYear_relId' => $acadYearId,
                'sssh_sem_relId' => $semId,
            ])
            ->andWhere(['OR', ['sssh_approved' => NULL], ['<>', 'sssh_approved', 0]])
            ->one();
    }
    // $this->__constructor();
    return \backend\models\StudentSemesterStatusHistory::find()
        ->where([
            'sssh_std_relId' => $key['id'],
        ])
        ->andWhere(['OR', ['sssh_approved' => NULL], ['<>', 'sssh_approved', 0]])
        ->one();
}


function getOptions2($stdId)
{
    // ->where(['sssn_name' => ['Probation 1', 'Probation 2']])
    $kafiya = \backend\models\StudentSemesterStatusHistory::find()
        ->where(['sssh_std_relId' => $stdId, 'sssh_approved' => 1])
        ->all();
    $kafiyaList = [];
    if ($kafiya && count($kafiya) > 0) {
        foreach ($kafiya as $keyKafiya) {
            $kafiyaList[] = $keyKafiya->sssh_sssn_relId;
        }

        $kafiyaList = array_unique($kafiyaList);

        // var_dump($kafiyaList);die();
    }
    $sssns = \backend\models\StudentSemesterStatusName::find()
        //->where(['OR',['sssn_pass_status' => 3],['sssn_pass_status' => 2]])
        ->andWhere(['NOT IN', 'id', $kafiyaList])
        ->orderBy('sssn_name ASC')
        ->all();
    //echo "WONDETOM";
    $sssnOptions = "<option value=''> - </option>";
    if ($sssns && count($sssns) > 0) {
        foreach ($sssns as $key) {
            $sssnOptions .= "<option title='{$key->id}' value={$key->id}>{$key->sssn_name}</option>";
        }
    }
    return $sssnOptions;
}
