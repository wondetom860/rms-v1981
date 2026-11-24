<?php 
if ($model && count($model) > 0) {
	echo "<hr>";
	echo "<table class='table table-condensed table-sm table-hover table-bordered' style='font-size: 9pt;'>";
	foreach ($model as $keyStudent) {
		$btn = "<button class='btn  btn-xs btn-success pull-right' onclick='select({$keyStudent->id});return false;'>Select</button>";
		echo "<tr>
			<td>{$keyStudent->std_regId}</td>
			<td>{$keyStudent->getFullName()}</td>
			<td>{$btn}</td>
		</tr>";
	}
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No record found.</p>";
}

?>

