<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right hidden" onclick="toggleDisplay(4343);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 
// var_dump($candidates);die();
if (isset($candidates)) {
	if ($candidates && count($candidates) > 0) {
		$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
		$evpc = $deptPlanModel->criteriaBundleRel->evaluate_per_course ? "Evaluation is made per course" : '';
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='4343'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #685'>
				Eligible Evaluators of: {$facultyModel->instRel->getFullName()}<br>
				Evaluation Type: {$deptPlanModel->criteriaBundleRel->code}: {$deptPlanModel->criteriaBundleRel->evaluator_role} >> {$deptPlanModel->criteriaBundleRel->candidate_staff_role}<br>
				{$evpc}
			</caption>";
		echo "<thead style='background-color: #D6D;'>
				<th>#</th>
				<th>Full Name</th>
				<th>Option</th>
			</thead><tbody style='background-color: #ACE;'>";
		$count = 0;
		foreach ($candidates as $user) {
			echo "<tr>";
			// $input = "<input name='' type='checkbox' id='evalist__{$user->id}' data-code='{$user->getFullName()}' onclick='addToEvaluatorList({$user->id},{$deptPlanModel->id})'>";
			$input = "<input class='evaluators_{$facultyModel->instRel->inst_user_relId}_{$facultyModel->id}' type='checkbox' id='evalist__{$user->id}_{$deptPlanModel->id}_{$facultyModel->instRel->inst_user_relId}_{$facultyModel->id}' data-code='{$user->getFullName()}' onChange='addToEvaluatorList({$user->id},{$deptPlanModel->id},{$facultyModel->instRel->inst_user_relId},{$facultyModel->id})'>";
			echo "
				<td>".++$count."</td>
				<td>{$user->getFullName()}</td>
				<td>{$input}</td>
				";
			echo "</tr>";
		}
		$btnSubmit = "<button class='btn btn-primary btn-xs pull-right' onclick='CreateControlSetting();return false;'>Save</button>";
		echo "<tr style='background-color: #FDD;'>
				<td colspan='7' style='background-color: #FDD; text-align: center;'>Selected: <span id='elas_count_{$facultyModel->instRel->inst_user_relId}_{$deptPlanModel->id}_{$facultyModel->id}' style='font-style:italic; font-size: 7pt;'>-</span>{$btnSubmit}</td>
			</tr>";
		echo "</tbody></table>";
	}else{
		// $btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id});return false;'>Generate control entry</button>";
		echo "No evaluator list found,";
	}
}else{
	echo "Invalid request";
	return;
}

?>
<script type="text/javascript">

</script>
</div>
<!-- 
<div class="" style="font-family: sans-serif;">
	
</div> -->