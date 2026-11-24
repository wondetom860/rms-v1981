<?php 

if ($model) {
	// $course = ;
	echo "<table class='table table-condensed table-bordered table-sm'>";
	echo "<caption style='color:#000; font-weight:normal'><center>Course Title : <u>{$model[0]->coff2detCourseRel->course_title}</u></center></caption>";
	echo __displayHeader();
	echo "<tbody>";
	echo __displayBody($model);
	echo "</tbody>";
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}

function __displayBody($model){
	$report = "";
	foreach ($model as $keycoff2det) {
		$inst = $keycoff2det->coff2detInstRel ? $keycoff2det->coff2detInstRel->getFullName() : 'Uknown';
		$totalStudents = count($keycoff2det->coff2detCoff2Rel->coff2GroupRel->wGroupAssignments);

		$enrolledStudents = count($keycoff2det->coff2detCoff2Rel->getSemesterRegistration2Details($keycoff2det->coff2det_course_relId)->all());

		$maxGrade = $keycoff2det->coff2detCoff2Rel->getMaxGrade($keycoff2det->coff2det_course_relId);
		$maxGrade = $maxGrade && count($maxGrade) > 0 ? $maxGrade[0]->srd2GradeRel->gs_ltr_grade : '-';
		$gs = $keycoff2det->coff2detCoff2Rel->coff2GroupRel;
		$gName = $gs->gsGroupRel->group_name;
		$clslvl = $gs->gsClslvlRel->getClslvlDisp();
		$p = $gs->gsDeptRel->dept_code.'-'.$gs->gsProgRel->program_name;
		$report .= 
			"<tr>
				<td>{$keycoff2det->coff2detCoff2Rel->coff2GroupRel->gsAcadYearRel->sch_year_desc}-{$keycoff2det->coff2detCoff2Rel->coff2GroupRel->gsSemRel->semester_name}</td>
				<td>{$gName}-(<i>{$p} {$clslvl}</i>)</td>
				<td>{$inst}</td>
				<td>{$enrolledStudents}/{$totalStudents}</td>
				<td>-</td>
				<td>{$maxGrade}</td>
				<td>-</td>
			</tr>";
	}

	return $report;
}

function __displayHeader(){
	return "
		<thead>
			<th>Academic Year</th>
			<th>Group</th>
			<th>Instructor</th>
			<th>#Student<small class='pull-right' style='font-style:italic;'>Enrolled/Total</small></th>
			<th>Max. Mark</th>
			<th>Max. Grade</th>
			<th>Evaluation</th>
		</thead>
		";
}

?>