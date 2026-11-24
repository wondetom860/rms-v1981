<?php 

if ($model && count($model) > 0) {
	echo "<table class='table table-sm table-bordered table-condensed table-hover' style='font-size:9pt;'>";

	echo "<thead>
			<th>#</th>
			<th>Id No.</th>
			<th>Full Name</th>
		</thead><tbody>";
	$count = 0;	
	foreach ($model as $person) {
		$ID = !is_null($person->MIL_ID_NO) ? $person->MIL_ID_NO : $person->ID_NO;
		echo "<tr style='cursor:pointer' onclick='selectPerson({$person->id})'>
				<td>".++$count."</td>
				<td>{$ID}</td>
				<td>{$person->getFullName()}</td>
			</tr>";
	}

	echo "</tbody></table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}


?>