<?php

if ($records && count($records) > 0) {
	if (isset($options) && $options == false) {
		$btn = $btnMng = "";
	} else {
		// $btn = "<button class=\"btn btn-xs btn-primary pull-right\" title=\"Add programs on this academic year\" onclick=\"loadProgramForm();return false;\">Add Program</button>";
		// $btn = "<a class=\"btn btn-xs btn-primary pull-right\" title=\"Add programs under this academic year\" href = '/AcademicCalendar/ac-active-programs/create?syid={$records[0]->academic_year_relId}' return false;\">Add Program</a>";
		$btn = "<a class=\"btn btn-xs btn-primary pull-right\" style='margin-right:3px;' title=\"Add programs under this academic year\" href = '/course-offering/index' return false;\">Add Program</a>";
		$btnMng = "<a class=\"btn btn-xs btn-warning pull-right\" style='margin-right:3px;' title=\"Manage Programs\" href='/AcademicCalendar/ac-active-programs/'>Manage</a>";
	}

	echo "<table class='table table-striped table-condensed table-bordered' style='font-size:8pt;'>
			<caption style='font-weight:normal; text-align: center; color:black;'>Programs for {$records[0]->academicYearRel->getAcHeader()}{$btn}{$btnMng}</caption>
			<thead style='background-color: #DEF;'>
				<th>#</th>
				<th>Program</th>
				<th>Department</th>
				<th>Class Year</th>
				<th>Semester</th>
				<th>Specialization</th>
				<th>Status</th>
				<th>Group</th>
				<th>Remark</th>
			</thead>";
	$count = 0;
	foreach ($records as $record) {
		$cls = $record->classLevelRel ? $record->classLevelRel->getClslvlDisp() : "-";
		$sem = $record->semesterRel ? $record->semesterRel->semester_name : "-";
		$spec = $record->specRel ? $record->specRel->spec_name : "-";
		$status = $record->getStatus();
		$groups = $record->getGroups();
		$gc = $record->graduate_class ? 'GC' : "";
		echo "<tr>
					<td>" . ++$count . "</td>
					<td><b>{$record->admissionRel->admission_name[0]}-{$record->programRel->program_name[0]}</b></td>
					<td style='text-align:center;'>{$record->deptRel->dept_code}</td>
					<td>{$cls}</td>
					<td>{$sem}</td>
					<td>{$spec}</td>
					<td>{$status}</td>
					<td>{$groups}</td>
					<td>{$gc}</td>
				</tr>";
	}
	echo "</table>";
} else {
	$addBtn = "<button class=\"btn btn-xs btn-primary pull-right\" title=\"Add programs under this academic year\" onclick=\"loadProgramForm();return false;\">Add Program</button>";
	if (isset($ayId)) {
		$addBtn = "<a class=\"btn btn-xs btn-primary pull-right\" title=\"Add programs under this academic year\" href = '/AcademicCalendar/ac-active-programs/create?syid={$ayId}' return false;\">Add Program</a>";
	}

	echo "{$addBtn}";

	echo "<p class='bg-warning'>No record found.</p>";
}


?>

<script type="text/javascript">
	window.PrintList = (gsetId) => {
		window.open('/w-group-setup/get-assignments2?wt_gsetUp_entry=' + gsetId,
			'title:Student List',
			'height=1100,width=700'
		);
		//get-assignments?wt_gsetUp_entry=
	}
</script>