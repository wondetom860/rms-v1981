<?php 

if (isset($model) && !empty($model)) {
	echo "<table class='table table-sm table-condensed table-bordered table-hover'>";
	echo "<caption style='color:#000; text-align:center; font-weight:bold'>Department Selection Setting Entries</caption>";
	echo "<thead style='background-color:#a2d246;'>
			<th>#</th>
			<th>Entry</th>
			<th>Option</th>
		</thead><tbody>";
		$count = 0;
	foreach ($model as $keyModel) {
		$btn = "<button class='btn btn-xs btn-success' onclick='loadSelectionEntry({$keyModel->setting_relId});return false;'>Show</button>";
		echo "<tr>
				<td>".++$count."</td>
				<td>{$keyModel->getSettingENtry()}</td>
				<td>{$btn}</td>
			</tr>";
		// echo "{$keyModel->getStudentFullName()} == {} == {$keyModel->getDeptSelection()} <hr>";
	}

	echo "</tbody></table>";
}else{
	echo "<p class='bg-danger'>No Record Available</p>";
}


?>