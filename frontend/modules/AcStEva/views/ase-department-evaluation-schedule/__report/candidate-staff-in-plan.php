<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(5456562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 
// var_dump($candidates);die();
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
				<th>Academic Year</th>
				<th>Course</th>
				<th>Section/Group</th>
				<th>Assigned as</th>
				<th>Evalutors</th>
				<th>Option</th>
			</thead><tbody>";
		$count = 0;
		foreach ($candidates as $facultyAssignment) {
			echo "<tr>";
			$inst = $facultyAssignment->instRel;
			$coff2det = $facultyAssignment->coff2detRel;
			$dept = $coff2det->coff2detCoff2Rel->coff2GroupRel->gsDeptRel;
			// check if setting is generated: 
			if ($deptPlanModel->criteriaBundleRel->evaluate_per_course) {
				$setting = $deptPlanModel->getAseEvaluationControlSettings()
					->andFilterWhere([
						'candidate_staff_relId' => $inst->inst_user_relId,
						'course_relId' => $coff2det->coff2det_course_relId,
					])
					->one();
			}else{
				$setting = $deptPlanModel->getAseEvaluationControlSettings()
					->andFilterWhere([
						'candidate_staff_relId' => $inst->inst_user_relId,
					])
					->one();
			}
			
			$evaluators = '';
			$evltrs = 0;
			if ($setting && count($setting) > 0) {
				// var_dump($setting);die();
				if (!is_null($setting->evaluators)) {
					$evaluators = $setting->evaluators;
					$evltrs = count(json_decode($setting->evaluators));
				}
			}
			// $bundle = $controlEntry->deptPlanRel->criteriaBundleRel;
			$input = "<button id='facu__{$facultyAssignment->id}' data-evaluators='{$evaluators}' onclick='getCandidateEvaluators({$facultyAssignment->id},{$deptPlanModel->id},{$facultyAssignment->instRel->inst_user_relId})' title='Load eligible evaluators for selection'>Load</button>";
			// $perCourse = $bundle->evaluate_per_course?"YES":"NO";
			// $criteriaCount = count(json_decode($bundle->criteria_IDS));
			echo "
				<td>".++$count."</td>
				<td>{$dept->dept_code}</td>
				<td>{$inst->getFullName()}</td>
				<td>{$coff2det->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}-{$coff2det->coff2detCoff2Rel->coff2SemRel->semester_name}</td>
				<td>{$coff2det->coff2detCourseRel->getCourseCredits()}</td>
				<td>{$coff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}</td>
				<td>{$facultyAssignment->assign_as}</td>
				<td style='text-align:center;'>{$evltrs}</td>
				<td>{$input}</td>
				";
			echo "</tr>";
			echo " <tr style='display:none' class='candidate_staff_container'>
					<td colspan='9' id='evalautors_id_{$facultyAssignment->id}'>---</td>
				</tr>";
		}
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
<script type="text/javascript">
	
</script>
</div>