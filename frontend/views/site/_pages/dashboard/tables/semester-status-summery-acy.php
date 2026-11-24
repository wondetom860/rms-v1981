<?php

use backend\models\ClassLevel;

$sql1 = "
    SELECT 
        concat(sch_year_desc,'-',semester_name) as ACADEMICYEAR,
        upper(sssn_name) as STATUS,
        user_type AS TYPE,
        gender as GENDER,
        count(sssn_name) as STATUS_COUNT
    FROM 
        semester_registration_2
        left join student_semester_status_name on semester_registration_2.semreg_last_ssn_relId = student_semester_status_name.id
        inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join student on student.id=semester_registration_2.semreg_std_relId
        inner join user on user.id=student.std_user_relId
        inner join admission on admission.id=student.std_admission_relId
    WHERE
        admission_name = 'REGULAR' AND sssn_name IS NOT NULL
    GROUP BY
        semreg_acadYear_relId, semreg_semester_relId,sssn_name,gender,user_type
    ORDER BY
        semreg_acadYear_relId DESC, semreg_semester_relId DESC, sssn_pass_status ASC,STATUS_COUNT DESC, sssn_name ASC
";

$sql2 = "
    SELECT 
        concat(sch_year_desc,'-',semester_name) as ACADEMICYEAR,
        upper(sssn_name) as STATUS,
        user_type AS TYPE,
        gender as GENDER,
        count(sssn_name) as STATUS_COUNT
    FROM 
        student_semester_status_name
        inner join semester_registration_2 on semester_registration_2.semreg_last_ssn_relId = student_semester_status_name.id
        inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join student on student.id=semester_registration_2.semreg_std_relId
        inner join user on user.id=student.std_user_relId
        inner join admission on admission.id=student.std_admission_relId
    WHERE
        admission_name = 'REGULAR'
    GROUP BY
        semreg_acadYear_relId, semreg_semester_relId,sssn_name,gender,user_type
    ORDER BY
        semreg_acadYear_relId DESC, semreg_semester_relId DESC, STATUS_COUNT DESC
    ";
$sql3 = "
    SELECT 
        concat(sch_year_desc,'-',semester_name) as ACADEMICYEAR,
        concat(clslvl_num,'-',clslvl_name,'(',program_name,')') as CLASSLEVEL,
        upper(sssn_name) as STATUS,
        user_type AS TYPE,
        gender as GENDER,
        count(sssn_name) as STATUS_COUNT
    FROM 
        semester_registration_2
        left join student_semester_status_name on semester_registration_2.semreg_last_ssn_relId = student_semester_status_name.id
        inner join school_year on school_year.id=semester_registration_2.semreg_acadYear_relId
        inner join semester on semester.id=semester_registration_2.semreg_semester_relId
        inner join student on student.id=semester_registration_2.semreg_std_relId
        inner join user on user.id=student.std_user_relId
        inner join admission on admission.id=student.std_admission_relId
        inner join class_level on class_level.id=semester_registration_2.semreg_clslvl_relId
        inner join program on program.id=class_level.clslvl_program_relId
    WHERE
        admission_name = 'REGULAR' AND sssn_name IS NOT NULL
    GROUP BY
        semreg_acadYear_relId, semreg_semester_relId,semreg_clslvl_relId,gender,user_type
    ORDER BY
        semreg_acadYear_relId DESC, 
        semreg_semester_relId DESC, 
        semreg_clslvl_relId DESC,
        sssn_pass_status ASC,STATUS_COUNT DESC, 
        sssn_name ASC
";
$query = Yii::$app->db->createCommand($sql1);
$result = $query->queryAll();

// read totalEnrollments per AcademicYear (acadYear-Sem)
function getTotalEnrolled($acadYear)
{
    $ay = explode('-', $acadYear);
    $sql = "
        SELECT 
            count(*) as TOTALENROLLMENT 
        FROM 
            semester_registration_2 semreg
            inner join school_year on school_year.id=semreg.semreg_acadYear_relId
            inner join semester on semester.id=semreg.semreg_semester_relId
        WHERE
            school_year.sch_year_desc = '{$ay[0]}' AND
            semester.semester_name = '{$ay[1]}'
    ";

    return Yii::$app->db->createCommand($sql)->queryAll();
}

if ($result) {
    $status_array = array_unique(array_column($result, 'STATUS'));
    $types_array = array_unique(array_column($result, 'TYPE'));
    $gender_array = array_unique(array_column($result, 'GENDER'));
    $gender_array = array_merge($gender_array, ['TOTAL']);

    $acadYears = array_unique(array_column($result, 'ACADEMICYEAR'));

    // var_dump($acadYears);exit;

    $statusHeaders = getStatusHeaders32($result);
    // $headersCount = $statusHeaders[0];
    // $headersArray = $statusHeaders[2];
    // $statusHeaders = $statusHeaders[1];
    echo "<div style='max-height: 500px; overflow:auto; min-height: 500px;'>
        <table class='table table-hover table-bordered table-sm table-condensed' style='font-size: 9pt;'>
        <caption style='text-align:center; color:#000; font-weight:bolder;'>Academic Semester Status Sammery : Regular Admissions</caption>
        <thead style='background-color: #FDA;'>
        {$statusHeaders}
        </thead>  
        <tbody>";
    $tt = 0;
    // $kheadersCount = $headersCount + 2;
    // $colValues = [];
    // $byAcadYear = array_filter($result,function($r))
    foreach ($acadYears as $key => $value) {
        $byAcadYear = array_filter($result, function ($r) use ($value) {
            return $r['ACADEMICYEAR'] == $value;
        });
        $rowdata = getRowData($value, $status_array, $types_array, $gender_array, $byAcadYear);
        $enrolled = getTotalEnrolled($value);
        $yEn = $enrolled ? $enrolled[0]['TOTALENROLLMENT'] : '-';
        echo "<tr title='{$value}'>
            <td rowsapn=2>{$value}</td>
            <td style='background-color: #89F;text-align: center;'>{$yEn}</td>
            {$rowdata}
       </tr>";
    }
    echo "</tbody>
        </table>
    ";
} else {
    echo "Result not published yet";
}

