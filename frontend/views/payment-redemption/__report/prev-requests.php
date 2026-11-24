<?php 

if ($models && count($models) > 0) {
	echo "<table class='table table-condensed table-sm table-bordered'>";
	echo "<thead>
			<th>#</th>
			<th>Academic Year</th>
			<th>Reason</th>
			<th>Approved</th>
			<th>Used</th>
		</thead><tbody>";
		$count = 0;
	foreach ($models as $model) {
		$app = $model->approvedBy? $model->approvedBy->getFullName() : 'Not Approved.';
		$used = $model->used? 'Used' : 'Not Used Yet';
		echo "<tr>
				<td>".++$count."</td>
				<td>{$model->acadYearRel->sch_year_desc}-{$model->semRel->semester_name}</td>
				<td>{$model->reason}</td>
				<td>{$app}</td>
				<td>{$used}</td>
			</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "<p>No previous request found.</p>";
	return;
}

?>