<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(784589565)" title="Show/Hide employment types under this category">[=]</button>
<?php 

if (isset($model) && !empty($model)) {
	if (is_null($model->employment_types)) {
		echo " No Employment type categorized for this evaluation type.";
		return;
	}else{
		$existingEmpTypes = json_decode($model->employment_types);
		$empType = \backend\models\EmploymentType::find()->where(['id'=>$existingEmpTypes])->all();
	}
	// $btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='784589565'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Target Employment Type by this category: <i>{$model->evalTypeRel->type_name}-({$model->code})</i>
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Employment Type</th>
			<th>Remark</th>
		</thead><tbody>";
		$count = 0;
	
	if (is_null($model->employment_types)) {
		$empType = null;
	}else{
		$existingEmpTypes = json_decode($model->employment_types);
		$empType = \backend\models\EmploymentType::find()->where(['id'=>$existingEmpTypes])->all();
	}
	
	foreach ($empType as $eType) {
		echo "<tr>";
		// $input = "<input name='' type='checkBox' id='cat__{$eType->id}' data-code='{$eType->code}' onchange='addToCriteriaList({$eType->id})'>";
		echo "
			<td>".++$count."</td>
			<td>{$eType->et_name}</td>
			<td></td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "Employment Type not found for this category.";
	return;
}
?>
</div>

<script type="text/javascript">
	
</script>