<?php 
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;

// ->where(['status' => SelectionSettingEntry::STATUS_APPROVED])
$entries = SelectionSettingEntry::find()->orderBy(['created_at' => SORT_DESC])->all();

if ($entries && count($entries) > 0) {
?>

<table class="table table-condensed table-stripped table-hover table-bordered table-sm" style="background-color: #FFF;">
	<thead style="background-color: #a2d246">
		<th>#</th>
		<th>Academic Year</th>
		<th>Class Level</th>
		<th>Dept. Choose #</th>
		<th>Status</th>
		<th title="Start-Date - End-Date">Selection Date</th>
	</thead>
	<tbody>
<?php
	$count = 0;
	foreach ($entries as $keySettingEntry) {
		echo "<tr>
				<td>".++$count."</td>
				<td>{$keySettingEntry->acadYearRel->sch_year_desc} - {$keySettingEntry->semRel->semester_name}</td>
				<td>{$keySettingEntry->getClassLevel()} Year ({$keySettingEntry->clslvlRel->clslvlProgramRel->program_name})</td>
				<td>{$keySettingEntry->max_count_dept} Department(s)</td>
				<td>{$keySettingEntry->getStatus()}</td>
				<td>{$keySettingEntry->getSelectionDate()}</td>
			</tr>";
	}
?>
	</tbody>
</table>
<?php
}else{
	echo "<p class='bg-warning'>No record found.</p>";
}

?>