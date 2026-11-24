<?php 

if ($records && count($records) > 0) {
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='border-color:black;'>
			<caption style='font-weight:normal; text-align: center; color:black;'>Programs for {$records[0]->academicYearRel->getAcHeader()}[ {$records[0]->academicYearRel->getSchedule()} ]</caption>
			<thead style='background-color:#FAC'>
				<th>#</th>
				<th>Program</th>
				<th>Department</th>
				<th>Class Year</th>
				<th>Semester</th>
				<th>Specialization</th>
				<th>Status</th>
				<th>Schedule</th>
			</thead>";
		// check for scheduled activities if academic calendar and action id are known.
		$checkSchedule = (isset($activityId) && !empty($activityId) ) && (isset($ayid) && !empty($ayid));
		$count = 0;
		foreach ($records as $record) {
			$cls = $record->classLevelRel? $record->classLevelRel->getClslvlDisp() : "-";
			$sem = $record->semesterRel? $record->semesterRel->semester_name : "-";
			$spec = $record->specRel? $record->specRel->spec_name : "-";
			$status = $record->status? 'Active' : "Not Active";

			$select = "<input type='checkBox' onChange='selectOne({$record->id});' name='selectionBox' class='selectionBox pull-right' id='{$record->id}'>";
			$sched = " - ";
			if ($checkSchedule && $model->checkScheduled($activityId,$record->id)) {
				$select = $model->getSchedule($activityId,$record->id,1);
				$sched = "({$select[0]}) : ";
				$select = $select[1];
			}
			

			echo "<tr>
					<td>".++$count."</td>
					<td>{$record->admissionRel->admission_name}-{$record->programRel->program_name}</td>
					<td>{$record->deptRel->dept_code}</td>
					<td>{$cls}</td>
					<td>{$sem}</td>
					<td>{$spec}</td>
					<td>{$status}</td>
					<td>{$sched} {$select}</td>
				</tr>";
		}
	echo "</table>";
}else{
	$addBtn = "<button class=\"btn btn-xs btn-primary pull-right\" title=\"Add schedule under this program\" onclick=\"loadProgramForm();return false;\">Add schedule</button>";
	if (isset($ayId)) {
		$addBtn = "<a class='btn btn-xs btn-primary' href='/AcademicCalendar/ac-program-schedule/create?pid={$model->id}' title='Add activity schedule under this program.'>Add Scehdule</a>";
	}

	echo "{$addBtn}";
	echo "<p class='bg-warning'>No record found.</p>";
}

?>