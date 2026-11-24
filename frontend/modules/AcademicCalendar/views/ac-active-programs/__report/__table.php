<table class='table table-sm table-striped table-condensed table-bordered table-hover' style='font-size:9pt;'>
	<caption style='font-weight:normal; color: black; text-align:center; background-color: #FED089'>
		>>Students:
	</caption>
	<thead style="background-color: grey;">
		<th>#</th>
		<th>Reg. Id</th>
		<th>Student's Full Name</th>
		<th>Admission Year</th>
		<th style='text-align: center;'>Program-Class Level<br>
			<span style='font-style: italic; font-size: 7pt;'>Last attended</span>
		</th>
		<th>Specialization</th>
		<th colspan=2>Option</th>
	</thead>
	<?php

	use \frontend\modules\AcademicCalendar\models\AcActivePrograms;

	$count = 0;
	$activeProgramsModel = new AcActivePrograms();

	$activeAcademicYears = $activeProgramsModel->getActiveAcademicYear(null);
	$ac_count = count($activeAcademicYears);

	foreach ($students as $student) {
		// echo "<pre>{$student->getFullName()}</pre><br>";
		if (
			!is_null($gsetModel->gs_spec_relId) &&
			($student->std_spec_relId != $gsetModel->gs_spec_relId && !is_null($student->std_spec_relId))
		) {
			continue;
		}

		if ($student->stdIsGraduated()) {
			continue;
		}
		$assignCheckBox = "";
		$assignStatus = '-';
		$spec = $student->std_spec_relId ? $student->stdSpecRel->spec_name : "-";

		$semregs = $student->getSemesterRegistrations()
			->orderBy([
				'semreg_acadYear_relId' => SORT_DESC,
				'semreg_semester_relId' => SORT_DESC
			])
			->all();
		$lastAcadYear = '';
		// check if the student is assigned to any active program in this semester:
		if ($ac_count > 0 && !is_null($student->std_group_realId)) {
			$assignCheckBox = checkIfActiveStudent($student, $activeAcademicYears);
			if (is_null($assignCheckBox)) { //student is assigned to a group that is not active in this semester.
				// check last grade submission:
				// check last semester status  => b/c in this case student has attented a class.
				if ($semregs && count($semregs) > 0) {
					$lastEntry = $semregs[0];
					$ssn = $lastEntry->semesterLastSsnRel ? $lastEntry->semesterLastSsnRel->sssn_name : '-';
					$assignStatus = "Year: {$lastEntry->semregAcadYearRel->sch_year_desc}-{$lastEntry->semregSemesterRel->semester_name}<br>";
					$lastAcadYear = $assignStatus . "{$lastEntry->semregClslvlRel->getClslvlDisp()}";

					$assignStatus .= "SGPA: " . sprintf("%0.2f", $lastEntry->semreg_SGPA) . "<br>";
					$assignStatus .= "CGPA: " . sprintf("%0.2f", $lastEntry->semreg_CGPA) . "<br>";
					$assignStatus .= "Status: " . $ssn;

					// check last entry pass status:
					//  && $lastEntry->semregStdRel->std_semester_status == 'ACTIVE'
					if ($lastEntry->semesterLastSsnRel && in_array($lastEntry->semesterLastSsnRel->sssn_pass_status, [1, 2])) { //pass and warning
						$assignCheckBox = "<input type='checkBox' id='{$student->id}_{$gsetModel->id}_00' onChange='assignStudent({$student->id},{$gsetModel->id});return false;' title='Group Student to this section'>";
					} else {
						// if Non-Regular student and in hte same class level, allow assigning.
						if (($lastEntry->semregStdRel->stdAdmissionRel->admission_name != "REGULAR") && ($lastEntry->semregClslvlRel->clslvl_num == $gsetModel->gsClslvlRel->clslvl_num)) {
							$assignCheckBox = "<input type='checkBox' id='{$student->id}_{$gsetModel->id}_00' onChange='assignStudent({$student->id},{$gsetModel->id});return false;' title='Group Student to this section'>";
						} else {
							$assignCheckBox = "{$lastEntry->semregStdRel->std_semester_status}";
						}
					}
				} else { //assigned to some group in that semester, but has not enrolled any course.
					$assignStatus = "Course enrollement record not found.";
					$assignCheckBox = "<input type='checkBox' id='{$student->id}_{$gsetModel->id}_00' onChange='assignStudent({$student->id},{$gsetModel->id});return false;' title='Group Student to this section'>";
				}
			} else { //if active student, for changing group, student should not enroll any course:
				// check if student enrolled course:
				if (isset($loadOnlyUngrouped) && !empty($loadOnlyUngrouped)) {
					continue;
				}
				$assignStatus = $assignCheckBox;
				$assignCheckBox = "";
			}
		} else {
			// this case is for fresh students.
			if ($semregs && count($semregs) > 0) {
				$lastEntry = $semregs[0];

				$ssn = $lastEntry->semesterLastSsnRel ? $lastEntry->semesterLastSsnRel->sssn_name : '-';
				$assignStatus = "Year: {$lastEntry->semregAcadYearRel->sch_year_desc}-{$lastEntry->semregSemesterRel->semester_name}<br>";
				$lastAcadYear = $assignStatus . "{$lastEntry->semregClslvlRel->getClslvlDisp()}";
				$assignStatus .= "SGPA: " . sprintf("%0.2f", $lastEntry->semreg_SGPA) . "<br>";
				$assignStatus .= "CGPA: " . sprintf("%0.2f", $lastEntry->semreg_CGPA) . "<br>";
				$assignStatus .= "Status: " . $ssn;

				$assignCheckBox = "<input type='checkBox' id='{$student->id}_{$gsetModel->id}_00' onChange='assignStudent({$student->id},{$gsetModel->id});return false;' title='Group Student to this section'>";
			} else {
				$assignStatus = "Fresh Year";
				$assignCheckBox = "<input type='checkBox' id='{$student->id}_{$gsetModel->id}_00' onChange='assignStudent({$student->id},{$gsetModel->id});return false;' title='Group Student to this section'>";
			}
		}
	?>
		<tr>
			<td><?= ++$count ?></td>
			<td><?= $student->std_regId ?></td>
			<td title='<?= $student->std_semester_status ?>'><?= $student->getFullName() ?></td>
			<td><?= $student->getYearOfAdmission() ?></td>
			<td>
				<?= $student->getProgramHeader() ?><br>
				<span style="font-size: 7pt; font-style: italic;"><?= $lastAcadYear ?></span>
			</td>
			<td><?= $spec ?></td>
			<td style='text-align: left;'><?= $assignStatus ?></td>
			<td style='text-align: left;'><?= $assignCheckBox ?></td>
		</tr>
	<?php } //else{ echo "<p class='bg-warning'>No record found</p>"; } 
	?>
</table>

<?php



// my functions:
function checkIfActiveStudent($student, $academicYears)
{
	if ($academicYears && count($academicYears) > 0) {
		$stdGset = $student->stdGroupReal;
		$btnUngroup = "";
		foreach ($academicYears as $academicYear) {
			if (($stdGset->gsAcadYearRel->sch_year_desc == $academicYear->school_year) && ($stdGset->gsSemRel->semester_name == $academicYear->semester)) {
				// check if enrolled course
				if (!$stdGset->studentEnrolledCourse($student->id)) {
					$btnUngroup = "<button class='btn btn-xs btn-warning pull-right badge badge-warning' onclick='ungroup({$stdGset->id},{$student->id}); return false;'>Cancel</button>";
				} else {
					$btnUngroup = "Enrolled.";
				}
				return "<span class='bg-success' title='Assigned to active group in this semester.'>{$stdGset->getGsDisp()}</span>{$btnUngroup}";
			}
		}
	}
}

?>