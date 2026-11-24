<?php 
// better look for only active academic departments.
// $depts = \backend\models\Department::find()->all();
$categories = frontend\modules\Depal\models\DepalCategory::find()->all();
if ($categories && count($categories) > 0) {
	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
	echo " <thead>";
	echo "<th>#</th>
		<th>Category Name</th>
		<th>Comparision Type</th>
		<th>Select</th>";
	echo " </thead><tbody>";
	$count = 0;
	foreach ($categories as $category) {
		echo "<tr>";
		$input = "<input name='' type='checkBox' id='cat_{$category->id}' onchange='addToCategoryList({$category->id})'>";
		echo "<td>".++$count."</td>
			<td>{$category->category_name}</td>
			<td>{$category->comparison_type}</td>
			<td style='text-align:center;'>{$input}</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
}

?>