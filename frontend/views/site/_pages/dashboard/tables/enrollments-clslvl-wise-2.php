<?php

use backend\models\ClassLevel;
use backend\models\SemesterRegistration2;

// $enrolls = SemesterRegistration2::find()
//     ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
//     ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
//     ->select('concat([sch_year_desc],"-",[semester_name]) as ACY')
//     ->addSelect('count(*) as data')
//     ->groupBy('semreg_acadYear_relId , semreg_semester_relId')
//     ->orderBy(['ACY' => SORT_ASC])
//     ->createCommand()->queryAll();

$query = Yii::$app->db->createCommand("
    select concat(sch_year_desc,'-',semester_name) as ACY, concat(clslvl_name,'(',program_prefix,')') as CLSLVL, count(*) as COUNT,semreg_acadYear_relId,semreg_semester_relId,semreg_clslvl_relId 
    from semester_registration_2 inner join school_year 
        on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join class_level on class_level.id=semester_registration_2.semreg_clslvl_relId
        inner join program on program.id=class_level.clslvl_program_relId
    group by semreg_acadYear_relId,semreg_semester_relId,semreg_clslvl_relId
    order by ACY DESC, program_name DESC, clslvl_num ASC
");

$result = $query->queryAll();

if ($result) {
    echo "
        <table class='table table-hover table-bordered table-sm table-condensed' style='font-size: 9pt;'>
        <thead style='background-color: #8D0;'>
            <tr>
                <th rowspan=2>ACY</th>
                <th rowspan=2>Class Year(Program)</th>
                <th colspan=3  style='text-align:center;'>Enrollments Count</th>
                <th rowspan=2>Remark</th>
            </tr>
            <tr>
                <th style='text-align:center;'>Male</th>
                <th style='text-align:center;'>Female</th>
                <th style='text-align:center;'>Total</th>
            </tr>
        </thead>  
        <tbody>  
    ";
    $count = 0;
    $totalAll = 0;
    $totalMale =  0;
    $totalFemale =  0;
    $acyRowCount = 0;
    $clslvlRowCount = 0;
    $acy = NULL;
    $clslvl = NULL;
    $resp = "";
    $acyIndexed = __indexByACY($result);
    $table = '';
    foreach ($acyIndexed as $acy => $valueAcy) {
        $table .= "<tr>";
        $table .= "<td rowspan=" . count($valueAcy) . ">{$acy}</td>";
        // display all valueAcys at index 0;

        $clslvlIndexed = __indexByCLSLVL($valueAcy);
        foreach ($clslvlIndexed as $clvlvl => $valueClslvl) {
            $clslll = ClassLevel::findOne($valueClslvl[0]['semreg_clslvl_relId']);
            $table .= "<tr><td rowspan=" . count($valueClslvl) . ">{$clslll->getClslvlDisp()}</td>";
            // display all valueClslvl at index 0
            foreach ($valueClslvl as $indexx => $valuee) {
                $total = getCountByGender2($valuee);
                $maleCount = isset($total[0]['GENDER']) && ($total[0]['GENDER'] == 'MALE') ? $total[0]['data'] : 0;
                $femaleCount = isset($total[1]['GENDER']) && ($total[1]['GENDER'] == 'FEMALE') ? $total[1]['data'] : 0;

                $totalCount = $maleCount + $femaleCount;
                $table .= "<tr>
                        <td>{$maleCount}</td>
                        <td>{$femaleCount}</td>
                        <td>{$totalCount}</td>
                    </tr>";
            }
            $table .= "</tr>";
        }
        // echo "<tr>       
        //     <td style='vertical-align:middle'>{$value['ACY']}</td>        
        //     <td style='vertical-align:middle'>{$clslll->clslvlProgramRel->program_name}</td>        
        //     <td style='vertical-align:middle'>{$clslll->getClslvlDisp()}</td>        
        //     <td style='text-align:center;'>{$maleCount}</td>        
        //     <td style='text-align:center;'>{$femaleCount}</td>        
        //     <td style='text-align:center;'>{$value['COUNT']}</td>        
        //     <td></td>        
        // </tr>";

        $table .= "</tr>";
        $totalMale += $maleCount;
        $totalFemale += $femaleCount;
        $totalAll += $totalMale + $totalFemale;
    }
    $table .= "<tr></tr>";
    $table .= "</tbody>
            <tr style='text-align:center; background-color: #ACE; font-weight:bold'>
                <td colspan=2>Total</td>
                <td>{$totalMale}</td>
                <td>{$totalFemale}</td>
                <td>{$totalAll}</td>
            </tr>
        </table>";
    echo $table;
} else {
    echo "No data found.";
}

function __indexByCLSLVL($array)
{
    $clslvlRows = [];
    // $count = 0;
    $clslvls = array_column($array, 'CLSLVL');
    foreach ($clslvls as $index => $clslvl) {
        foreach ($array as $key => $value) {
            if ($clslvl == $value['CLSLVL']) {
                $clslvlRows[$clslvl][] = $value;
            }
        }
    }
    return $clslvlRows;
}

function __indexByACY($array)
{
    $acyRows = [];
    // $count = 0;
    $acys = array_column($array, 'ACY');
    foreach ($acys as $index => $acy) {
        foreach ($array as $key => $value) {
            if ($acy == $value['ACY']) {
                $acyRows[$acy][] = $value;
            }
        }
    }
    return $acyRows;
}
function getCountByGender2($value)
{
    $acy_id = $value['semreg_acadYear_relId'];
    $sem_id = $value['semreg_semester_relId'];
    $clslvl_id = $value['semreg_clslvl_relId'];

    return SemesterRegistration2::find()
        ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
        ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
        ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.gender as GENDER')
        ->addSelect('count(*) as data')
        ->where([
            'semreg_acadYear_relId' => $acy_id,
            'semreg_semester_relId' => $sem_id,
            'semreg_clslvl_relId' => $clslvl_id
        ])
        ->groupBy('GENDER')
        ->orderBy(['GENDER' => SORT_ASC])
        ->createCommand()->queryAll();
}


// if (is_null($acy)) {
//     // open ACY row here
//     $acy = $value['ACY'];
//     $clslvl = $value['semreg_clslvl_relId'];
//     $acyRowCount = 1;
//     $clslvlRowCount = 1;
// } else if ($acy == $value['ACY']) {
//     $acyRowCount++;
//     if ($clslvl == $value['semreg_clslvl_relId']) {
//         $clslvlRowCount++;
//     } else {
//         $clslvl = $value['semreg_clslvl_relId'];
//         $clslvlRowCount = 1;
//         // close clslvl row here
//     }
// } else {
//     $acy = $value['ACY'];
//     $clslvl = $value['semreg_clslvl_relId'];
//     // close ACY row here
//     $acyRowCount = 1;
//     $clslvlRowCount = 1;
// }