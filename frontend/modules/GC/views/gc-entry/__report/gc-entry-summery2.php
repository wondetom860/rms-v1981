<?php

use frontend\modules\GC\models\GcEntry;

$gc_entries = GcEntry::find()->all();

$gc_by_acy = GcEntry::find()->groupBy(['acad_year_relId','program_relId'])->all();

$gc_by_program = NULL;
$gc_by_dept = NULL;
$counttf = 0;
if ($gc_by_acy) {
    echo "<table class='table table-sm table-bordered table-condensed table-hover' style='background-color: #ACA;'>";
    echo "<thead style='background-color: #FDC;'>
            <tr>
                <th rowspan='2'>Acad.Year</th>
                <th rowspan='2'>Degree Level</th>
                <th rowspan='2'>Department</th>
                <th colspan='3' style='text-align: center;'>Counts#1</th>
                <th colspan='4' style='text-align: center;'>Counts#2</th>
                <th colspan='3' style='text-align: center;'>Program Total</th>
                <th colspan='3' style='text-align: center;'>ACY Total</th>
            </tr>
            <tr style='font-size: 9pt;'>
                <th style='text-align: center;'>Male</th>
                <th style='text-align: center;'>Female</th>
                <th style='text-align: center;'>Total</th>
                <th style='text-align: center;'>Military</th>
                <th style='text-align: center;'>Civil</th>
                <th style='text-align: center;'>Foreign</th>
                <th style='text-align: center;'>Total</th>
                <th style='text-align: center;'>Male</th>
                <th style='text-align: center;'>Female</th>
                <th style='text-align: center;'>Total</th>
                <th style='text-align: center;'>Male</th>
                <th style='text-align: center;'>Female</th>
                <th style='text-align: center;'>Total</th>
            </tr>
        </thead><tbody>";
    $acy_count_m = 0;
    $acy_count_f = 0;
    $acy_count_total = 0;
    $program_rows = '';
    foreach ($gc_by_acy as $acy) {
        $gc_by_program = array_filter($gc_entries, function ($gc_entry) use ($acy) {
            return $gc_entry->program_relId == $acy->program_relId;
        });
        $total_acy = countGender2($gc_entries);
        $total_acp = countGender2($gc_by_program);
        $first_time = false;
        $acys_count = count($gc_by_acy);
        // $acy_row_span = count($gc_by_program) * count($gc_by_dept);
        $rows_program = getProgramRows($gc_by_program, $total_acy, $total_acy, $acys_count, $first_time);
        $acy_count_m +=  $rows_program[2];
        $acy_count_f +=  $rows_program[3];
        $acy_count_total +=  $rows_program[4];
        $program_rows .= "<tr>
                <td rowspan='{$rows_program[0]}' style='vertical-align: middle'>{$acy->acadYearRel->school_year}</td>
                {$rows_program[1]}
                </tr>
            ";
        $counttf++;
    }

    echo "{$program_rows}";

    echo "</tbody></table>";
} else {
    echo "<p class='bg-warning'>No record found</p>";
    return;
}

function getProgramRows($gc_by_program, $total_acy, $total_acp, $acys_count, $first_time)
{
    $count = 1;
    $rows_prog = '';
    $p_count_m = 0;
    $p_count_f = 0;
    $p_count_total = 0;
    $rospan = $acys_count + count($gc_by_program);
    // var_dump($first_time);
    // exit;
    foreach ($gc_by_program as $index => $acp) {
        $dept_rows = getDeptRows($gc_by_program, $acp, $rospan, $total_acp, $total_acy, $first_time);
        $first_time = false;
        $rows_prog .= "{$dept_rows[1]}";
        $count += $dept_rows[0];

        $p_count_m +=  $dept_rows[2];
        $p_count_f +=  $dept_rows[3];
        $p_count_total +=  $dept_rows[4];
        // $count ++;
    }
    // $count += 1;
    // $count1 = countGender($gc_by_program);
    $rows_prog = "<tr>
            <td rowspan='$count' style='vertical-align: middle'>{$acp->programRel->program_name}</td>
            {$rows_prog}
            </tr>
    ";

    return [($count), $rows_prog, $p_count_m, $p_count_f, $p_count_total];
}