function getRowData($acadYear, $status_array, $types_array, $gender_array, $byAcadYear)
{
    $rep = "";
    $total__ = 0;
    foreach ($status_array as $keyStatus => $status) {
        $stat_sum = 0;
        $byStatus = array_filter($byAcadYear, function ($r) use ($status) {
            return $r['STATUS'] == $status;
        });
        $scr_colspan = count($types_array) * count($gender_array);
        $hoaik = $scr_colspan - 2;
        if ($byStatus) {
            // 
            foreach ($types_array as $keyTypes => $type) {
                $byTypes = array_filter($byStatus, function ($tt) use ($type) {
                    return $tt['TYPE'] == $type;
                });
                // var_dump($byTypes);exit;

                if ($byTypes) {
                    $maleArray = array_filter($byTypes, function ($ttt) {
                        return $ttt['GENDER'] == 'MALE';
                    });
                    $femaleArray = array_filter($byTypes, function ($ttt) {
                        return $ttt['GENDER'] == 'FEMALE';
                    });

                    // var_dump($maleArray,$femaleArray);exit;

                    $male = array_sum(array_column($maleArray, 'STATUS_COUNT'));
                    $female = array_sum(array_column($femaleArray, 'STATUS_COUNT'));
                    $total = $male + $female;
                    $rep .= "
                        <td>{$male}</td>
                        <td>{$female}</td>
                        <td style='background-color:#96D; text-align: center;'>{$total}</td>
                    ";
                    $total__ += $total;
                    $stat_sum += $total;
                } else {
                    $scr_colsp = (count($gender_array));
                    while ($scr_colsp > 0) {
                        $rep .= "<td>-</td>";
                        $scr_colsp--;
                    }
                }
            }
        } else {
            while ($scr_colspan > 0) {
                $rep .= "<td>-</td>";
                $scr_colspan--;
            }
            // no data found by this semetser status
        }

        // $rep .= "<tr style='font-wieight: bold'>
        //             <td colspan={$hoaik} style='text-align: center;'>Total</td>
        //             <td style='text-align: center;'>{$stat_sum}</td>
        //         </tr>";
    }
    $rep .= "<td style='text-align: center; font-weight: bold;'>{$total__}</td>";
    return $rep;
}


function getStatusHeaders32($result)
{
    $status_array = array_unique(array_column($result, 'STATUS'));
    $types_array = array_unique(array_column($result, 'TYPE'));
    $gender_array = array_unique(array_column($result, 'GENDER'));
    $gender_array = array_merge($gender_array, ['TOTAL']);

    $scr_colspan = (count($status_array) * count($types_array) * count($gender_array));
    $status_header_row = getStatusHeaderRow($status_array, $gender_array, $types_array);
    // $types_header_row = getTypesHeaderRow($status_array, $gender_array, $types_array);
    // $gender_header_row = getGenderHeaderRow($status_array, $gender_array, $types_array);
    $report = "<tr>
                <th rowspan=4>Academic Year</th>
                <th rowspan=4>Enrolled</th>
                <th style='text-align: center;' colspan={$scr_colspan}>Status Count Report</th>
                <th rowspan=4>Total</th>
            </tr>
            <tr>{$status_header_row[0]}</tr>
            <tr>{$status_header_row[1]}</tr>
            <tr>{$status_header_row[2]}</tr>

            
           ";
    return $report;
}

function getStatusHeaderRow($status_array, $gender_array, $types_array)
{
    $report = "";
    $status_header_col_span = count($gender_array) * count($types_array);
    $types_header_row = "";
    $gender_header_row = "";
    foreach ($status_array as $index => $status) {
        $sub = getTypesHeaderRow($status_array, $gender_array, $types_array);
        $types_header_row .= $sub[0];
        $gender_header_row .= $sub[1];
        $report .= "<th style='text-align: center;' colspan={$status_header_col_span}>{$status}</th>";
    }
    return [$report, $types_header_row, $gender_header_row];
}


function getTypesHeaderRow($status_array, $gender_array, $types_array)
{
    $report = "";
    $types_header_col_span = count($gender_array);
    $gender_header_row = "";
    foreach ($types_array as $index => $type) {
        $gender_header_row .= getGenderHeaderRow($gender_array);
        $type = $type == 'FOREGN' ? 'FOREIGN' : $type;
        $report .= "<th style='text-align: center;' colspan={$types_header_col_span}>{$type}</th>";
    }
    return [$report, $gender_header_row];
}

function getGenderHeaderRow($gender_array)
{
    $report = "";
    foreach ($gender_array as $index => $gender) {
        $gg = $gender ? $gender[0] : $gender;
        $report .= "<th style='text-align: center;'>{$gg}</th>";
    }
    return $report;
}
