<?php 
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;

$this->title = Yii::t('decris', 'Approve Setting Entries');
// $this->params['breadcrumbs'][] = ['label' => "Main", 'url' => ['/DepartmentSelection']];
$this->params['breadcrumbs'][] = $this->title;
// ->where(['status' => SelectionSettingEntry::STATUS_APPROVED])
$entries = SelectionSettingEntry::find()->orderBy(['created_at' => SORT_DESC])->all();

if ($entries && count($entries) > 0) {
?>

<h3><?= $this->title ?></h3>
<table class="table table-condensed table-stripped table-hover table-bordered table-sm"  style="background-color: #FFF;">
	<thead style="background-color: #a2d246">
		<th>#</th>
		<th>Academic Year</th>
		<th>Class Level</th>
		<th>Dept. Choose #</th>
		<th title="Start-Date - End-Date">Selection Date</th>
		<th>Status</th>
		<th title="Setting option">Option</th>
		<th title="Schedule Status">Schedule</th>
	</thead>
	<tbody>
<?php
	$count = 0;
	foreach ($entries as $keySettingEntry) {
		$optBtn = "";
		if (is_null($keySettingEntry->status)) {
			$optBtn = "<button class='btn btn-xs btn-primary' onclick='ApproveSetting({$keySettingEntry->id},1)'>Approve</button>";
		}else{
			if (count($keySettingEntry->departmentSelectEntries) == 0) {
				$optBtn = "<button class='btn btn-xs btn-danger' onclick='ApproveSetting({$keySettingEntry->id},2)'>Cancel Approval</button>";
			}else{
				$optBtn = "Setting Used!";
			}
		}
		$schedule = $keySettingEntry->isScheduleActive() ? 'Active' : 'Not Active';
		echo "<tr>
				<td>".++$count."</td>
				<td>{$keySettingEntry->acadYearRel->sch_year_desc} - {$keySettingEntry->semRel->semester_name}</td>
				<td>{$keySettingEntry->getClassLevel()} Year ({$keySettingEntry->clslvlRel->clslvlProgramRel->program_name})</td>
				<td>{$keySettingEntry->max_count_dept} Department(s)</td>
				<td>{$keySettingEntry->getSelectionDate()}</td>
				<td>{$keySettingEntry->getStatus()}</td>
				<td>{$optBtn}</td>
				<td>{$schedule}</td>
			</tr>";
	}
?>
	</tbody>
	<script type="text/javascript">
		window.ApproveSetting = (settingId,option)=>{
			if (confirm('Are you sure for this action?')) {
				$.post("/DepartmentSelection/selection-setting-entry/approve-setting-action",{settingId:settingId,option:option}).done((rep)=>{
					if (rep == 1) { window.location.href = window.location; }else{ alert(rep); }
				});
			}
		}
	</script>
</table>
<?php
}else{
	echo "<p class='bg-warning'>No record found.</p>";
}

?>