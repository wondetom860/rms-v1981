<?php

use backend\models\Student;

$admissions = Student::find()
    ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
    ->innerJoin('user', 'user.id=student.std_user_relId')
    ->select('school_year.sch_year_desc as Year')
    ->addSelect('count(*) as data')
    // ->addSelect('user.user_type as Profession')
    ->groupBy('Year')
    ->orderBy('Year')
    ->where(['user.activated' => 1, 'student.entry_type' => 1])
    ->createCommand()->queryAll();

function getAdmissionReportProfessionWise($admissionYear)
{

    // var_dump($admissionYear);exit;
    $result = [];
    $query = Student::find()
        ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.user_type as Profession')
        ->addSelect('count(*) as data')
        ->where(['school_year.sch_year_desc' => $admissionYear, 'user.activated' => 1])
        ->groupBy('Profession')
        ->createCommand()->queryAll();

    if ($query) {
        foreach ($query as $item) {
            $result[strtoupper($item['Profession'])] = $item['data'];
        }
    }

    // print_r($result);exit;
    return $result;
}

if ($admissions) {
    // print_r($admissions);exit;
?>
    <div style='max-height: 400px; overflow:auto; min-height: 400px;'>
        <table class="table table-condensed table-sm table-hover table-bordered">
            <caption style="font-weight:bolder; color:#000; text-align: center; background-color: #8EF;">
                Yearly Intake Report - tabular form (Military Profession wise)
            </caption>
            <thead style="background-color: #8D0; text-align: center;">
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Academic Year</th>
                    <th colspan="4">Admission Count</th>
                    <th rowspan="2">Remark</th>
                </tr>
                <tr>
                    <th>Military</th>
                    <th>Civil</th>
                    <th>Foreigner</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $totalMilitary = 0;
                $totalCivil = 0;
                $totalForeign = 0;
                $totalTotal = 0;
                foreach ($admissions as $admission) {
                    $professionWise = getAdmissionReportProfessionWise($admission['Year']);
                    $professionWise['MILITARY'] = isset($professionWise['MILITARY']) ? $professionWise['MILITARY'] : 0;
                    $professionWise['CIVIL'] = isset($professionWise['CIVIL']) ? $professionWise['CIVIL'] : 0;
                    $professionWise['FOREIGNER'] = isset($professionWise['FOREGN']) ? $professionWise['FOREGN'] : 0;
                    $totalMilitary += $professionWise['MILITARY'];
                    $totalCivil += $professionWise['CIVIL'];
                    $totalForeign += $professionWise['FOREIGNER'];
                    $totalTotal += $admission['data'];

                    // var_dump($professionWise);exit;

                    echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$admission['Year']}</td>
                    <td style='text-align: center;'>" . $professionWise['MILITARY'] . "</td>
                    <td style='text-align: center;'>" . $professionWise['CIVIL'] . "</td>
                    <td style='text-align: center;'>" . $professionWise['FOREIGNER'] . "</td>
                    <td style='text-align: center;'>{$admission['data']}</td>
                    <td></td>
                </tr>
                ";
                }
                echo "<tr style='font-weight:bold; text-align: center; text-decoration:underline; font-style: italic; background-color:#ACE'>
                    <td colspan=2>Total</td>
                    <td>{$totalMilitary}</td>
                    <td>{$totalCivil}</td>
                    <td>{$totalForeign}</td>
                    <td>{$totalTotal}</td>
            </tr>";
                ?>
            </tbody>
        </table>
    </div>
<?php
} else {
    echo "<p class='label bg-warning'>No data found!</p>";
}
?>