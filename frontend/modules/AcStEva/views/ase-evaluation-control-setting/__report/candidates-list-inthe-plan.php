<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(5456562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
	<?php
	// var_dump($candidates);die();
	// $candidates = $deptPlanModel->aseEvaluationControlSettings;
	if (isset($candidates)) {
		if ($candidates && count($candidates) > 0) {
			$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
			$evpc = $deptPlanModel->criteriaBundleRel->evaluate_per_course ? "Evaluation is made per course" : '';
			echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='54566562'>";
			echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Candidate Staff to be evaluated by the selected schedule: {$deptPlanModel->criteriaBundleRel->code}: {$deptPlanModel->criteriaBundleRel->evaluator_role} >> {$deptPlanModel->criteriaBundleRel->candidate_staff_role}<br>
				{$evpc}
			</caption>";
			echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Dept.</th>
				<th>Staff to be evaluated</th>
				<th>Course</th>
				<th>Section/Group</th>
				<th>Assigned as</th>
				<th>Option</th>
			</thead><tbody>";
			$count = 0;
			$uid = Yii::$app->user->id;
			foreach ($candidates as $controlSetting) {
				if (!is_null($controlSetting->evaluators) && !in_array($uid, json_decode($controlSetting->evaluators)) || !$controlSetting->facultyCourseAssignmentRel) {
					continue;
				}

				$candidate = $controlSetting->candidateStaffRel;

				// if already evalauted, continue:
				if ($controlSetting->alreadyEvaluated($uid, $candidate->id)) {
					continue;
				}
				$dept = $controlSetting->deptPlanRel->deptRel;
				$facultyAssignment = $controlSetting->facultyCourseAssignmentRel;
				$coff2det = $facultyAssignment->coff2detRel;
				echo "<tr>";
				// $bundle = $controlEntry->deptPlanRel->criteriaBundleRel;
				$input = "<button id='eva_form__{$controlSetting->id}' data-evaluators='{$controlSetting->evaluators}' onclick='loadEvaluationForm({$controlSetting->id})' title='Load evaluation form for selection'>Load</button>";
				// $perCourse = $bundle->evaluate_per_course?"YES":"NO";
				// $criteriaCount = count(json_decode($bundle->criteria_IDS));
				echo "
				<td>" . ++$count . "</td>
				<td>{$dept->dept_code}</td>
				<td>{$candidate->getFullName()}</td>
				<td>{$coff2det->coff2detCourseRel->getCourseCredits()}</td>
				<td>{$coff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}</td>
				<td>{$facultyAssignment->assign_as}</td>
				<td>{$input}</td>
				";
				echo "</tr>";
			}
			echo "</tbody></table>";
		} else {
			// $btnGenerate = "<button class='btn btn-xs btn-primary' title='Generate Evaluation Control Entires for this schedule.' onclick='generateControlSetting({$model->id});return false;'>Generate control entry</button>";
			echo "No Candidate staff enlisted here,";
		}
	} else {
		echo "Invalid request";
		return;
	}


	?>
	<script type="text/javascript">
		window.loadEvaluationForm = (settingId) => {
			// load staff evaluation form as per the evakuation type selected: 
			// alert('Ere bakih: I am from controlSetting->view file!');
			$('#modalContent').html("Loading evaluation form....");
			$.get('/AcStEva/ase-evaluation-result/create', {
				settingId: settingId
			}).done((resp) => {
				$('#modalContent').html(resp);
			});
		}
	</script>
</div>