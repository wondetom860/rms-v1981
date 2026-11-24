<?php 
// better look for only active academic departments.
$depts = \backend\models\Department::find()->where(['status' => 1])->all();
// $activePrograms = frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->where('status = 1')->groupBy('dept_relId')->all();
if ($depts && count($depts) > 0) {
	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
	echo " <thead>";
	echo "<th>#</th>
		<th>Code</th>
		<th>Name</th>
		<th>Select</th>";
	echo " </thead><tbody>";
	$count = 0;
	foreach ($depts as $dept) {
		echo "<tr>";
		$input = "<input name='' type='checkBox' id='dept_{$dept->id}' onchange='addToDeptList({$dept->id})'>";
		echo "<td>".++$count."</td>
			<td>{$dept->dept_code}</td>
			<td>{$dept->dept_name}</td>
			<td style='text-align:center;'>{$input}</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
}

?>