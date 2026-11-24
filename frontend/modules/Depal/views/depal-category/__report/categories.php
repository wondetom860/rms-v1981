<?php 
// better look for only active academic departments.
// $depts = \backend\models\Department::find()->all();
$categories = frontend\modules\Depal\models\DepalCategory::find()->all();
if ($categories && count($categories) > 0) {
	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
	echo "<caption style='font-weight:normal; color: #000; text-align:center;'>
				Criteria Categories:
			</caption>";
	echo " <thead>";
	echo "<th>#</th>
		<th>Category</th>
		<th>Comparision Type</th>
		<th>Interval</th>";
	echo " </thead><tbody>";
	$count = 0;
	foreach ($categories as $category) {
		echo "<tr>";
		$interval = $category->comparison_type == 'POINT_INTERVAL' ? sprintf("%0.2f",$category->lower_bound)."-".sprintf("%0.2f",$category->upper_bound):"-";
		// $input = "<input name='' type='checkBox' id='cat_{$category->id}' onchange='addToCategoryList({$category->id})'>";
		echo "<td>".++$count."</td>
			<td>{$category->category_name}</td>
			<td>{$category->comparison_type}</td>
			<td>{$interval}</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
}

?>