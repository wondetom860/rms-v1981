<?php 

if (isset($settingModel) && !is_null($settingModel->depts_to_choose_from)) {
	$deptsToChooseFrom = json_decode($settingModel->depts_to_choose_from);
	$departments = \backend\models\Department::find()->where(['id' => $deptsToChooseFrom])->all();
}else{
	$departments = \backend\models\Department::find()->all();
}


if ($departments && count($departments) > 0) {
	echo "<table class='table table-striped table-bordered table-sm table-condensed'>";
	echo "<thead style='background-color:#a2d246;'>
			<th>#</th>
			<th>Department Name</th>
			<th>Option</th>
		</thead><tbody>";
	$count = 0;
	foreach ($departments as $keyDept) {
		$opt = "<button class='btn btn-primary btn-xs pull-right' onclick='addToSelected({$keyDept->id},1);return false;'>Add</button>";
		echo "<tr id='{$keyDept->id}_tr'>
				<td>".++$count."</td>
				<td>{$keyDept->dept_name}</td>
				<td>{$opt}</td>
			</tr>";
	}

	echo "</tbody></table>";
}


?>