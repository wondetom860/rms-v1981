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
    select * from ac_active_programs
	inner join ac_academic_year on ac_academic_year.id=ac_active_programs.academic_year_relId
	inner join department on department.id=ac_active_programs.dept_relId
    where school_year=:acy AND semester=:sem
    order by program_relId,class_level_relId,dept_name
")->bindParam(':acy', $acy)->bindParam(':sem', $sem);

// gset->semregs returns all the students enrolled courses under this gset.

$result = $query->queryAll();

$result2 = AcActivePrograms::find()
    ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
    ->innerJoin('department', 'department.id=ac_active_programs.dept_relId')
    ->where(" school_year='{$acy}' AND semester='{$sem}' ")
    ->orderBy('program_relId,class_level_relId,dept_name')
    ->all();
if ($result2) {
    echo "<div style='max-height: 500px; overflow:auto;'>
        <table class='table table-hover table-bordered table-sm table-condensed' style='font-size: 9pt;'>
        <caption style='text-align:center; color:#000; font-weight:bolder;'>Course Enrollments - Department wise {$acy}-{$sem}</caption>
        <thead style='background-color: #8D0;'>
            <tr>
                <th rowspan=2>Program</th>
                <th rowspan=2>Class Year</th>
                <th rowspan=2>Department</th>
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
    $totalGender =  0;
    foreach ($result2 as $acp) {
        // $value => ACP [AcActiveProgram]
        $gsetIds = array_column($acp->getGroupSetups()->all(), 'id');

        $total = getCountByGender3($acp, $gsetIds);
        // $totalM = isset($total[0]) ? 
        $maleCount = isset($total[0]['GENDER']) && ($total[0]['GENDER'] == 'MALE') ? $total[0]['data'] : 0;
        $femaleCount = isset($total[1]['GENDER']) && ($total[1]['GENDER'] == 'FEMALE') ? $total[1]['data'] : 0;
        $clslll = ClassLevel::findOne($acp['class_level_relId']);
        $spec = $acp->spec_relId ? "({$acp->specRel->spec_name})" : '';
        $totalMale += $maleCount;
        $totalFemale += $femaleCount;
        $totalGender = ($maleCount + $femaleCount);
        $totalAll += $totalGender;
        echo "<tr>              
            <td style='vertical-align:middle'>{$clslll->clslvlProgramRel->program_name}</td>        
            <td style='vertical-align:middle'>{$clslll->getClslvlDisp()}</td>        
            <td style='vertical-align:middle'>{$acp->deptRel->dept_name}{$spec}</td>        
            <td style='text-align:center;'>{$maleCount}</td>        
            <td style='text-align:center;'>{$femaleCount}</td>        
            <td style='text-align:center;'>{$totalGender}</td>        
            <td></td>        
        </tr>";
    }
    echo "<tr></tr>";
    echo "</tbody>
            <tr style='text-align:center; background-color: #ACE; font-weight:bold'>
                <td colspan=3>Total</td>
                <td>{$totalMale}</td>
                <td>{$totalFemale}</td>
                <td>{$totalAll}</td>
            </tr>
        </table></div>";
} else {
    echo "No data found.";
}

/**
 * Summary of getCountByGender3
 * @param mixed $value
 * @param mixed $gsetIds
 * @return array
 */
function getCountByGender3($acp, $gsetIds)
{
    // $acy_id = $value['semreg_acadYear_relId'];
    // $sem_id = $value['semreg_semester_relId'];
    // $clslvl_id = $value['semreg_clslvl_relId'];

    return SemesterRegistration2::find()
        ->innerJoin('school_year', 'school_year.id=semester_registration_2.semreg_acadYear_relId')
        ->innerJoin('semester', 'semester.id=semester_registration_2.semreg_semester_relId')
        ->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
        ->innerJoin('user', 'user.id=student.std_user_relId')
        ->select('user.gender as GENDER')
        ->addSelect('count(*) as data')
        ->where([
            'semreg_acadYear_relId' => $acp->academicYearRel->schoolYearRel->id,
            'semreg_semester_relId' => $acp->academicYearRel->semesterRel->id,
            'semreg_clslvl_relId' => $acp->class_level_relId
        ])
        ->andWhere([
            'std_group_realId' => $gsetIds
        ])
        ->groupBy('GENDER')
        ->orderBy(['GENDER' => SORT_ASC])
        ->createCommand()->queryAll();
}
