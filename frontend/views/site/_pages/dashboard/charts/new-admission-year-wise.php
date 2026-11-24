<?php

use backend\models\Student;
use practically\chartjs\widgets\Chart;

echo Chart::widget([
    'type' => Chart::TYPE_BAR,
    'datasets' => [
        [
            'query' => Student::find()
                ->innerJoin('school_year', 'school_year.id=student.std_acadYear_relId')
                ->innerJoin('user', 'user.id=student.std_user_relId')
                ->select('school_year.sch_year_desc as Year')
                ->addSelect('count(*) as data')
                ->where(['user.activated' => 1,'student.entry_type' => 1])
                ->orderBy('Year')
                ->groupBy('Year')
                ->createCommand(),
            'labelAttribute' => 'Year',
            'label' => 'New admissions count',
        ],
        // 'title' => 'New Admission Report - Year Wise'
    ],
    'clientOptions' => [
        'title' => [
            'display' => true,
            'text' => 'Yearly Intake Status',
        ],
        // 'label' => ['display' => false],
    ]
]);
