<?php 


?><div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(454555543);return false;" title="Show/Hide evaluation criterai under this category">+</button>
<?php 

if (isset($model) && !empty($model)) {
	// $btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
	// $btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage();return false;'>Manage</button>";
	if (is_null($model->profess_list)) {
		echo "Employee profession list empty for this category.";
		return;
	}
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='454555543'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Employee Profession Types under <b><i>{$model->category_name}</i></b>
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Profession</th>
			<th>Option</th>
		</thead><tbody>";
		$count = 0;
	foreach (json_decode($model->profess_list) as $point) {
		echo "<tr>";
		echo "
			<td>".++$count."</td>
			<td>{$point}</td>
			<td></td>
			<td></td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "Employee profession list NOT found under this category.";
	return;
}
?>
</div>
<script type="text/javascript">
	
</script>