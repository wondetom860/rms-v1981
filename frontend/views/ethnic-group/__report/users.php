<?php 

if (isset($model)) {
	$users = $model->users;

	if ($users && count($users) > 0) {
		echo "<table class='table table-condensed table-striped table-bordered' style='font-size: 10pt;'>
			<thead>
				<th>#</th>
				<th>Full Name</th>
				<th>Gender</th>
			</thead>
			<tbody>";
		$count = 0;
		foreach ($users as $user) {
			echo "<tr>
					<td>".++$count."</td>
					<td>{$user->getFullName()}</td>
					<td>{$user->gender}</td>
				</tr>";
		}
		echo "</tbody>
		</table>";
	}
}else{
	echo "No record";
	return;
}

?>