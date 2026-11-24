<?php

// @var $this yii\web\View
// @var $model frontend\modules\AcActivePrograms\models\AcActivePrograms

use backend\models\SemesterRegistration2;
use backend\models\WGroupAssignment;

if (isset($model) && !empty($model)) {
    $this->title = 'Graduate Class Point History';

    $gsetIds = $model->getGroupSetups()->select('id')->column();
    $stdIds = WGroupAssignment::find()
        ->select('gas_std_relId')
        ->where(['in', 'gas_gset_relId', $gsetIds])
        ->column();
    $stdIds = array_unique($stdIds);
    if ($stdIds) {
        $semesterRegistrations = SemesterRegistration2::find()
            ->where(['semreg_std_relId' => $stdIds])
            ->andWhere(' semreg_last_ssn_relId IS NOT NULL ')
            ->orderBy([
                'semreg_acadYear_relId' => SORT_ASC,
                'semreg_semester_relId' => SORT_ASC
            ])
            ->all();
        if ($semesterRegistrations) {
            $acyColumn = array_column($semesterRegistrations, 'semregAcadYearRel');
            $semColumn = array_column($semesterRegistrations, 'semregSemesterRel');
            // $acyColumn = array_column($semesterRegistrations, 'semreg_semester_relId');
            $header = getHeader($acyColumn, $semColumn);
            $spec = $model->spec_relId ? $model->specRel->spec_name : '-';
            echo "<table class='table table-bordered table-striped table-hover table-sm'>
                <caption style='text-align: center; font-weight: bold; color:#000'>
                    <h4>Graduate Class Point History</h4>
                    Academic Year: <u>{$model->getAcadYearHeader()}</u>
                    <br>Department: <u>{$model->deptRel->dept_name}</u> &nbsp; &nbsp; &nbsp; &nbsp;
                    Program: <u>{$model->getProgramHeader()}</u> &nbsp; &nbsp; &nbsp; &nbsp;
                    <br>Specialization: <u>{$spec}</u> &nbsp; &nbsp; &nbsp; &nbsp;
                </caption>
                {$header}
                <tbody>";
            $count = 0;
            foreach ($stdIds as $stdId) {
                $stdSemreg = array_filter($semesterRegistrations, function ($item) use ($stdId) {
                    return $item->semreg_std_relId == $stdId;
                });
                $pointHistory = form_point_history($stdSemreg, $acyColumn, $semColumn, $stdId);
                $semreg = array_shift($stdSemreg);//similar to $stdSemreg[0]
                $student = $semreg->semregStdRel;
                echo "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$student->std_regId}</td>
                        <td>{$student->getFullName()}</td>
                        {$pointHistory}
                        <td> </td>
                </tr>";
            }
            echo "</tbody>
            </table>";
        } else {
            echo "<p class='bg-warning'>No students found in this academic program</p>";
            return;
        }
    } else {
        echo "<p class='bg-warning'>Section NOT found in this academic program</p>";
        return;
    }
} else {
    echo "<p class='bg-warning'>Invalid Request</p>";
    return;
}


function form_point_history($stdSemreg, $acyObj, $semObj, $stdId)
{
    $pointHistory = "";
    // $semreg = array_shift($stdSemreg);
    $acyObj = array_unique($acyObj, SORT_REGULAR);
    $semObj = array_unique($semObj, SORT_REGULAR);
    $credhrSum = 0;
    $gpsum = 0;
    foreach ($acyObj as $acy) {
        foreach ($semObj as $sem) {
            $semregg = array_filter($stdSemreg, function ($item) use ($sem, $acy, $stdId) {
                return $item->semreg_semester_relId == $sem->id &&
                    $item->semreg_acadYear_relId == $acy->id
                    && $item->semreg_std_relId == $stdId;
            });
            $semregg = array_shift($semregg);
            if ($semregg) {
                // $gpa = $semregg->getGPA();
                // $credhrSum += $semregg->semreg_totalCrdtHr;
                // $gpsum += $semregg->semreg_gradePoint_sum;
                $sgpa = sprintf("%.2f", $semregg->semreg_SGPA);
                $cgpa = sprintf("%.2f", $semregg->semreg_CGPA);
                $pointHistory .= "<td style='text-align: center;'>{$sgpa}</td>";
                $pointHistory .= "<td style='text-align: center;'>{$cgpa}</td>";
            } else {
                $pointHistory .= "<td style='text-align: center;'> </td>";
                $pointHistory .= "<td style='text-align: center;'> </td>";
            }
        }
    }
    // $semreg = array_shift($stdSemreg);
    $last_semreg = array_pop($stdSemreg);
    $credhrSum = $last_semreg->semreg_totalCrdtHr;
    $gpsum = $last_semreg->semreg_gradePoint_sum;
    // $credhrSum = array_sum(array_column($stdSemreg, 'semreg_totalCrdtHr'));
    // $gpsum = array_sum(array_column($stdSemreg, 'semreg_gradePoint_sum'));
    $pointHistory .= "<td style='text-align: center; font-weight: bold;'>{$credhrSum}</td>";
    $pointHistory .= "<td style='text-align: center; font-weight: bold;'>" . sprintf("%.2f", $gpsum) . "</td>";
    return $pointHistory;
}

function getHeader($acyColumn, $semColumn)
{
    $acyColumn = array_map(function ($item) {
        return $item->sch_year_desc;
    }, $acyColumn);
    $semColumn = array_map(function ($item) {
        return $item->semester_name;
    }, $semColumn);
    $semColumn = array_unique($semColumn);
    $acyColumn = array_unique($acyColumn);
    $hed = getAcyHeader($acyColumn, $semColumn);
    $header = "<tr>
            <th rowspan='4' style='vertical-align: bottom'>#</th>
            <th rowspan='4' style='vertical-align: bottom'>Student ID</th>
            <th rowspan='4' style='vertical-align: bottom'>Student Name</th>
        </tr>
        <tr>{$hed[0]}</tr>
        <tr>{$hed[1]}</tr>
        <tr>{$hed[2]}
            <th style='text-align: center;'>Crdt. Hr</th>
            <th style='text-align: center;'>GPS</th>
        </tr>
        ";
    $header .= "</tr>";
    return $header;
}

function getAcyHeader($acyColumn, $semColumn)
{
    $header = "";
    $semHeader = "";
    $gparow = "";
    foreach ($acyColumn as $acy) {
        $l2 = getSemHeader($semColumn);
        $semHeader .= $l2[0];
        $gparow .= $l2[1];
        $header .= "<th colspan=4 style='text-align: center;'>$acy</th>";
    }
    $header .= "<th rowspan=2 colspan=2 style='text-align: center; vertical-align: bottom'>Total</th>";
    $header .= "<th rowspan=4 style='text-align: center; vertical-align: bottom'>Remark</th>";
    // $gparow .= "";
    return [$header, $semHeader, $gparow];
}

function getSemHeader($semColumn)
{
    $header = "";
    $gpaHeader = "";
    foreach ($semColumn as $sem) {
        $gpaHeader .= getGPAHeader();
        $header .= "<th colspan=2 style='text-align: center;'>$sem</th>";
    }
    // $gpaHeader .= "";
    return [$header, $gpaHeader];
}

function getGPAHeader()
{
    return "
        <th style='text-align: center;'>SPA</th>
        <th style='text-align: center;'>CGPA</th>
        ";
}
