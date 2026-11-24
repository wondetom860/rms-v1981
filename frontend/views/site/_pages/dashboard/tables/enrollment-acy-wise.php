<?php

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
    select concat(sch_year_desc,'-',semester_name) as ACY, count(*) as COUNT, semreg_acadYear_relId,semreg_semester_relId
    from semester_registration_2 inner join school_year 
        on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
    group by semreg_acadYear_relId,semreg_semester_relId
    order by ACY DESC
");

$result = $query->queryAll();

if ($result) {
    echo "
        <div style='max-height: 500px; overflow:auto; min-height: 500px;'>
        <table class='table table-hover table-bordered table-sm table-condensed'>
        <thead style='background-color: #8D0;'>
            <tr>
                <th rowspan=2>#</th>
                <th rowspan=2>ACY</th>
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
    foreach ($result as $key => $value) {
        $total = getCountByGender($value);
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($total as $gindex => $data) {
            if ($data["GENDER"] == 'MALE') {
                $maleCount = $data['data'];
            } else if ($data["GENDER"] == 'FEMALE') {
                $femaleCount = $data['data'];
            }
        }
        // $maleCount = isset($total[0]['GENDER']) && ($total[0]['GENDER'] == 'MALE') ? $total[0]['data'] : 0;
        // $femaleCount = isset($total[1]['GENDER']) && ($total[1]['GENDER'] == 'FEMALE') ? $total[1]['data'] : 0;
        $totalCount = $maleCount + $femaleCount;
        // if ($femaleCount == 0) {
        //     var_dump($total);
        //     exit;
        // }
        echo "<tr>
            <td>" . ++$count . "</td>        
            <td style='vertical-align:middle'>{$value['ACY']}</td>        
            <td style='text-align:center;'>{$maleCount}</td>        
            <td style='text-align:center;'>{$femaleCount}</td>        
            <td style='text-align:center;'>{$totalCount}</td>        
            <td></td>        
        </tr>";
        $totalAll += $totalCount;
        $totalMale += $maleCount;
        $totalFemale += $femaleCount;
    }
    echo "<tr></tr>";
    echo "</tbody>
            <tr style='text-align:center; background-color: #ACE; font-weight:bold'>
                <td colspan=2>Total</td>
                <td>{$totalMale}</td>
                <td>{$totalFemale}</td>
                <td>{$totalAll}</td>
            </tr>
        </table></div>";
} else {
    echo "No data found.";
}


function getCountByGender($value)
{
    $acy_id = $value['semreg_acadYear_relId'];
    $sem_id = $value['semreg_semester_relId'];

    return SemesterRegistration2::find()
        ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
        ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
        ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.gender as GENDER')
        ->addSelect('count(*) as data')
        ->where(['semreg_acadYear_relId' => $acy_id, 'semreg_semester_relId' => $sem_id])
        ->groupBy('GENDER')
        ->orderBy(['GENDER' => SORT_ASC])
        ->createCommand()->queryAll();
}
