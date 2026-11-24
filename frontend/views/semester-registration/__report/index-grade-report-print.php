<?php 

if ($model && count($model) > 0) {
	// $course = ;
	echo "<table class='table table-condensed table-bordered table-sm'>";
	echo "<caption style='color:#000; font-weight:normal; font-size: 10pt;'>
		<center><u><h4>Semester Status Report</h4></u></center><br>";

	$__header = "";
	// display academic year of offering 
	if (isset($printParams['semreg_acadYear_relId']) && !empty($printParams['semreg_acadYear_relId'])) {
		// echo "<br><center>Acadmic Year: <u>{$model[0]->semregAcadYearRel->sch_year_desc}</u></center>";
		$__header .= "<td><center><u>Acadmic Year</u><br><b>{$model[0]->semregAcadYearRel->sch_year_desc}</b></center></td>";
	}

	// display semester of offering 
	if (isset($printParams['semreg_semester_relId']) && !empty($printParams['semreg_semester_relId'])) {
		// echo "<br><center>Semester: <u>{$model[0]->semregSemesterRel->semester_name}</u></center>";
		$__header .= "<td><center><u>Semester</u><br><b>{$model[0]->semregSemesterRel->semester_name}</b></center></td>";
	}

	// echo "<br>";

	// display semester of offering 
	if (isset($printParams['semreg_clslvl_relId']) && !empty($printParams['semreg_clslvl_relId'])) {
		// echo "<br><center>Class Level: <u>{$model[0]->semregClslvlRel->getClslvlDisp()}-{$model[0]->semregClslvlRel->clslvlProgramRel->program_name}</u></center>";
		$__header .= "<td><center><u>Class Level</u><br><b>{$model[0]->semregClslvlRel->getClslvlDisp()}-{$model[0]->semregClslvlRel->clslvlProgramRel->program_name}</b></center></td>";
	}

	// display department offered the coure
	if (isset($printParams['std_dept_relId']) && !empty($printParams['std_dept_relId'])) {
		$dept = \backend\models\Department::findOne(intval($printParams['std_dept_relId']));
		$deptName = $dept? $dept->dept_name : '-';
		// echo "<br><center>: <u>{$deptName}</u></center>";
		$__header .= "<td><center><u>Department</u><br><b>{$deptName}</b></center></td>";
	}

	// echo "<br>";

	// display program offered the coure
	if (isset($printParams['std_prog_relId']) && !empty($printParams['std_prog_relId'])) {
		$prog = \backend\models\Program::findOne(intval($printParams['std_prog_relId']));
		$progName = $prog? $prog->program_name : '-';
		// echo "<br><center>: <u>{$progName}</u></center>";
		$__header .= "<td><center><u>Program</u><br><b>{$progName}</b></center></td>";
	}

	if (isset($printParams['semreg_last_ssn_relId']) && !empty($printParams['semreg_last_ssn_relId'])) {
		$__header .= "<td><center><u>Semester Status</u><br><b>{$model[0]->semesterLastSsnRel->sssn_name}</b></center></td>";
	}

	echo "<table class='table table-striped table-condensed'>
					<tbody>
						<tr>
							{$__header}
						</tr>
					</tbody>
			</table>";
	
	echo "</caption>";
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
	$count = 0;
	$modelGroupAssignment = new \backend\models\WGroupAssignment();
	$statusArray = [];
	$clslvl = "";
	foreach ($model as $semRegs) {
		$gass = $modelGroupAssignment->getStudentGroupAssignment($semRegs->semregAcadYearRel,$semRegs->semregSemesterRel,$semRegs->semregStdRel);

		if ($gass) {
			$gs = $gass->gasGsetRel;
			$clslvl = $gs->gsClslvlRel->getClslvlDisp();
		}else{
			$report .= 
			"<tr>
				<td>".++$count."</td>
				<td>{$semRegs->semregStdRel->std_regId}</td>
				<td>{$semRegs->semregStdRel->getFullName()}</td>
				<td>{$semRegs->semregStdRel->stdUserRel->user_type}</td>
				<td>{$semRegs->semregAcadYearRel->sch_year_desc}-{$semRegs->semregSemesterRel->semester_name}</td>
				<td>{$clslvl}</td>
				<td colspan='6' class='bg-warning'><span style='text-align:center;'>Group assignment entry NOT found.</span></td>
			</tr>";

			continue;
		}
		
		$p = $gs->gsAdmRel->admission_name.'-'.$gs->gsProgRel->program_name;
		$spec = isset($gs->gsSpecRel) ? $gs->gsSpecRel->spec_name : '';
		$lastStatus = $semRegs->semesterLastSsnRel ? $semRegs->semesterLastSsnRel->sssn_name : '-';
		isset($statusArray[$lastStatus]) ? $statusArray[$lastStatus]++ : ($statusArray[$lastStatus] = 1);
		$SGPA = "";
		$CGPA = "";
		if ($semRegs->semreg_gradeCompleted) {
			$SGPA = sprintf("%0.3f",$semRegs->semreg_SGPA);
			$CGPA = sprintf("%0.3f",$semRegs->semreg_CGPA);
		}
		$report .= 
			"<tr>
				<td>".++$count."</td>
				<td>{$semRegs->semregStdRel->std_regId}</td>
				<td>{$semRegs->semregStdRel->getFullName()}</td>
				<td>{$semRegs->semregStdRel->stdUserRel->gender}</td>
				<td>{$semRegs->semregStdRel->stdUserRel->user_type}</td>
				<td>{$clslvl}</td>
				<td>{$p}</td>
				<td>{$gs->gsDeptRel->dept_name}</td>
				<td>{$spec}</td>
				<td>{$SGPA}</td>
				<td>{$CGPA}</td>
				<td>{$lastStatus}</td>
			</tr>";
	}

	$statas = "";
	$statusCount = "";
	$statasCount = 0;
	$totalCount = 0;
	foreach (array_keys($statusArray) as $statusName) {
		$statas .= "<th>{$statusName}</th>";
		$statasCount++;
	}

	foreach (array_keys($statusArray) as $statusName) {
		$totalCount += $statusArray[$statusName];
		$statusCount .= "<td style='text-align:center;'>{$statusArray[$statusName]}</td>";
	}

	$summaryTable = "<table class='table table-bordered table-striped table-condensed table-hover'>
					<caption style='font-weight:bolder; text-align:center; color:#000; font-size:12pt;'><span>Status Summary</span></caption>
					<thead>
						<tr>
							<th colspan={$statasCount} style='text-align:center;'>Status</th>
							<th rowspan=2>Total</th>
							<th rowspan=2>Remark</th>
						</tr>
						<tr>
							{$statas}
						</tr>
					</thead>
					<tbody>
						<tr>
							{$statusCount}
							<td style=text-align:center;>{$totalCount}</td>
							<td></td>
						</tr>
					</tbody>
			</table>";
	$report .= "<tr>
				<td colspan=4></td>
				<td colspan=4 style='text-align:center;'><center>{$summaryTable}</center></td>
				<td colspan=4></td>
			</tr>";

	return $report;
}

function __displayHeader(){
	return "
		<thead>
			<th>#</th>
			<th>ID No.</th>
			<th>Student Name</th>
			<th>Gender</th>
			<th>Student Type</th>
			<th>Class Year</th>
			<th>Program</th>
			<th>Department</th>
			<th>Specialization</th>
			<th>SGPA</th>
			<th>CGPA</th>
			<th>Status</th>
		</thead>
		";
}

?>