<?php

use backend\models\Student;

$admissions = Student::find()
    ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
    ->innerJoin('user', 'user.id=student.std_user_relId')
    ->select('school_year.sch_year_desc as Year')
    ->addSelect('count(*) as data')
    ->groupBy('Year')
    ->orderBy('Year')
    ->where(['user.activated' => 1, 'student.entry_type' => 1])
    ->createCommand()->queryAll();

function getAdmissionReportGenderWise($admissionYear)
{
    $result = [];
    $query = Student::find()
        ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.gender as Gender')
        ->addSelect('count(*) as data')
        ->where(['school_year.sch_year_desc' => $admissionYear, 'user.activated' => 1])
        ->groupBy('Gender')
        ->createCommand()->queryAll();
    if ($query) {
        foreach ($query as $item) {
            $result[strtoupper($item['Gender'])] = $item['data'];
        }
    }
    return $result;
}

if ($admissions) {
?>
    <div style='max-height: 450px; overflow:auto; min-height: 450px;'>
        <table class="table table-condensed table-sm table-hover table-bordered">
            <caption style="font-weight:bolder; color:#000; text-align: center; background-color: #8EF;">
                Yearly Intake Report - tabular form (Year & Gender wise)
            </caption>
            <thead style="background-color: #8D0; text-align: center;">
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Academic Year</th>
                    <th colspan="3">Admission Count</th>
                    <th rowspan="2">Remark</th>
                </tr>
                <tr>
                    <th>Female</th>
                    <th>Male</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $totalFemale = 0;
                $totalMale = 0;
                $totalTotal = 0;
                foreach ($admissions as $admission) {
                    $genderWise = getAdmissionReportGenderWise($admission['Year']);
                    $genderWise['MALE'] = isset($genderWise['MALE']) ? $genderWise['MALE'] : 0;
                    $genderWise['FEMALE'] = isset($genderWise['FEMALE']) ? $genderWise['FEMALE'] : 0;

                    $totalFemale += $genderWise['FEMALE'];
                    $totalMale += $genderWise['MALE'];
                    $totalTotal += $admission['data'];
                    echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$admission['Year']}</td>
                    <td style='text-align: center;'>" . $genderWise['FEMALE'] . "</td>
                    <td style='text-align: center;'>" . $genderWise['MALE'] . "</td>
                    <td style='text-align: center;'>{$admission['data']}</td>
                    <td></td>
                </tr>
                ";
                }
                echo "<tr style='font-weight:bold; text-align: center; text-decoration:underline; font-style: italic; background-color:#ACE'>
                    <td colspan=2>Total</td>
                    <td>{$totalFemale}</td>
                    <td>{$totalMale}</td>
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