<?php 

$departments = \backend\models\Department::find()->all();

if ($departments && count($departments) > 0) {
	echo "<table class='table table-striped table-bordered table-sm table-condensed hidden' id='dept_list_table_hidden'>";
	echo "<caption><small>Carefully check your choice order.</small><caption>";
	echo "<thead style='background-color:#a2d246;'>
			<th>Choice Order</th>
			<th>Department Name</th>
			<th>Code</th>
			<th>Option</th>
		</thead><tbody>";
	$count = 0;
	foreach ($departments as $keyDept) {
		$opt = "<button class='btn btn-danger btn-xs pull-right' onclick='addToSelected({$keyDept->id},0);return false;'>Cancel</button>";
		echo "<tr id='{$keyDept->id}_tr_hidden' class='tr_hidden hidden'>
				<td id='{$keyDept->id}_order_count' style='font-weight:bold; text-align:center;'></td>
				<td>{$keyDept->dept_name}</td>
				<td>{$keyDept->dept_code}</td>
				<td>{$opt}</td>
			</tr>";
	}

	echo "</tbody></table>";
}


?>