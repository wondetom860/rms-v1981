<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right hidden" onclick="toggleDisplay(4343);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
	<?php
	// var_dump($candidates);die();
	if (isset($model) && !is_null($model->evaluators)) {
		$candidates = \backend\models\User::find()->where(['id' => json_decode($model->evaluators)])->orderBy(['first_name' => SORT_ASC, 'middle_name' => SORT_ASC])->all();
		if ($candidates && count($candidates) > 0) {
			$deptPlanModel = $model->deptPlanRel;
			$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
			$evpc = $deptPlanModel->criteriaBundleRel->evaluate_per_course ? "Evaluation is made per course" : '';
			$course = $deptPlanModel->criteriaBundleRel->evaluate_per_course && $model->course_relId ? "<br>Course : {$model->courseRel->getCourseCredits()}" : '';
			echo "<table class='table table-condensed table-bordered table-hover' style='font-family:sans-serif; font-size:8pt;' id='4343'>";
			echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #685'>
				Eligible Evaluators of: {$model->candidateStaffRel->getFullName()}<br>
				Evaluation Type: {$deptPlanModel->criteriaBundleRel->code}: {$deptPlanModel->criteriaBundleRel->evaluator_role} >> {$deptPlanModel->criteriaBundleRel->candidate_staff_role}<br>
				{$evpc}{$course}
			</caption>";
			echo "<thead style='background-color: #D6D;'>
				<th>#</th>
				<th>Full Name</th>
				<th>Completed?</th>
				<th>Remark</th>
			</thead><tbody style='background-color: #ACE;'>";
			$count = 0;
			foreach ($candidates as $user) {
				$comltd = $model->checkIfEvaluted($user);
				echo "<tr>";
				// $input = "<input name='' type='checkbox' id='evalist__{$user->id}' data-code='{$user->getFullName()}' onclick='addToEvaluatorList({$user->id},{$deptPlanModel->id})'>";
				echo "
				<td>" . ++$count . "</td>
				<td>{$user->getFullName()}</td>
				<td>{$comltd[0]}/{$comltd[1]}</td>
				<td>-</td>
				";
				echo "</tr>";
			}
			echo "</tbody></table>";
		} else {
			// $btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id});return false;'>Generate control entry</button>";
			echo "No evaluator list found,";
		}
	} else {
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