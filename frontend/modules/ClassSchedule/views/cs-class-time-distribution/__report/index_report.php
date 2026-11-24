<?php 


$AcademicWorkDay = \frontend\modules\ClassSchedule\models\CsAcademicWorkDay::find()->all();

// var_dump($AcademicWorkDay);die();

// var_dump(d);die();

if ($AcademicWorkDay && count($AcademicWorkDay) > 0) {
	echo "
		<table class='table table-xs table-condensed table-bordered table-sm'>
		<caption style='font-weight: bold; color: #000; text-align: center; background-color:#FDE09A;'>Class Schedule</caption>
		<thead style='background-color:#a6df03;'>
			<th>DAY/TIME</th>
	";

	$justOne = $AcademicWorkDay[0];

	$classTimeDistrib = $justOne->csClassTimeDistributions;
	foreach ($classTimeDistrib as $key) {
		$class = $key->luncTime() ? 'warning' : ($key->nowTime() ? 'info':'active') ;
		echo "
			<th style='text-align:center;' class='bg-{$class}'>{$key->classCount()}<br><small style='font-size:7pt;'>{$key->getStartTime()}-{$key->getEndTime()}</small></td>
			";
	}
	echo "</thead><tbody>";

	foreach ($AcademicWorkDay as $key) {
		$dists = $key->csClassTimeDistributions;
		$bgToday = $key->isToday() ? 'info' : 'active';
		echo "<tr>";
		echo "<td class='bg-{$bgToday}' style='background-color:#FDE88A;'>{$key->day_name}</td>";
		foreach ($dists as $keyDist) {
			$class = $keyDist->luncTime() ? 'warning' : 'active';
			if ($keyDist->nowTime() && $keyDist->workDayRel->isToday()) {
				$class = 'success';
			}
			$schedules = $keyDist->csClassScheduleDetails;
			if ($schedules && count($schedules) > 0) {
				$scheds = renderableSchedule($schedules);
				$class = 'danger';
			}else{
				if ($keyDist->luncTime()) {
					$scheds = "";
				}else{
					$scheds = "";
				}
			}
			echo "<td class='bg-{$class}'>{$scheds}</td>";
		}
		echo "</tr>";
	}

	echo "</tbody></table>";
}else{
	echo "<p class='bg-error'>No AcademicWorkDay entry found.</p>";
}


function renderableSchedule($schedules){
	$sc = "";
	foreach ($schedules as $keySchedule) {
		if (strtoupper($keySchedule->class_type) == 'LAB') {
			// display TA's Name
			$inst_name = $keySchedule->coff2detRel->coff2detTARel ? $keySchedule->coff2detRel->coff2detTARel->getFullName_av() : 'Uknown';
		}else{
			$inst_name = $keySchedule->coff2detRel->coff2detInstRel ? $keySchedule->coff2detRel->coff2detInstRel->getFullName_av() : 'Uknown';
		}
		$sc .= "<small><center style='border-bottom:1px solid black;'>
			{$keySchedule->coff2detRel->coff2detCourseRel->course_code}/
			{$keySchedule->coff2detRel->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}/
			{$keySchedule->roomRel->room}<br>
			<i>{$keySchedule->class_type} - {$inst_name}</i>
		</center></small><br>";
	}

	return $sc;
}
?>


