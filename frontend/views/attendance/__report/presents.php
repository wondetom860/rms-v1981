<?php 

if (isset($model)) {
	$pStudents = $model->getPresentStudent();		

	if ($pStudents && count($pStudents) > 0) {
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
		foreach ($pStudents as $student) {
			$btnAbsent = "<button class='btn btn-warning btn-xs' title='Is student absent?' onclick='absentStudent({$student->id},{$model->id});return false;'>Change</button>";
			echo "<tr>
					<td>".++$count."</td>
					<td>{$student->std_regId}</td>
					<td>{$student->getFullName()}</td>
					<td style='text-align:center;'>{$btnAbsent}</td>
				</tr>";
		}
		echo "</table>";
	}else{
		echo "<p class='bg-warning'>No record found.</p>";
	}
}


?>