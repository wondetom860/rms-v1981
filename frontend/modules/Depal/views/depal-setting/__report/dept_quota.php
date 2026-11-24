<?php 
$quotas = $model->distributeDepalQuotas();
$elligibleDepts = json_decode(trim($model->elligible_depts,'"'));
if ($quotas && count($quotas) > 0) {
	$input = "<button class='btn btn-xs btn-primary pull-right' title='Update quota' onclick='updateQuota({$model->id})'>Update</button>";
	echo "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size: 8pt;'>";
	echo "<caption style='font-weight:normal; color: #000; text-align:center; background-color: #FFE;'>
				Department/Site allocation quota percentage ({$model->getSettingHeader()}) {$input}
			</caption>";
	echo " <thead style='background-color: #8D9'>";
	echo "<th>#</th>
		<th>Department/Site</th>
		<th>Percentage</th>
		<th>Remark</th>
		";
	echo " </thead><tbody>";
	$count = 0;
	$total = $model->getDepalDeptQoutas()->sum('q_percentage');
	foreach ($quotas as $quota) {
		if (!in_array($quota->dept_relId, $elligibleDepts)) {
			$total -= $quota->q_percentage;
			$quota->delete();
			continue;
		}
		// echo "<tr>";
		// $input = "<button class='btn btn-xs btn-link pull-right' title='See report' onclick='generateReport({$quota->id})'>Report</button>";
		// $input = "<input name='' type='button' id='cat_{$quota->id}' onchange='addToquotaList({$quota->id})'>";
		echo "
			<td>".++$count."</td>
			<td>{$quota->deptRel->dept_name}</td>
			<td>{$quota->q_percentage}%</td>
			<td>{$quota->remark}</td>
			";
		echo "</tr>";
	}
	echo " <tr style='background-color: #FED; font-weight: bold; text-align: center;'><td colspan=2 style='text-align:center; font-weight: bold;'>Total</td><td style='text-align: left;'>{$total}%</td><td></td></tr>";
	echo "</tbody></table>";
}else{
	$model->distributeDepalQuotas();
	echo "Importing data, please reload your page to see updated one....";
	return;
}

?>

<script type="text/javascript">
	const updateQuota = (settingId) => {
		// window.open('/Depal/depal-dept-quota/update-quota?hypherMode=1&settingid='+settingId,'title:Update department quota (%percentage)','width:600, height: 600');

		$('#modal').find('.modal-header').html('Update department quota (%percentage)');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Depal/depal-dept-quota/update-quota',{settingid:settingId}).done(function(data){
            $('#modalContent').html(data);
        });
	}
	window.generateReport = (settingid) => {
		window.open('/Depal/depal-categorized-candidates/show-grouped-students?hypherMode=1&settingid='+settingid,'title:Student List','width:1090,height: 900');
	}
</script>