<?php

use backend\models\ClassLevel;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\SemesterRegistration2;
use common\components\CommonControllerFunctions as CCF;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;

$acy = '';
$sem = '';

$current = CCF::currentTime();
if ($current) {
    $acy = SchoolYear::findOne($current['Y'])->sch_year_desc;
    $sem = Semester::findOne($current['S'])->semester_name;
} else {
    // if there is no active academic year entry, look for recently closed one.
    $recentAcadYear = AcAcademicYear::getRecentAcademciYear();
    if ($recentAcadYear) {
        $acy = $recentAcadYear->school_year;
        $sem = $recentAcadYear->semester;
    }
}


$query = Yii::$app->db->createCommand("
    SELECT 
        semreg_clslvl_relId as CLSLVL,
        student_semester_status_name.sssn_name as STATUS,
        count(*) as COUNT
    FROM 
        semester_registration_2
        inner join student_semester_status_name on semester_registration_2.semreg_last_ssn_relId = student_semester_status_name.id 
        inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join class_level on class_level.id=semester_registration_2.semreg_clslvl_relId
        inner join program on program.id=class_level.clslvl_program_relId
    where 
        sch_year_desc = :acy AND 
        semester_name = :sem AND
        semreg_last_ssn_relId IS NOT NULL
    group by semreg_clslvl_relId,semreg_last_ssn_relId
    order by semreg_clslvl_relId ASC, student_semester_status_name.sssn_pass_status ASC
")->bindParam(':acy', $acy)->bindParam(':sem', $sem);

$result = $query->queryAll();

if ($result) {
    $statusHeaders = getStatusHeaders($result);
    $headersCount = $statusHeaders[0];
    $headersArray = $statusHeaders[2];
    $statusHeaders = $statusHeaders[1];
    echo "<div style='max-height: 500px; overflow:auto; min-height: 500px;'>
        <table class='table table-hover table-bordered table-sm table-condensed' style='font-size: 9pt;'>
        <caption style='text-align:center; color:#000; font-weight:bolder;'>Student Semester Status Summary {$acy}-{$sem}</caption>
        <thead style='background-color: #8D0;'>
            <tr>
                <th rowspan=2>Program</th>
                <th rowspan=2>Class Year</th>
                <th colspan={$headersCount} style='text-align:center;'>Count</th>
                <th rowspan=2>Total</th>
            </tr>
            <tr>
                {$statusHeaders}
            </tr>
        </thead>  
        <tbody>";
    $tt = 0;
    $kheadersCount = $headersCount + 2;
    $colValues = [];
    foreach (array_unique(array_column($result, "CLSLVL")) as $row) {

        $valuess = getStatusAndCount($row, $result, $headersCount, $headersArray);
        $tt += $valuess[0];
        $colValues[] = $valuess[2];
        $clslvl = ClassLevel::findOne($row);
        // var_dump($valuess);exit;

        echo "
                <tr>
                    <td>{$clslvl->clslvlProgramRel->program_name}</td>
                    <td>{$clslvl->getClslvlDisp()}</td>
                    {$valuess[1]}
                </tr>
            ";
    }
    // footer total
    $colsTotal = getColsSum($colValues, $headersCount);
    echo "<tr style='text-align:center; font-weight: bold; background-color: #ACE; text-decoration: underline; font-style: italic;'>
        <td colspan=2>Total</td>
            {$colsTotal}
        <td>{$tt}</td>
    <tr>";
    echo "</tbody>
        </table>
    ";
} else {
    echo "Result not published yet";
}

function getColsSum($colValues, $headersCount)
{
    $colTds = "";
    if (count($colValues) == 0) {
        $colTds = "<td colspan={$headersCount} style='text-align: center;'>-</td>";
    } else {
        $i = 0;
        while ($i < $headersCount) {
            $colTds .= "<td>" . array_sum(array_column($colValues, $i)) . "</td>";
            $i++;
        }
    }

    return $colTds;
}
function getStatusAndCount($row, $result, $columnsCount, $headersArray)
{
    $tds = "";
    $total = 0;
    $cols = [];
    $resultt = array_filter($result, function ($r) use ($row) {
        return $r['CLSLVL'] == $row;
    });
    $countColumns = 0;


    // var_dump($resultt);
    // var_dump($headersArray);
    // exit;


    foreach ($headersArray as $ha) {
        $valueee = array_filter($resultt, function ($r) use ($ha) {
            return $r['STATUS'] == $ha;
        });
        if (!empty($valueee)) {
            $value = array_values($valueee)[0];
            $tds .= "<td style='text-align: center;'>{$value['COUNT']}</td>";
            $total += $value["COUNT"];
            $cols[] = $value["COUNT"];
            $countColumns++;
        } else {
            $tds .= "<td style='text-align: center;'>0</td>";
            $total += 0;
            $countColumns++;
            $cols[] = 0;
        }
    }
    // add total column
    $tds .= "<td style='text-align: center; font-weight:bold'>{$total}</td>";

    return [$total, $tds, $cols];
}

function getStatusHeaders($result)
{
    $statusHeader = "";
    $count = 0;
    $headersArray = [];

    foreach (array_unique(array_column($result, "STATUS")) as $status) {
        $statusHeader .= "<th>{$status}</th>";
        $count++;
        $headersArray[] = $status;
    }
    return [$count, $statusHeader, $headersArray];
}
