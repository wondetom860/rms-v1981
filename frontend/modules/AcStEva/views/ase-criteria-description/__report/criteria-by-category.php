<div class="container-fluid">
	<button class="btn btn-xs btn-warning pull-right" onclick="toggleDisplay(45455554);return false;" title="Show/Hide evaluation criterai under this category">+</button>
<?php 

if (isset($model) && !empty($model)) {
	$btnMore = "<button class='btn btn-xs btn-primary pull-right' title='Register new criteria under this category' onclick='registerNewCriteria({$model->id});return false;'>Register Evaluation Criteria</button>";
	$btnManage = "<button class='btn btn-xs btn-info pull-right' title='Manage Evalution Descriptions' onclick='redirectPage();return false;'>Manage</button>";
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-size:8pt;' id='45455554'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
			Evalution criteria under <b><i>{$model->category_name}</i></b>{$btnMore}{$btnManage}
		</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Code</th>
			<th>Criteria Description</th>
			<th>Scale</th>
			<th>Remark</th>
			<th>Option</th>
		</thead><tbody>";
		$count = 0;
	foreach ($model->aseCriteriaDescriptions as $point) {
		echo "<tr>";
		echo "
			<td>".++$count."</td>
			<td>{$point->code}</td>
			<td>{$point->description}</td>
			<td>{$point->max_rating_scale}</td>
			<td></td>
			<td></td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "Evaluation criteria not found under this category.";
	return;
}
?>
</div>
<script type="text/javascript">
	const registerNewCriteria = (modelId) => {
		$('#modal').find('.modal-header').html('Criteria description registration under category');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-criteria-description/create',{categoryid:modelId}).done(function(data){
            $('#modalContent').html(data);
        });
	}

	window.toggleDisplay = (id) => {
        $('#'+id).toggle('display');
    }

	const redirectPage = () => {
		window.location.href = "/AcStEva/ase-criteria-description/";
	}
</script>