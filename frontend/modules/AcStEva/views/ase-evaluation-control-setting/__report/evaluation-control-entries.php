<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(5456562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
	<?php

	if (isset($model)) {
		if ($model->status != $model::STATUS_CLOSED) {
			$btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id},{$model->criteria_bundle_relId});return false;'>Generate control entry</button>";
		} else {
			$btnGenerate = "Schedule Closed. >> ";
		}
		echo "{$btnGenerate}";
		$controlEntries = $model->aseEvaluationControlSettings;
		if ($controlEntries && count($controlEntries) > 0) {
			$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
			echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='54566562'>";
			echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Department Staff Evaluation schedules under <b><i>{$model->headerRel->evaluation_header}</i></b><br>
				Department : <b><i>{$model->deptRel->dept_name}</i></b>{$btnManage}
			</caption>";
			echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Dept.</th>
				<th>Eval. Type</th>
				<th>Staff to be evaluated</th>
				<th>Evaluators' Role</th>
				<th>Average Point</th>
				<th>Average %</th>
				<th>Course</th>
				<th>Option</th>
			</thead><tbody>";
			$count = 0;
			foreach ($controlEntries as $controlEntry) {
				echo "<tr>";
				$deptPlanModel = $controlEntry->deptPlanRel;
				$dept = $controlEntry->deptPlanRel->deptRel;
				$bundle = $controlEntry->deptPlanRel->criteriaBundleRel;
				// $input = "<input name='Update' type='button' class='btn btn-xs btn-warning' id='bun__{$deptSchedule->id}' data-code='{$bundle->code}' onclick='manageSchedule({$deptSchedule->id})'>";
				$perCourse = $bundle->evaluate_per_course ? "YES" : "NO";
				$criteriaCount = count(json_decode($bundle->criteria_IDS));
				$evalutorsCount = $controlEntry->evaluators ? count(json_decode($controlEntry->evaluators)) : 0;
				$evaluators = "<button class='btn btn-xs btn-link pull-right' onclick='showEvaluators({$controlEntry->id});return false;'>{$evalutorsCount}</button>";
				echo "
				<td>" . ++$count . "</td>
				<td>{$dept->dept_code}</td>
				<td>{$deptPlanModel->criteriaBundleRel->code}: {$deptPlanModel->criteriaBundleRel->evaluator_role} >> {$deptPlanModel->criteriaBundleRel->candidate_staff_role}</td>
				<td>{$controlEntry->candidateStaffRel->getFullName()}</td>
				<td>{$bundle->evaluator_role}{$evaluators}</td>
				<td style='text-align: center;'>{$bundle->average_point}</td>
				<td style='text-align: center;'>{$bundle->average_percentage}</td>
				<td style='text-align: center;'>{$perCourse}</td>
				<td style='text-align: center;'><a href='#' onclick='showEvaluationCriteriaDetail({$bundle->id})'>{$criteriaCount}</a></td>
				<td styele='text-align:center;'>-</td>
				";
				echo "</tr>";
			}
			echo "</tbody></table>";
		} else {
			if ($model->status != $model::STATUS_CLOSED) {
				$btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id},{$model->criteria_bundle_relId});return false;'>Generate control entry</button>";
			} else {
				$btnGenerate = "Schedule Closed.";
			}

			echo "No entry generated yet, {$btnGenerate}";
		}
	} else {
		echo "Invalid request";
		return;
	}


	?>
	<script type="text/javascript">
		window.showEvaluators = (controlId) => {
			displayEvaluators(controlId);
		}
	</script>
</div>