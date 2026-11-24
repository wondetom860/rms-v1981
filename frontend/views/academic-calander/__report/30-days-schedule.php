<?php 

use \backend\models\AcademicCalander; 

$dt = date("Y-m-d");

// $date =  date( "Y-m-d", strtotime( "$dt +{$intrvalDays} day" ) );
$start_date = date( "Y-m-d", strtotime( "$dt -0 day" ) );
$end_date = date( "Y-m-d", strtotime( "$dt +30 day" ) );

// var_dump($end_date,$end_date);die();

$schedules = AcademicCalander::getSchedule($start_date,$end_date);

if ($schedules && count($schedules) > 0) {
	$btnMng = "";
	if (Yii::$app->user->can('Admin')) {
		$btnMng = "<a class='btn btn-xs btn-warning pull-right' href='/academic-calander/'>Manage</a>";
	}
	echo "
		<table class='table table-bordered table-condensed table-striped table-sm' style='border-color:#000; font-size:10pt;'>
			<caption style='font-weight:normal; text-align:center; border-color:#000;'>Registrar 30 days schedule from <i class='bg-info'>{$start_date}</i> to <i class='bg-info'>{$end_date}</i>{$btnMng}</caption>
			<thead style='background-color: #DFD;border-color:#000;'>
				<th style='border-color:#000;'>Task/Activity</th>
				<th style='border-color:#000;'>Academic Year</th>
				<th style='border-color:#000;'>Program</th>
				<th style='border-color:#000;'>Class Level</th>
				<th style='border-color:#000;'>Start Date</th>
				<th style='border-color:#000;'>End</th>
				<th style='border-color:#000;'>Status</th>
			</thead>
			<tbody style='border-color:#000;'>
	";

	$count = 0;

	foreach ($schedules as $keyAacademicSchedule) {
		$st = $keyAacademicSchedule->getStatus();
		$acadYear = $keyAacademicSchedule->acad_acadYear_relId ? $keyAacademicSchedule->acadAcadYearRel->sch_year_desc : 'Academic Year';
		$sem = $keyAacademicSchedule->acad_sem_relId ? $keyAacademicSchedule->acadSemRel->semester_name: 'Semester';
		echo "<tr style='border-color:#000;'>
				<td style='border-color:#000;'>{$keyAacademicSchedule->acadActionRel->aca_name}</td>
				<td style='border-color:#000;'>{$acadYear}-{$sem}</td>
				<td style='border-color:#000;'>{$keyAacademicSchedule->getProgram()}</td>
				<td style='border-color:#000;'>{$keyAacademicSchedule->getClassLevl()}</td>
				<td style='border-color:#000;'>{$keyAacademicSchedule->getStartDate()}</td>
				<td style='border-color:#000;'>{$keyAacademicSchedule->getEndDate()}</td>
				<td style='border-color:#000;' class='bg-{$st[0]}'>{$st[1]}</td>
			</tr>";
	}

	echo "</tbody>
		</table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}


?>

