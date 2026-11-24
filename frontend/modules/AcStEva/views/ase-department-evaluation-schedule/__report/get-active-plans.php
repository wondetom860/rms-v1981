<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right hidden" onclick="toggleDisplay(54566562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
	<?php

	use backend\models\Student;
	if(Yii::$app->user->can('DepartmentHead')){
		$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->innerJoinWith('criteriaBundleRel')
			->where([
				'status' => 1,
				'dept_relId' => $inst->inst_dep_relId,
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	}elseif (Yii::$app->user->can('Instructor')) {
		$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->innerJoinWith('criteriaBundleRel')
			->where([
				'status' => 1,
				'dept_relId' => $inst->inst_dep_relId,
				'evaluator_role' => ['Instructor'],
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	} elseif (Yii::$app->user->can('Student')) {
		$std = Student::find()->where(['std_user_relId' => Yii::$app->user->id])->one();
		// get student last enrollment:
		$lastSemregCOursedeptIds = getLastEnrolledCourses($std);
		// var_dump($lastSemregCOursedeptIds);die();
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->innerJoinWith('criteriaBundleRel')
			->where([
				'status' => 1,
				'evaluator_role' => 'Student',
				// 'dept_relId' => $std->std_dept_relId//this should be the department from which the student has enrolled course in that specific semester
				'dept_relId' => $lastSemregCOursedeptIds
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	} else if (Yii::$app->user->can('Technical Assistant')) {
		$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->innerJoinWith('criteriaBundleRel')
			->where([
				'status' => 1,
				'dept_relId' => $inst->inst_dep_relId,
				'evaluator_role' => ['Technical Assistant'],
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	} else if(Yii::$app->user->can('AcademicDean')){
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->innerJoinWith('criteriaBundleRel')
			->where([
				'status' => 1,
				'evaluator_role' => ['AcademicDean'],
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	} else {
		// student
		$entries = \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::find()
			->where([
				'status' => 1,
			])
			->orderBy(['dept_relId' => SORT_ASC])
			->all();
	}

	if ($entries && count($entries) > 0) {
		// $btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Department Staff Evaluation schedules
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Code</th>
				<th>Dept.</th>
				<th>Evaluator Role</th>
				<th>Candidate Staff Role</th>
				<th>Average Point</th>
				<th>Average %</th>
				<th>Per Course?</th>
				<th>Crieteria</th>
				<th>Schedule</th>
				<th>Option</th>
			</thead><tbody>";
		$count = 0;
		foreach ($entries as $deptSchedule) {
			// $deptSchedule->getStatus();
			echo "<tr>";
			$bundle = $deptSchedule->criteriaBundleRel;
			$input = "<button name='Update' class='btn btn-xs btn-info' id='bun__{$deptSchedule->id}' data-code='{$bundle->code}' onclick='showCandidateStaff({$deptSchedule->id})' title='Show staff to be evaluated'>Show Staff</button>";
			$perCourse = $bundle->evaluate_per_course ? "YES" : "NO";
			$criteriaCount = count(json_decode($bundle->criteria_IDS));
			$srate = sprintf("%0.2f", $bundle->average_point);
			// if (!in_array(Yii::$app->user->identity->id, json_decode($bundle->evaluators, true))) {
			// 	continue;
			// }
			echo "
				<td>" . ++$count . "</td>
				<td>{$bundle->code}</td>
				<td>{$deptSchedule->deptRel->dept_code}</td>
				<td>{$bundle->evaluator_role}</td>
				<td>{$bundle->candidate_staff_role}</td>
				<td style='text-align: center;'>{$srate}</td>
				<td style='text-align: center;'>{$bundle->average_percentage}%</td>
				<td style='text-align: center;'>{$perCourse}</td>
				<td style='text-align: center;'><a href='#' onclick='showEvaluationCriteriaDetail({$bundle->id})'>{$criteriaCount}</a></td>
				<td styele='text-align:center;'>{$deptSchedule->getScheduleInterval()}-{$deptSchedule->getStatus()}</td>
				<td styele='text-align:center;'>{$input}</td>
				";
			echo "</tr>";
		}
		echo "</tbody></table>";
	} else {
		echo "No active schedule found for semester evaluation.";
	}

	function getLastEnrolledCourses(Student $student){
		return $student->getLastEnrolledCourseDeptIds();
	}
	?>
</div>

<script type="text/javascript">
	window.manageSchedule = (scheduleId) => {
		window.location.href = "/AcStEva/ase-department-evaluation-schedule/view?id=" + scheduleId;
	}
	const showCandidateStaff = (deptPlanId) => {
		$('#modal').find('.modal-header').html('Academic Staff performance evaluaution - Staff to be evaluated: ');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/AcStEva/ase-evaluation-control-setting/get-candidates-for-evaluator', {
			deptPlanId: deptPlanId
		}).done((resp) => {
			$('#modalContent').html(resp);
		});
	}
	window.toggleDisplay = (id) => {
		$('#' + id).toggle('display');
	}
	window.showEvaluationCriteriaDetail = (bundleId) => {
		$('#modal').find('.modal-header').html('Evaluation Criteria Deatils');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/AcStEva/ase-criteria-bundle/show-criteria-detail', {
			bundleId: bundleId
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
</script>