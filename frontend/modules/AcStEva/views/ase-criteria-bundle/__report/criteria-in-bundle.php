<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(7845895659)" title="Show/Hide evaluation criterai under this category">[=]</button>
	<?php

	if (isset($model) && !empty($model)) {
		// $btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='7845895659'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Applicable Evaluation criteria for : <i>{$model->evalTypeRel->type_name}-({$model->code})</i>
		</caption>";
		echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Code</th>
			<th>Evaluation Criteria</th>
			<th colspan='6' style='text-align:center;'>Ratings</th>
			<th>Remark</th>
		</thead><tbody>";
		$count = 0;

		if (is_null($model->criteria_IDS)) {
			$criteria = null;
		} else {
			$existingCriteria = json_decode($model->criteria_IDS);
			$criteria = \frontend\modules\AcStEva\models\AseCriteriaDescription::find()->where(['id' => $existingCriteria])->all();
		}

		foreach ($criteria as $point) {
			echo "<tr>";
			// $input = "<input name='' type='checkBox' id='cat__{$point->id}' data-code='{$point->code}' onchange='addToCriteriaList({$point->id})'>";
			echo "
			<td>" . ++$count . "</td>
			<td style='white-space: nowrap'>{$point->code}</td>
			<td>{$point->description}</td>
			<td style='text-align: center;'>1</td>
			<td style='text-align: center;'>2</td>
			<td style='text-align: center;'>3</td>
			<td style='text-align: center;'>4</td>
			<td style='text-align: center;'>5</td>
			<td style='text-align: center;'>NA</td>
			<td style='text-align: center;'></td>
			";
			echo "</tr>";
		}
		echo "</tbody></table>";
	} else {
		echo "Evaluation criteria not found under this category.";
		return;
	}
	?>
</div>

<script type="text/javascript">

</script>