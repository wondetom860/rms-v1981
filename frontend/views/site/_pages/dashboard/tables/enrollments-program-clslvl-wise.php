<?php

use backend\models\ClassLevel;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\SemesterRegistration2;
use common\components\CommonControllerFunctions as CCF;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;

$acy = '';
$sem = '';

$current = CCF::currentTime();
if ($current) {
    $acy = SchoolYear::findOne($current['Y'])->sch_year_desc;
    $sem = Semester::findOne($current['S'])->semester_name;
} else {
    $recentAcadYear = AcAcademicYear::getRecentAcademciYear();
    if ($recentAcadYear) {
        $acy = $recentAcadYear->school_year;
        $sem = $recentAcadYear->semester;
    }
}
// get current active acYear & Semester

// var_dump($current);exit;

// $enrolls = SemesterRegistration2::find()
//     ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
//     ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
//     ->select('concat([sch_year_desc],"-",[semester_name]) as ACY')
//     ->addSelect('count(*) as data')
//     ->groupBy('semreg_acadYear_relId , semreg_semester_relId')
//     ->orderBy(['ACY' => SORT_ASC])
//     ->createCommand()->queryAll();

$query = Yii::$app->db->createCommand("
    select  
        semester_registration_2.id as ID,
        program_name as PROG,
        count(*) as COUNT 
    from semester_registration_2 
        inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join class_level on class_level.id=semester_registration_2.semreg_clslvl_relId
        inner join program on program.id=class_level.clslvl_program_relId
    where sch_year_desc = :acy AND semester_name = :sem
    group by program.id
    order by program_name ASC
")->bindParam(':acy', $acy)->bindParam(':sem', $sem);

// gset->semregs returns all the students enrolled courses under this gset.

$result = $query->queryAll();

// foreach ($result as $key => $value) {
//     echo "<hr>";
//     var_dump($key,$value);
// }
// // var_dump($result);
// exit;



if ($result) {
    echo "<div style='max-height: 500px; overflow:auto; min-height: 500px;'>
        <table class='table table-hover table-bordered table-sm table-condensed' style='font-size: 9pt;'>
        <caption style='text-align:center; color:#000; font-weight:bolder;'>Active Students - [Porfession-Program-Class Level] wise {$acy}-{$sem}</caption>
        <thead style='background-color: #8D0;'>
            <tr>
                <th rowspan=2>Program</th>
                <th rowspan=2>Class Year</th>
                <th colspan=4  style='text-align:center;'>Count</th>
                <th rowspan=2>Remark</th>
            </tr>
            <tr>
                <th style='text-align:center;'>CIVIL</th>
                <th style='text-align:center;'>FOREIGN</th>
                <th style='text-align:center;'>MILITARY</th>
                <th style='text-align:center;'>Total</th>
            </tr>
        </thead>  
        <tbody>  
    ";
    $count = 0;
    $totalAll = 0;
    $totalCivil =  0;
    $totalForeign =  0;
    $totalMilitary =  0;
    $totalRow =  0;
    foreach ($result as $index => $value) { //iterates for each program
        $total = getCountByClslvl($value['ID'], $value['PROG']);
        if ($total) {
            $pCivilTotal = 0;
            $pMilitaryTotal = 0;
            $pForeignTotal = 0;
            $pTotalRow = 0;
            foreach ($total as $key2 => $value2) { //iterates for each class year
                $total2 = getCountByProf4($value2['ID'], $value2['CLSYR'], $value['PROG']);
                if ($total2) {
                    $civilCount = 0;
                    $militaryCount = 0;
                    $foreignCount = 0;

                    foreach ($total2 as $key3 => $value3) {
                        if ($value3['PROF'] == 'MILITARY') {
                            $militaryCount =  $value3['data'];
                        } elseif ($value3['PROF'] == 'FOREGN') {
                            $foreignCount = $value3['data'];
                        } elseif ($value3['PROF'] == 'CIVIL') {
                            $civilCount =  $value3['data'];
                        }
                    }
                    $clslll = ClassLevel::findOne($value2['CLSYR']);
                    $pCivilTotal += $civilCount;
                    $pMilitaryTotal += $militaryCount;
                    $pForeignTotal += $foreignCount;
                    $totalRow = ($civilCount + $militaryCount + $foreignCount);
                    // $totalRow = $totalRow == 0 ? $value['COUNT'] : $totalRow;
                    $totalAll += $totalRow;
                    echo "<tr>
                        <td style='vertical-align:middle'>" . $value['PROG'] . "</td>
                        <td style='vertical-align:middle'>{$clslll->getClslvlDisp()}</td>
                        <td style='text-align:center;'>{$civilCount}</td>
                        <td style='text-align:center;'>{$foreignCount}</td>
                        <td style='text-align:center;'>{$militaryCount}</td>
                        <td style='text-align:center; font-weight:bold;'>{$totalRow}</td>
                        <td></td>
                    </tr>";
                } else {
                    echo "<tr>
                        <td style='vertical-align:middle'>" . $value['PROG'] . "</td>
                        <td colspan=6></td>
                    </tr>";
                }
            }
            $pTotalRow = ($pCivilTotal + $pMilitaryTotal + $pForeignTotal);
            $totalMilitary += $pMilitaryTotal;
            $totalForeign += $pForeignTotal;
            $totalCivil += $pCivilTotal;
            echo "
                <tr style='font-weight: bold; text-align:center; background-color: #AEF;'>
                    <td colspan=2>Total</td>
                    <td>{$pCivilTotal}</td>
                    <td>{$pForeignTotal}</td>
                    <td>{$pMilitaryTotal}</td>
                    <td>{$pTotalRow}</td>
                </tr>
            ";
        } else {
            echo "<tr><td colspan=7></td></tr>";
        }
    }
    echo "<tr></tr>";
    echo "</tbody>
            <tr style='text-align:center; background-color: #ACE; font-weight:bold'>
                <td colspan=2>GrandTotal</td>
                <td>{$totalCivil}</td>
                <td>{$totalForeign}</td>
                <td>{$totalMilitary}</td>
                <td>{$totalAll}</td>
            </tr>
        </table></div>";
} else {
    echo "No data found.";
}

function getCountByClslvl($smeregId, $PRGRAM)
{
    $smereg = SemesterRegistration2::findOne($smeregId);

    return SemesterRegistration2::find()
        ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
        ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
        ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
        ->innerJoin('program', 'program.id=student.std_prog_relId')
        ->select('semreg_clslvl_relId as CLSYR')
        ->addSelect('semester_registration_2.id as ID')
        ->addSelect('count(*) as data')
        ->where([
            'semreg_acadYear_relId' => $smereg->semreg_acadYear_relId,
            'semreg_semester_relId' => $smereg->semreg_semester_relId,
            'program_name' => $PRGRAM
        ])
        ->groupBy('CLSYR')
        ->orderBy(['CLSYR' => SORT_ASC])
        ->createCommand()->queryAll();
}

/**
 * Summary of getCountByGender3
 * @param mixed $value
 * @param mixed $gsetIds
 * @return array
 */
function getCountByProf4($smeregId, $clslvl_id, $program_name)
{
    // var_dump($smeregId, $clslvl_id, $program_name);
    // echo "<hr>";
    // return;

    $smereg = SemesterRegistration2::findOne($smeregId);

    return SemesterRegistration2::find()
        ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
        ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
        ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
        ->innerJoin('class_level', 'class_level.id=semester_registration_2.semreg_clslvl_relId')
        ->innerJoin('program', 'program.id=student.std_prog_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.user_type as PROF')
        ->addSelect('count(*) as data')
        ->where([
            'semreg_acadYear_relId' => $smereg->semreg_acadYear_relId,
            'semreg_semester_relId' => $smereg->semreg_semester_relId,
            'semreg_clslvl_relId' => $clslvl_id,
            'program_name' => $program_name,
        ])
        ->groupBy('PROF')
        ->orderBy(['PROF' => SORT_ASC])
        ->createCommand()->queryAll();
}