function getDeptRows($gc_by_program, $acp1, $rospan, $total_acp, $total_acy, $first_time = null)
{
    $count = 1;
    $rows = '';
    $d_count_m = 0;
    $d_count_f = 0;
    $d_count_total = 0;

    foreach ($gc_by_program as $acp) {
        $gc_by_dept = array_filter($gc_by_program, function ($by_acp) use ($acp1) {
            return ($by_acp->dept_relId == $acp1->dept_relId) && ($by_acp->id == $acp1->id) && ($by_acp->program_relId == $acp1->program_relId);
        });
    }
    foreach ($gc_by_dept as $index => $acd) {
        $rr = '';
        $counts1 = countGender($acd);
        $counts2 = countProfession($acd);
        $spec = $acd->spec_relId ? "({$acd->specRel->spec_name})" : '';
        $rows .= "<tr>
            <td style='vertical-align: middle'>{$acd->deptRel->dept_name}{$spec}</td>
            <td style='vertical-align: middle'>{$counts1[0]}</td>
            <td style='vertical-align: middle'>{$counts1[1]}</td>
            <td style='vertical-align: middle'>{$counts1[2]}</td>

            <td style='vertical-align: middle'>{$counts2[0]}</td>
            <td style='vertical-align: middle'>{$counts2[2]}</td>
            <td style='vertical-align: middle'>{$counts2[1]}</td>
            <td style='vertical-align: middle'>{$counts2[3]}</td>
            ";
        if (($count == 0) && $first_time) {
            $rows .= "
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acp[0]}</td>
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acp[1]}</td>
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acp[2]}</td>
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acy[0]}</td>
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acy[1]}</td>
                <td rowspan='{$rospan}' style='vertical-align: middle'>{$total_acy[2]}</td>
            ";
        }
        $rows .= "</tr>";
        $count++;
        $d_count_m += $counts1[0];
        $d_count_f += $counts1[1];
        $d_count_total += $counts1[2];
    }

    return [($count), $rows, $d_count_m, $d_count_f, $d_count_total];
}

function countProfession($acd)
{
    $stds = $acd->gcStudents;
    $count_mil = 0;
    $count_civ = 0;
    $count_for = 0;
    $count_total = count($stds);

    foreach ($stds as $std) {
        $std = $std->stdRel;
        if ($std->stdUserRel->user_type == 'MILITARY') {
            $count_mil++;
        } elseif ($std->stdUserRel->user_type == 'CIVIL') {
            $count_civ++;
        } elseif ($std->stdUserRel->user_type == 'FOREGN') {
            $count_for++;
        }
    }
    $sum = $count_mil + $count_for + $count_civ;
    $count_total = $count_total != ($sum) ? ($sum) : $count_total;
    return [$count_mil, $count_for, $count_civ,  $count_total];
}

function countGender2($gcentries)
{
    $m_total = 0;
    $f_total = 0;
    $total_total = 0;
    foreach ($gcentries as $gc) {
        $tt = countGender($gc);

        $m_total += $tt[0];
        $f_total += $tt[1];
        $total_total += $tt[2];
    }

    return [$m_total, $f_total, $total_total];
}

function countGender($gc_entry)
{
    $stds = $gc_entry->gcStudents;
    $count_m = 0;
    $count_f = 0;
    $count_total = count($stds);

    foreach ($stds as $std) {
        $std = $std->stdRel;
        if ($std->stdUserRel->gender == 'MALE') {
            $count_m++;
        }
        if ($std->stdUserRel->gender == 'FEMALE') {
            $count_f++;
        }
    }
    $sum = $count_m + $count_f;
    $count_total = $count_total != ($sum) ? ($sum) : $count_total;
    return [$count_m, $count_f, $count_total];
}
