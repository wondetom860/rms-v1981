<?php 
//$model
if (isset($model)) {
	$courses = $model->removedCourses;
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;' id='54566562'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			<h4>Student Withdrawal Request Detail</h4>
			Student Name: <b><u>{$model->swhStdRel->getFullName()} ({$model->swhStdRel->std_regId})</u></b><br>
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Academic Year</th>
			<th>Withdrawal reason</th>
			<th>Last Attended Section</th>
			<th>HOD Approval</th>
			<th>Registrar Approval</th>
			<th>Removed registration Entry</th>
			<th>Remark</th>
		</thead><tbody>";
	if (!$model->section_relId) { $model->getSetStudentSection(); }
	// var_dump($model->section_relId);exit();
	$apr = $model->swh_lastApproved ? "Approved" : "Pending...";
    $apr .= $model->swhLastApprovedBy?' by '.$model->swhLastApprovedBy->getFullName_av() : '';
    $apr .= $model->swh_lastApproved_at?' on '.date_format(date_create($model->swh_lastApproved_at),'d/m/Y') : '';
    $aprHod = $model->swh_HOD_approved ? $model->swhHODApprovedBy->getFullName_av().", on ".date_format(date_create($model->swh_HOD_approved_at),"d/m/Y") : "Pending...";
    $c = count($courses);
	$section = $model->sectionRel ? $model->sectionRel->gasGsetRel->getGsDisp() : '-';
	echo "
		<td>1</td>
		<td>{$model->swhAcadYearRel->sch_year_desc}-{$model->swhSemRel->semester_name}</td>
		<td>{$model->swhReasonRel->withdrawalres_description}</td>
		<td>{$section}</td>
		<td>{$aprHod}</td>
		<td>{$apr}</td>
		<td><a href='#' onclick='showRemovedCourses({$model->id})'>{$c} Course(s)</a></td>
		<td>-</td>
		";
	echo "</tr>";
	echo "</tbody></table>";
}else{
	echo "Invalid request";
	return;
}


 ?>