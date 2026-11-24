<?php 
// use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;

// ->where(['status' => SelectionSettingEntry::STATUS_APPROVED])
$entries = $settingList;

if ($entries && count($entries) > 0 && !is_string($entries)) {
?>

<table class="table table-condensed table-stripped table-hover table-bordered table-sm" style="background-color: #FFF;">
	<thead style="background-color: #a2d246">
		<th>#</th>
		<th>Academic Year</th>
		<th>Class Level</th>
		<th>Dept. Choose #</th>
		<th title="Start-Date - End-Date">Selection Date</th>
		<th>Application</th>
	</thead>
	<tbody>
<?php
	$count = 0;
	foreach ($entries as $keySettingEntry) {
		$btn = "";
		// check if student made any selection ... 
		$seelctionEntry = $keySettingEntry->checkIfRequested();

		if ($seelctionEntry && count($seelctionEntry) > 0) {
			$btn = "Selection already made, Please click <a target='_blank' href='/DepartmentSelection/department-select-entry/view?id={$seelctionEntry->id}'> here </a> to see detail.";
		}else{
			$btn = "<button class='btn btn-xs btn-secondary' onclick='showApplicationForm({$keySettingEntry->id});return false;'>Show Form</button>";
		}
		echo "<tr>
				<td>".++$count."</td>
				<td>{$keySettingEntry->acadYearRel->sch_year_desc} - {$keySettingEntry->semRel->semester_name}</td>
				<td>{$keySettingEntry->getClassLevel()} Year ({$keySettingEntry->clslvlRel->clslvlProgramRel->program_name})</td>
				<td>{$keySettingEntry->max_count_dept} Department(s)</td>
				<td>{$keySettingEntry->getSelectionDate()}</td>
				<td>{$btn}</td>
			</tr>";
	}
?>
	</tbody>
</table>

<script type="text/javascript">
	window.showApplicationForm = (settingId)=>{
		selectedDepts = [];
		$("#form-container").html("Loading application Form...");
		$("#form-container").removeClass('hidden');
		$.post('/DepartmentSelection/department-select-entry/get-selection-form',{settingId:settingId}).done((resp)=>{
			$("#form-container").html(resp);
		});
	}
</script>
<?php
}elseif(is_string($entries)){echo "<p class='bg-warning'>{$entries}</p>";}else{
	echo "<p class='bg-warning'>No Record</p>";
}

?>