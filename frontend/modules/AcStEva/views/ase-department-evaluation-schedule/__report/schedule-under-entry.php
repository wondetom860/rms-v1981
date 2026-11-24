<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(54566562);return false;" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 

if (isset($model)) {
	$entries = $model->getAseDepartmentEvaluationSchedules()->orderBy(['dept_relId' => SORT_ASC])->all();
	if ($entries && count($entries) > 0) {
		// $btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				Department Staff Evaluation schedules under <b><i>{$model->evaluation_header}</i></b><br>
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Department</th>
				<th>Evaluation Type</th>
				<th>Average Pt.</th>
				<th>Average %</th>
				<th>Per-Course</th>
				<th>Criteria</th>
				<th>Schedule</th>
				<th>Remark</th>
			</thead><tbody>";
			$count = 0;
		foreach ($entries as $deptSchedule) {
			echo "<tr>";
			$bundle = $deptSchedule->criteriaBundleRel;
			$input = "<input name='Update' type='button' class='btn btn-xs btn-warning' id='bun__{$deptSchedule->id}' data-code='{$bundle->code}' onclick='manageSchedule({$deptSchedule->id})'>";
			$perCourse = $bundle->evaluate_per_course?"YES":"NO";
			$criteriaCount = count(json_decode($bundle->criteria_IDS));
			$dcode = $deptSchedule->deptRel ? $deptSchedule->deptRel->dept_code : '';
			$status = "<span class='pull-right bg-info' style='font-size: 8pt; font-style: italic;'>Status: {$deptSchedule->getStatus()}</span>";
			echo "
				<td>".++$count."</td>
				<td>{$dcode}</td>
				<td>{$bundle->code} : [{$bundle->evaluator_role}>>{$bundle->candidate_staff_role}]</td>
				<td style='text-align: center;'>{$bundle->average_point}</td>
				<td style='text-align: center;'>{$bundle->average_percentage}</td>
				<td style='text-align: center;'>{$perCourse}</td>
				<td style='text-align: center;'><a href='#' onclick='showEvaluationCriteriaDetail({$bundle->id})'>{$criteriaCount}</a></td>
				<td styele='text-align:center;'>{$deptSchedule->getScheduleInterval()}{$status}</td>
				<td></td>
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