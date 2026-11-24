
<?php
$records = $model;

if (isset($records) && count($records) > 0) {
	?>

	<table class="table table-condensed table-bordered" cellpadding="3" cellspacing="0">
		<caption style="color: #000; font-weight: bold; text-align: center;">
		College Name : <u style="font-weight: normal;">College of Engineering</u><br>
		Academic Year : <u style="font-weight: normal;"><?= date_format(date_create($records[0]->gstd_date_of_graduation),'Y') ?></u>
		Department : <u style="font-weight: normal;"><?= isset($records[0]->gstdDeptRel)?$records[0]->gstdDeptRel->dept_name :'' ?></u>
		Program : <u style="font-weight: normal;"><?= isset($records[0]->gstdProgRel)?$records[0]->gstdProgRel->program_name:'' ?></u>
		</caption>
		<thead">
			<th>#</th>
			<th>Full Name</th>
			<th>Reg. ID</th>
			<th>Rank</th>
			<th>Age</th>
			<th>Gender</th>
			<th>Prof. Category</th>
			<th>Citizenship</th>
			<th>Prev. CGPA</th>
			<th>Enterance Exam.</th>
		</thead>
		<tbody>
			<?php
			$count = 0;

			foreach ($records as $key) {
				$user = $key->gstdUserRel;
				$full_name = $user->first_name .' '. $user->middle_name .' '. $user->last_name;
				$regId = isset($key->gstd_regId)?$key->gstd_regId:'-';
				$rank = isset($key->gstd_rank)?$key->gstd_rank:'-';
				$profCat = isset($key->gstd_profession_category)?$key->gstd_profession_category:'-';
				$age = isset($user->dob)?date_diff(date_create($user->dob), date_create($key->gstd_date_of_graduation))->y % 100:'-';
				$nat = (isset($user->nationality) && $user->nationality != 0) ? $user->nationalityRel->name:'-';
				$prevCGPA = (isset($key->gstd_prev_CGPA)) ? $key->gstd_prev_CGPA:'-';
				$EntrExam = (isset($key->gstd_enterance_exam)) ? $key->gstd_enterance_exam:'-';
				echo "<tr>
						<td>". ++$count ."</td>
						<td>{$full_name}</td>
						<td>{$regId}</td>
						<td>{$rank}</td>
						<td>{$age}</td>
						<td>{$user->gender}</td>
						<td>{$profCat}</td>
						<td>{$nat}</td>
						<td>{$prevCGPA}</td>
						<td>{$EntrExam}</td>
					</tr>";

			}
			// echo "<tr style='font-weight:bold;'>
			// 	<td style='text-align:center; font-weight:bold;'>Total</td>
			// 	<td style='text-align:center;'>{$malecount}</td>
			// 	<td style='text-align:center;'>{$femalecount}</td>
			// 	<td style='text-align:center;'>{$militarycount}</td>
			// 	<td style='text-align:center;'>{$civilcount}</td>
			// 	<td style='text-align:center;'>{$expatriatcount}</td>
			// 	<td style='text-align:center;'>{$totalcount}</td>
			// </tr>";

			?>
		</tbody>
	</table>
	<?php
}

?>