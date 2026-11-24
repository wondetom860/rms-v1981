<?php

use backend\models\Student;

$admissions = Student::find()
    ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
    // ->innerJoin('user', 'user.id=student.std_user_relId')
    ->select('school_year.sch_year_desc as Year')
    ->addSelect('count(*) as data')
    // ->addSelect('user.user_type as Profession')
    ->groupBy('Year')
    ->createCommand()->queryAll();

function getAdmissionReportProfessionWise2($admissionYear)
{

    // var_dump($admissionYear);exit;
    $result = [];
    $query = Student::find()
        ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.user_type as Profession')
        ->addSelect('count(*) as data')
        ->where([
            'school_year.sch_year_desc' => $admissionYear,
            'user.activated' => 1,
            'student.entry_type' => 1
        ])
        ->groupBy('Profession')
        ->orderBy('school_year.sch_year_desc')
        ->createCommand()->queryAll();

    if ($query) {
        foreach ($query as $item) {
            $result[strtoupper($item['Profession'])] = $item['data'];
        }
    }

    // print_r($result);exit;
    return $result;
}

$dataYearWise = [];
$years = [];

if ($admissions) {
    foreach ($admissions as $admission) {
        $years[] = $admission['Year'];
        $professionWise = getAdmissionReportProfessionWise2($admission['Year']);
        $professionWise['MILITARY'] = isset($professionWise['MILITARY']) ? $professionWise['MILITARY'] : 0;
        $professionWise['CIVIL'] = isset($professionWise['CIVIL']) ? $professionWise['CIVIL'] : 0;
        $professionWise['FOREIGNER'] = isset($professionWise['FOREGN']) ? $professionWise['FOREGN'] : 0;

        $dataYearWise['MILITARY'][] = $professionWise['MILITARY'];
        $dataYearWise['CIVIL'][] = $professionWise['CIVIL'];
        $dataYearWise['FOREIGN'][] = $professionWise['FOREIGNER'];
    }
}

use miloschuman\highcharts\Highcharts;

echo Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Yearly Intake Report - Military Profession Wise'],
        'xAxis' => [
            'categories' => $years
        ],
        'yAxis' => [
            'title' => ['text' => 'Yearly Admissions']
        ],
        'credits' => ['enabled' => false],
        'series' => [
            ['name' => 'MILITARY', 'data' => $dataYearWise['MILITARY']],
            ['name' => 'CIVIL', 'data' => $dataYearWise['CIVIL']],
            ['name' => 'FOREIGNER', 'data' => $dataYearWise['FOREIGN']]
        ]
    ]
]);
