<?php 
echo Yii::$app->user->can('Admin') ? "<button class='btn btn-xs btn-secondary pull-right' onclick='addSection()' title='Add new section under this program'>Add Section(s)</button><hr>":'';
if (isset($gsModels) && !empty($gsModels)) {
	echo "<table class='table table-condensed table-striped table-bordered table-hover' style='font-size:9pt;'>";
	echo "<thead>
			<th>#</th>
			<th>Group</th>
			<th>Students</th>
			<th>Option</th>
		</thead>";
	$count = 0;
	foreach ($gsModels as $gs) {
		$button_add = Yii::$app->user->can('Admin') ? "<a class='btn btn-xs btn-primary' title='Assign students to this section' onclick='groupStudents({$gs->id})'>Add</a>" : '';
		$button_list = "<button class='btn btn-xs btn-info' title='List students in this section/group' onclick='listStudents({$gs->id})'>List</button>";
		$stdCount = $gs->getWGroupAssignments()->count();
		echo "<tr>
				<td>".++$count."</td>
				<td>{$gs->getGsDisp()}({$gs->gsGroupRel->group_name})</td>
				<td style='text-align: center;'>{$stdCount}</td>
				<td>[{$button_add}&nbsp;|&nbsp;{$button_list}]</td>
			</tr>";
	}
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No Group/Section made.</p>";
}
?>

<script type="text/javascript">
	window.listStudents = (gsetId) => {

		window.open('/w-group-setup/get-assignments2?wt_gsetUp_entry='+gsetId,'title:Student List','width:640,height:600');
		//get-assignments?wt_gsetUp_entry=
	}
</script>