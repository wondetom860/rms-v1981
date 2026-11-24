<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(78898);return false;" title="Show/Hide employment types under this category">[=]</button>
<?php 

if (isset($model) && !empty($model)) {
	// $btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='78898'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Select Employment Type(s) to selected evaluation type : <i>{$model->evalTypeRel->type_name}</i>
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Employment Type</th>
			<th>Remark</th>
			<th>Select</th>
		</thead><tbody>";
		$count = 0;
	$EmpType = \backend\models\EmploymentType::find()->all();
	foreach ($EmpType as $eType) {
		// if ($model->criteria_IDS && in_array($eType->id,json_decode($model->criteria_IDS))) {
		// 	continue;
		// }
		echo "<tr>";
		$input = "<input name='' type='checkBox' id='eat_{$eType->id}' data-type='{$eType->et_name}' onchange='addToEmpTypeList({$eType->id})'>";
		echo "
			<td>".++$count."</td>
			<td style='word-break: keep-all'>{$eType->et_name}</td>
			<td></td>
			<td style='text-align: center;'>{$input}</td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "Evaluation criteria not found under this category.";
	return;
}
?>
</div>
<script type="text/javascript">
	
</script>