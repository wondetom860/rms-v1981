<?php

if ($model && count($model) > 0) {
	// $course = ;
	$course = $model[0]->coff2detCourseRel;
	echo "<div class='container-fluid table-responsive'>
		<table class='table table-condensed table-bordered table-sm' style='font-size:9pt;'>";
	echo "<caption style='color:#000; font-weight:normal; font-size: 10pt;'>
		<center><u>Course Assignment Report</u><br>
		<b>Course:</b> [{$course->getCourseCredits()}]</b>-{$course->course_title}</center>";

	// display semester of offering 
	$sem = "";
	if (isset($printParams['sem']) && !empty($printParams['sem'])) {
		$sem = "-{$model[0]->coff2detCoff2Rel->coff2SemRel->semester_name}";
	}

	// display academic year of offering 
	if (isset($printParams['acadYear']) && !empty($printParams['acadYear'])) {
		echo "<center>Acadmic Year: <u>{$model[0]->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}{$sem}</u></center><br>";
	}

	// display department offered the coure
	if (isset($printParams['department']) && !empty($printParams['department'])) {
		echo "<center>Department: <u>{$model[0]->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->dept_name}</u></center><br>";
	}

	echo "<caption>";
	echo __displayHeader();
	echo "<tbody>";
	echo __displayBody($model);
	echo "</tbody>";
	echo "</table></div>";
} else {
	echo "<p class='bg-warning'>No record.</p>";
}

function __displayBody($model)
{
	$report = "";
	$count = 0;
	foreach ($model as $keycoff2det) {
		if ($keycoff2det->coff2detCoff2Rel) {
			$gs = $gs = $keycoff2det->coff2detCoff2Rel->coff2GroupRel;
		} else {
			continue;
		}
		$course = $keycoff2det->coff2detCourseRel;
		$inst = isset($keycoff2det->coff2detInstRel) ? $keycoff2det->coff2detInstRel->getFullName() : '';
		$clslvl = $gs->gsClslvlRel->getClslvlDisp();
		$p = $gs->gsAdmRel->admission_name[0] . '-' . $gs->gsProgRel->program_name[0];
		$spec = isset($gs->gsSpecRel) ? $gs->gsSpecRel->spec_name : '';
		$report .=
			"<tr>
				<td>" . ++$count . "</td>
				<td>{$keycoff2det->coff2detCoff2Rel->coff2GroupRel->gsAcadYearRel->sch_year_desc}-{$keycoff2det->coff2detCoff2Rel->coff2GroupRel->gsSemRel->semester_name}</td>
				<td>{$clslvl}</td>
				<td><b>{$p}</b></td>
				<td>{$gs->gsDeptRel->dept_code}</td>
				<td>{$spec}</td>
				<td><b>{$inst}</td>
			</tr>";
	}

	return $report;
}

function __displayHeader()
{
	return "
		<thead>
			<th>#</th>
			<th>ACY</th>
			<th>Class Year</th>
			<th>Program</th>
			<th>Department</th>
			<th>Spec./Stream</th>
			<th>Course Inst./TA</th>
		</thead>
		";
}
