<?php 

if (isset($model)) {
	$aStudents = $model->getAbsents();

	if ($aStudents && count($aStudents) > 0) {
		echo "<table class='table table-condensed table-sm table-bordered' cellpadding=3 cellspacing=0>";
		echo "<thead>";
		echo "
			<th>#</th>
			<th>Reg. ID</th>
			<th>Student Name</th>
			<th>Status</th>
			";
		echo "</thead>";
		$count = 0;
		foreach ($aStudents as $student) {
			$btnPresent = "<button class='btn btn-warning btn-xs' title='Is student present?' onclick='presentStudent({$student->id},{$model->id});return false;'>Change</button>";
			echo "<tr>
					<td>".++$count."</td>
					<td>{$student->std_regId}</td>
					<td>{$student->getFullName()}</td>
					<td style='text-align:center;'>{$btnPresent}</td>
				</tr>";
		}
		echo "</table>";
	}
}


?>