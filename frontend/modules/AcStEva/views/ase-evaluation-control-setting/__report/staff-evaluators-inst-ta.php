<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right hidden" onclick="toggleDisplay(4343);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 
// var_dump($candidates);die();
if (isset($candidates)) {
	if ($candidates && count($candidates) > 0) {
		$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
		$evpc = $deptPlanModel->criteriaBundleRel->evaluate_per_course ? "Evaluation is made per course" : '';
		$btnAll = "<input class='pull-right' title='Select All' type='checkbox' id='evalist__ALL' data-code='All' onChange='addAllToEvaluatorList({$deptPlanModel->id},{$facultyModel->instRel->inst_user_relId},{$facultyModel->id})'>";
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='4343'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #685'>
				Eligible Evaluators of: {$facultyModel->instRel->getFullName()}<br>
				Evaluation Type: {$deptPlanModel->criteriaBundleRel->code}: {$deptPlanModel->criteriaBundleRel->evaluator_role} >> {$deptPlanModel->criteriaBundleRel->candidate_staff_role}<br>
				{$evpc}
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Dept.</th>
				<th>Staff Name</th>
				<th>Academic Year</th>
				<th>Course</th>
				<th>Section/Group</th>
				<th>Assigned as</th>
				<th>Option{$btnAll}</th>
			</thead><tbody>";
		$count = 0;
		foreach ($candidates as $facultyAssignment) {
			echo "<tr>";
			$inst = $facultyAssignment->instRel;
			$coff2det = $facultyAssignment->coff2detRel;
			$dept = $coff2det->coff2detCoff2Rel->coff2GroupRel->gsDeptRel;
			$input = "<input class='evaluators_{$facultyModel->instRel->inst_user_relId}_{$facultyModel->id}' type='checkbox' id='evalist__{$inst->inst_user_relId}_{$deptPlanModel->id}_{$facultyModel->instRel->inst_user_relId}_{$facultyModel->id}' data-code='{$inst->getFullName()}' onChange='addToEvaluatorList({$inst->inst_user_relId},{$deptPlanModel->id},{$facultyModel->instRel->inst_user_relId},{$facultyModel->id})'>";
			// $perCourse = $bundle->evaluate_per_course?"YES":"NO";
			// $criteriaCount = count(json_decode($bundle->criteria_IDS));
			echo "
				<td>".++$count."</td>
				<td>{$inst->instDepRel->dept_code}</td>
				<td>{$inst->getFullName()}</td>
				<td>{$coff2det->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}-{$coff2det->coff2detCoff2Rel->coff2SemRel->semester_name}</td>
				<td>{$coff2det->coff2detCourseRel->getCourseCredits()}</td>
				<td>{$coff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}</td>
				<td>{$facultyAssignment->assign_as}</td>
				<td>{$input}</td>
				";
			echo "</tr>";
		}
		$btnSubmit = "<button class='btn btn-primary btn-xs pull-right' onclick='CreateControlSetting();return false;'>Save</button>";
		echo "<tr style='background-color: #FDD;'>
				<td colspan='8' style='background-color: #FDD; text-align: center;'>Selected: <span id='elas_count_{$facultyModel->instRel->inst_user_relId}_{$deptPlanModel->id}_{$facultyModel->id}' style='font-style:italic; font-size: 7pt;'>-</span>{$btnSubmit}</td>
			</tr>";
		echo "</tbody></table>";
	}else{
		// $btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id});return false;'>Generate control entry</button>";
		echo "No Candidate staff enlisted here,";
	}
}else{
	echo "Invalid request";
	return;
}

?>
</div>
<script type="text/javascript">

</script>