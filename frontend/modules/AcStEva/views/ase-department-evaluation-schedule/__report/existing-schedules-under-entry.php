<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(54566562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 

if (isset($model)) {
	$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
	$entries = $model->headerRel->getAseDepartmentEvaluationSchedules()->andFilterWhere(['dept_relId' => $inst->inst_dep_relId])->all();
	if ($entries && count($entries) > 0) {
		// $btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Department Staff Evaluation schedules under <b><i>{$model->headerRel->evaluation_header}</i></b><br>
				Department : <b><i>{$model->deptRel->dept_name}</i></b>
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Code</th>
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
			echo "<tr>";
			$bundle = $deptSchedule->criteriaBundleRel;
			$input = "<input name='Update' type='button' class='btn btn-xs btn-warning' id='bun__{$deptSchedule->id}' data-code='{$bundle->code}' onclick='manageSchedule({$deptSchedule->id})'>";
			$perCourse = $bundle->evaluate_per_course?"YES":"NO";
			$criteriaCount = count(json_decode($bundle->criteria_IDS));
			echo "
				<td>".++$count."</td>
				<td>{$bundle->code}</td>
				<td>{$bundle->evaluator_role}</td>
				<td>{$bundle->candidate_staff_role}</td>
				<td style='text-align: center;'>{$bundle->average_point}</td>
				<td style='text-align: center;'>{$bundle->average_percentage}</td>
				<td style='text-align: center;'>{$perCourse}</td>
				<td style='text-align: center;'><a href='#' onclick='showEvaluationCriteriaDetail({$bundle->id})'>{$criteriaCount}</a></td>
				<td styele='text-align:center;'>{$deptSchedule->getScheduleInterval()}</td>
				<td styele='text-align:center;'>{$input}</td>
				";
			echo "</tr>";
	}
	echo "</tbody></table>";
	}else{
		echo "No Record found. Please register one.";
	}
}
?>
</div>

<script type="text/javascript">
	window.manageSchedule = (scheduleId) => {
		window.location.href = "/AcStEva/ase-department-evaluation-schedule/view?id="+scheduleId;
	}
	window.toggleDisplay = (id) => {
        $('#'+id).toggle('display');
    }
    window.showEvaluationCriteriaDetail = (bundleId) => {
    	$('#modal').find('.modal-header').html('Evaluation Criteria Deatils');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-criteria-bundle/show-criteria-detail',{bundleId:bundleId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
</script>