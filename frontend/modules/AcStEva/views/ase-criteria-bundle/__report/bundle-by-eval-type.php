<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay()" title="Show/Hide evaluation criterai under this category">[=]</button>
<?php 

if (isset($model) && !empty($model)) {
	$btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteriaBundle({$model->id});return false;'>Register Evaluation Criteria</button>";
	$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage2();return false;'>Manage</button>";
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='table_criteria_bundle_container'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Evalution criteria bundle under <b><i>{$model->type_name}</i></b>{$btnMore}{$btnManage}
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
			<th>Remark</th>
		</thead><tbody>";
		$count = 0;
	foreach ($model->aseCriteriaBundles as $bundle) {
		echo "<tr>";
		$perCourse = $bundle->evaluate_per_course?"YES":"NO";
		$criteriaCount = count(json_decode($bundle->criteria_IDS));
		$avPt = sprintf("%0.2f",$bundle->average_point);
		echo "
			<td>".++$count."</td>
			<td>{$bundle->code}</td>
			<td>{$bundle->evaluator_role}</td>
			<td>{$bundle->candidate_staff_role}</td>
			<td>{$avPt}</td>
			<td>{$bundle->average_percentage}%</td>
			<td>{$perCourse}</td>
			<td style='text-align: center;'><a href='#' onclick='showEvaluationCriteriaDetail({$bundle->id})'>{$criteriaCount}</a></td>
			<td></td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "No recod found.";
	return;
}
?>

</div>

<script type="text/javascript">
	const showEvaluationCriteriaDetail = (bundleId) => {
		$('#modal').find('.modal-header').html('Criteria bundle detail');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-criteria-bundle/show-criteria-detail',{bundleId:bundleId}).done(function(data){
            $('#modalContent').html(data);
        });
	}
	const registerNewCriteriaBundle = (modelId) => {
		$('#modal').find('.modal-header').html('Criteria bundle by evaluation type');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-criteria-bundle/create',{typeid:modelId}).done(function(data){
            $('#modalContent').html(data);
        });
	}

	const redirectPage2 = () => {
		window.location.href = "/AcStEva/ase-criteria-bundle/";
	}
</script>