<?php 
$modelCAS = new \backend\models\ClearanceAllowedStudent();
if (isset($model)) {
	if ($model && count($model) > 0) {
		echo "<table class='table table-striped table-condensed table-bordered' style='font-size:8pt;'>
			<thead style='background-color:#8DF;'>
				<th>#</th>
				<th>Full Name</th>
				<th>Academic Year</th>
				<th>Program</th>
				<th>Department</th>
				<th>Specialization</th>
				<th>Class Year</th>
				<th>Option</th>
			<thead>
			<tbody>";
		$count = 0;
		foreach ($model as $gStudent) {
			if ($modelCAS->requestedForThis($gStudent)) {
				continue;
			}
			$user = $gStudent->semregStdRel->stdUserRel;
			$fullName =  ucfirst(strtolower($user->first_name)) .' '. ucfirst(strtolower($user->middle_name)) .' '. ucfirst(strtolower($user->last_name));
			$acadYear = $gStudent->semregAcadYearRel->sch_year_desc .'-'. $gStudent->semregSemesterRel->semester_name;
			$clslvl = $gStudent->semregClslvlRel->clslvl_num;
			$prog = $gStudent->semregStdRel->stdAdmissionRel->admission_name .' '. $gStudent->semregStdRel->stdProgRel->program_name;
			$btn = "<button class='btn btn-xs btn-primary' onclick='addStudent({$gStudent->id});return false;'>Allow Clerance</button>";
			$gStudent = $gStudent->semregStdRel;
			$spec = is_null($gStudent->std_spec_relId) ? '':$gStudent->stdSpecRel->spec_name;
			$dept = $gStudent->stdDeptRel->dept_code;

			echo "<tr>
					<td>". ++$count ."</td>
					<td>{$fullName}</td>
					<td>{$acadYear}</td>
					<td>{$prog}</td>
					<td>{$dept}</td>
					<td>{$spec}</td>
					<td>{$clslvl}</td>
					<td>{$btn}</td>
				</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No Record";
	}
}

?>