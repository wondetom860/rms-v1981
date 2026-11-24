<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(4545555);return false;" title="Show/Hide evaluation criterai under this category">+</button>
<?php 

if (isset($model) && !empty($model)) {
	// $btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='4545555'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Select Evaluation criteria to selected evaluation type : <i>{$model->evalTypeRel->type_name}</i>
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Code</th>
			<th>Criteria Description</th>
			<th>Scale</th>
			<th>Remark</th>
			<th>Select</th>
		</thead><tbody>";
		$count = 0;
	$criteria = \frontend\modules\AcStEva\models\AseCriteriaDescription::find()->all();
	foreach ($criteria as $point) {
		// if ($model->criteria_IDS && in_array($point->id,json_decode($model->criteria_IDS))) {
		// 	continue;
		// }
		echo "<tr>";
		$input = "<input name='' type='checkBox' id='cat__{$point->id}' data-code='{$point->code}' onchange='addToCriteriaList({$point->id})'>";
		echo "
			<td>".++$count."</td>
			<td style='word-break: keep-all'>{$point->code}</td>
			<td>{$point->description}</td>
			<td style='text-align: center;'>{$point->max_rating_scale}</td>
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