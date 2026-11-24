<?php

/**
 * @var \frontend\modules\AcStEva\models\AseSemesterEvaluation $model
 * @var \backend\models\Instructor $inst
 */
if (isset($model)) {
	$bundle = $model->criteriaBundleRel;
	$employmentType = json_decode($bundle->employment_types);
	$criteriaCount = 0;

	$inst = \backend\models\Instructor::findOne(['inst_user_relId' => $model->controlSettingRel->candidate_staff_relId]);
	if (!$inst) {
		echo "Candidate staff information is incorrect.";
		return;
	}
	if (!$inst->employmentTypeRel) {
		if (Yii::$app->user->can('Admin') || Yii::$app->user->can('DepartmentHead')) {
			echo "Emplyment Type Uknown. <a href='/instructor/update?id={$inst->id}' target='_blank'>Update here</a>";
		} else {
			echo "<p class='bg-warning'>Candidate profile information incomplete, Please contact the corresponding Department Head.</p>";
		}
		return;
	}
	if (is_array($employmentType) && !in_array($inst->employment_type_relId, $employmentType)) {
		// $criteriaCount -= count(json_decode($bundle->criteria_IDS));
		echo "This Evaluation type is NOT applicable for {$inst->employmentTypeRel->et_name} employees. Remaining : {$criteriaCount}";
		return;
	}
	// 
	$evalCriteriaIds = json_decode($bundle->criteria_IDS);
	$criteria = \frontend\modules\AcStEva\models\AseCriteriaDescription::find()
		->where([
			'id' => $evalCriteriaIds
		])
		->indexBy('category_relId')
		->all();
	if ($criteria) {
		echo "<table class='table table-condensed table-bordered table-hover' style='font-family:normal; font-size:10pt;' id='45455554'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Evalution criteria
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<tr>
					<th rowspan=2>#</th>
					<th rowspan=2>Code</th>
					<th rowspan=2>Criteria Description</th>
					<th colspan=6 style='text-align: center;'>Rate</th>
				</tr>
				<tr style='font-size: 8pt;'>
					<th style='text-align: center;'  title='Rate 1'>1</th>
					<th style='text-align: center;'  title='Rate 2'>2</th>
					<th style='text-align: center;'  title='Rate 3'>3</th>
					<th style='text-align: center;'  title='Rate 4'>4</th>
					<th style='text-align: center;'  title='Rate 5'>5</th>
					<th  style='text-align: center;' title='Not Applicable'>NA</th>
				</tr>
			</thead><tbody>";
		$count = 0;
		foreach ($criteria as $categoryid => $criteriaDescription) {
			$category = \frontend\modules\AcStEva\models\AseCriteriaCategory::findOne($categoryid);
			if (in_array($inst->instUserRel->user_type, json_decode($category->profess_list))) {
				echo "<tr style='background-color: #ACE; font-weight: bold;'>
						<td colspan=9><b  style='left: 30px;'>{$category->category_name}</b></td>
					</tr>";
				foreach ($category->aseCriteriaDescriptions as $point) {
					if (!in_array($point->id, $evalCriteriaIds)) {
						continue;
					}
					$ratings = "
							<td class='{$point->id}' id='{$point->id}_1' title='Rate 1' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_1' class='{$point->id}_radio' name='ratings[{$point->id}_1]' onchange='rateScale(1,{$point->id});return false;'>
							</td>
							<td class='{$point->id}' id='{$point->id}_2' title='Rate 2' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_2' class='{$point->id}_radio' name='ratings[{$point->id}_2]' onchange='rateScale(2,{$point->id});return false;'>
							</td>
							<td class='{$point->id}' id='{$point->id}_3' title='Rate 3' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_3' class='{$point->id}_radio' name='ratings[{$point->id}_3]' onchange='rateScale(3,{$point->id});return false;'>
							</td>
							<td class='{$point->id}' id='{$point->id}_4' title='Rate 4' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_4' class='{$point->id}_radio' name='ratings[{$point->id}_4]' onchange='rateScale(4,{$point->id});return false;'>
							</td>
							<td class='{$point->id}' id='{$point->id}_5' title='Rate 5' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_5' class='{$point->id}_radio' name='ratings[{$point->id}_5]' onchange='rateScale(5,{$point->id});return false;'>
							</td>
							<td class='{$point->id}' id='{$point->id}_0' title='Not Applicable' style='cursor:pointer;'>
								<input type='radio' id='_{$point->id}_0' class='{$point->id}_radio' name='ratings[{$point->id}_0]' onchange='rateScale(0,{$point->id});return false;'>
							</td>
						";
					echo "<tr>";
					echo "
						<td>" . ++$count . "</td>
						<td style='white-space: nowrap;'>{$point->code}</td>
						<td>{$point->description}</td>
						{$ratings}
						";
					echo "</tr>";
					$criteriaCount++;
				}
			} else {
				// $criteriaCount -= count($category->aseCriteriaDescriptions);
				echo "<tr style='background-color: #F12; font-style: italic;'>
						<td colspan=9>{$category->category_name} is not applicable for {$inst->instUserRel->user_type} Employees as per setting.</td>
					</tr>";
			}
		}
		echo "</tbody></table><br>
			<small class='bg-info pull-left'>Evaluated <span id='kgkdhjg'></span>/<span id='hjdhgdjk'>{$criteriaCount}</span></small>
		";
	}
} else {
	echo "Invalid request format.";
	return;
}
