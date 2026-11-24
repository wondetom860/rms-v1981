<?php

if (isset($model) && !empty($model)) {
	$gsets = $model->getAvtiveSections();
	$group = $model->srhSwhRel->sectionRel ? "for ".$model->srhSwhRel->sectionRel->gasGsetRel->getGsDisp() : "";
	if (!is_null($gsets)) {
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				<h4>List of Group Setups {$group}</h4>
				Student Name: {$model->srhStdRel->getFullName()}<br>
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Section</th>
				<th>Department</th>
				<th>Group/Students</th>
				<th>Option</th>
			</thead><tbody>";
		$count = 0;
		$gas = $model->getJoinedSection();
		foreach ($gsets as $gset) {
			$cc = count($gset->wGroupAssignments);
			$assign = $gas ? "-":"<button class='btn btn-xs btn-warning' onclick='assignStudent({$gset->id},{$model->srh_std_relId});return false;'>Assign</button>";
			echo "
				<td>".++$count."</td>
				<td>{$gset->getGsDisp()}</td>
				<td>{$gset->gsDeptRel->dept_name}</td>
				<td>{$gset->gsGroupRel->group_name}/({$cc} Student(s))</td>
				<td>{$assign}</td>
				";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No section setup found.";
		return;
	}
}else{
	echo "Invalid request.";
	return;
}

?>