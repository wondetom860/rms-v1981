<?php 

if (isset($assignments) && isset($model)) {
	$pts = isset($pts) && !empty($pts);
	if ($assignments && count($assignments) > 0) {
		$spec = isset($model->gs_spec_relId) ? " >> ".$model->gsSpecRel->spec_name : '';
		echo "
			<table class='table table-bordered table-sm table-stripped table-hover table-condensed'>
				<caption style='font-weight: bold; background-color:#42f5e9; color:#000;'>
					<center>
							<h5 style='font-weight: bold; font-size:10pt; text-decoration: underline'>Student List</h5>
							<b><i>{$model->getAcadYearHeader()}</i></b> >> 
							<b><i>{$model->gsDeptRel->dept_name}-{$model->gsClslvlRel->getClslvlDisp()}</i></b>{$spec}<br>
							<u><i>{$model->getGsDisp()}</i></u>
					</center>
				</caption>"	; 
		$ptsss = $pts ? "Remark" : "Option";
		echo "<thead style='background-color:#8D0;'>
					<th>#</th>
					<th>Reg. ID</th>
					<th>Full Name</th>
					<th>Gender</th>
					<th>Profession</th>
					<th>Nationality</th>
					<th>{$ptsss}</th>
				</thead><tbody>";
		$count = 0;
		foreach ($assignments as $keyGsAssignment) {
			$optBtns = $pts?"": "<button class='btn btn-xs btn-success btn__stds' id='btn__std{$keyGsAssignment->gas_std_relId}' onclick='showPointHistory({$keyGsAssignment->gas_std_relId});return false;'>Points</button>";
			$student = $keyGsAssignment->gasStdRel;
			$at = $student->getNationality();
			$nat = $at ? $at->name : '';
			echo "<tr>
						<td>".++$count."</td>
						<td>{$student->std_regId}</td>
						<td>{$student->getFullName()}</td>
						<td>{$student->stdUserRel->gender}</td>
						<td>{$student->std_type}</td>
						<td>{$nat}</td>
						<td>{$optBtns}</td>
					</tr>";
		}

		echo "</tbody></table>";
		echo "<div class='container-fluid' id='point_history_container'></div>";
	}else{
		echo "<p class='bg-warning'>No student assigned in this group.</p>";
		return;
	}

}else{
	echo "<p class='bg-error'> No record.</p>";
	return;
}

?>

