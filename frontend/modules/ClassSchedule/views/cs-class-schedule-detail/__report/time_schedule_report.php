<?php

/** @var yii/web $this */
$this->title = "Time Table";
$academicWorkDay = \frontend\modules\ClassSchedule\models\CsAcademicWorkDay::find()->all();
if ($academicWorkDay && count($academicWorkDay) > 0) {
	$headerInfo = __getHeaderCaption($type, $coffdetModel);
	echo "
		<table class='table table-sm table-condensed table-bordered table-sm'>
		<caption style='font-weight: normal; color: #000; text-align: center; background-color:#FDE09A;'>{$headerInfo}</caption>
		<thead style='background-color:#a6df03;'>
			<th>DAY/TIME</th>
	";

	$justOne = $academicWorkDay[0];

	$classTimeDistrib = $justOne->csClassTimeDistributions;
	foreach ($classTimeDistrib as $key) {
		$class = $key->luncTime() ? 'warning' : ($key->nowTime() ? 'info' : 'active');
		echo "
			<th style='text-align:center;' class='bg-{$class}'>{$key->classCount()}<br><small style='font-size:7pt;'>{$key->getStartTime()}-{$key->getEndTime()}</small></td>
			";
	}
	echo "</thead><tbody>";
	// $modelClassSchedule = new \frontend\modules\ClassSchedule\models\CsClassScheduleDetail();

	foreach ($academicWorkDay as $key) {
		$dists = $key->csClassTimeDistributions;
		$bgToday = $key->isToday() ? 'info' : 'active';
		echo "<tr>";
		echo "<td class='bg-{$bgToday}' style='background-color:#FDE88A;'>{$key->day_name}</td>";
		foreach ($dists as $keyDist) {
			$schedules = $model->getScheduleReport($coffdetModel, $keyDist, $type);
			if ($schedules && count($schedules) > 0) {
				$class = 'default';
				$scheds = renderableSchedule($schedules, $type);
			} else {
				$class = $keyDist->luncTime() ? 'warning' : 'default';
				// $class = 'default';
				$scheds = "<br>";
			}
			echo "<td class='bg-{$class}'>{$scheds}</td>";
		}
		echo "</tr>";
	}
	echo "</tbody></table>";
	if ($type == 1) { //Class Scehdule report for instructors do need course and instructor name.
		$offerings = getOfferings($coffdetModel);
		echo "<h4 style='text-align:center;'>Courses and Instructors</h4>";
		echo "<table class='table table-sm table-condensed table-hover table-bordered'>
				<tr style='background-color:#FD9;'>
					<th>#</th>
					<th>Course Code</th>
					<th>Crdt Hr.</th>
					<th>Course title</th>
					<th>Instructor</th>
					<th>Tech. Asst.</th>
					<th>Remark</th>
				</tr>{$offerings}
			</table>";
	}
} else {
	echo "<p class='bg-error'>No record.</p>";
}

function getOfferings($coffdetModel){
	$offerings = $coffdetModel->coff2detCoff2Rel->courseOffering2Details;
	if($offerings){
		$rep = "";
		$count = 0;
		foreach ($offerings as $offering) {
			$course = $offering->coff2detCourseRel;
			$inst = "NA";
			if($course->course_crdthr != 0)
				$inst = $offering->coff2det_inst_relId ? $offering->coff2detInstRel->getFullName(1) : 'TBA';
			$ta = "NA";
			if($course->course_lab_hour != 0){
				$ta = $offering->coff2det_TA_relId ? $offering->coff2detTARel->getFullName(1) : 'TBA';
			}
			$rep .= "<tr>
					<td>".++$count."</td>
					<td style='white-space: nowrap'>{$course->course_code}</td>
					<td style='white-space: nowrap'>{$course->getCourseCredits()}</td>
					<td style='white-space: nowrap'>{$course->course_title}</td>
					<td style='white-space: nowrap'>{$inst}</td>
					<td>{$ta}</td>
					<td></td>
				</tr>";
		}
		return $rep;
	}else{
		return "<td colspan=6>Offering not found</td>";
	}
}

function renderableSchedule($schedules, $type)
{
	$sc = "";
	$fontGroup = "bg-default";
	$fontInst = "bg-default";
	if ($type == 1) {
		$fontGroup = 'bg-info';
	} else {
		$fontInst = 'bg-info';
	}
	foreach ($schedules as $keySchedule) {
		$sc .= "<small><center style=''>
				{$keySchedule->coff2detRel->coff2detCourseRel->course_code}/
				<font class='{$fontGroup}'>{$keySchedule->coff2detRel->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}</font>/
				{$keySchedule->roomRel->room}</small>";

				// <br>
				// <i>{$keySchedule->class_type} - <font class='{$fontInst}'>{$keySchedule->coff2detRel->coff2detInstRel->getFullName_av()}</font></i>
				// </center></small><br>
	}

	return $sc;
}

function __getHeaderCaption($type, $coffdetModel)
{
	$t = "";
	if ($type == 1) { //report id as per group
		$gset = $coffdetModel->coff2detCoff2Rel->coff2GroupRel;
		$spec = $gset->gs_spec_relId ? $gset->gsSpecRel->spec_name : '-';
		$dept = $gset->gs_dept_relId ? $gset->gsDeptRel->dept_name : '-';
		$group = $gset->gsGroupRel->group_name;
		$acadyear = $gset->gsAcadYearRel->sch_year_desc . "-" . $gset->gsSemRel->semester_name;
		$clslvl = $gset->gs_clslvl_relId ? $gset->gsClslvlRel->getClslvlDisp() : '-';
		$t .= "<h3>Class Time Table - <small>For Group</small></h3>";
		$t .= "
				<b>Department : </b> <u><i>{$dept}</i></u>
				<b>Specialization : </b> <u><i>{$spec}</i></u><br>
				<b>Class Level : </b> <u><i>{$clslvl}</i></u>
				<b>Group : </b> <u><i>{$group}</i></u><br>
				<b>Academic Year : </b><u> <i>{$acadyear}</i></u>
			";
	} else if ($type == 2) { //report id as per Instructor
		$acadyear = $coffdetModel->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc . "-" . $coffdetModel->coff2detCoff2Rel->coff2SemRel->semester_name;

		$t .= "<h3>Class Time Table - <small>For course instructor</small></h3>";
		$t .= "
				<b>Academic Year : </b> <i>{$acadyear}</i>
			";
	}
	return $t;
}
