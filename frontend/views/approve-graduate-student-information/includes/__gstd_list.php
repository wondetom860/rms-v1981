<?php 
	
if (isset($model)) {
	if ($model && count($model) > 0) {
		echo "<table class='table table-striped table-condensed table-bordered'>
			<thead style='background-color:#8DF;'>
				<th>#</th>
				<th>Full Name</th>
				<th>Year of Admission</th>
				<th>Previous CGPA</th>
				<th>Enterance Exam</th>
				<th>Sponsored</th>
				<th>Program</th>
				<th>Department</th>
				<th>Specialization</th>
				<th>CGPA</th>
				<th>Year of Graduation</th>
				<th>Points</th>
				<th>Approve</th>
			<thead>
			<tbody>";
		$count = 0;
		foreach ($model as $gStudent) {
			$user = $gStudent->gstdUserRel;
			$fullName = $user->first_name.' '. $user->middle_name .' '. $user->last_name;
			$spon = $gStudent->gstdSponsorshipRel->schType_name;
			$prog = $gStudent->gstdAdmRel->admission_name .' '. $gStudent->gstdProgRel->program_name;
			$spec = is_null($gStudent->gstd_spec_relId) ? '':$gStudent->gstdSpecRel->spec_name;
			$gYear = date_format(date_create($gStudent->gstd_date_of_graduation),'Y'). ' ('. $gStudent->gstd_grad_year.')';

			$btn = "<button class='btn btn-xs btn-primary' onclick='approveRecord({$gStudent->id});return false;'>Approve</button>";
			$btn2 = "<button class='btn btn-xs btn-secondary' onclick='pointHistory({$gStudent->id});return false;'>Points</button>";
			echo "<tr>
					<td>". ++$count ."</td>
					<td>{$fullName}</td>
					<td>{$gStudent->gstd_adm_year}</td>
					<td>{$gStudent->gstd_prev_CGPA}</td>
					<td>{$gStudent->gstd_enterance_exam}</td>
					<td>{$spon}</td>
					<td>{$prog}</td>
					<td>{$gStudent->gstdDeptRel->dept_code}</td>
					<td>{$spec}</td>
					<td>{$gStudent->gstd_CGPA}</td>
					<td>{$gYear}</td>
					<td>{$btn2}</td>
					<td>{$btn}</td>
				</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No Record";
	}
}

?>