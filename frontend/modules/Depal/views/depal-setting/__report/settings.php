<?php 
// better look for only active academic departments.
// $depts = \backend\models\Department::find()->all();
$settings = frontend\modules\Depal\models\DepalSetting::find()
	->innerJoin('ac_active_programs','ac_active_programs.id=section_relId')
	->orderBy(['academic_year_relId' => SORT_DESC])
	->all();
if ($settings && count($settings) > 0) {
	echo "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size: 8pt;'>";
	echo "<caption style='font-weight:normal; color: #000; text-align:center;'>
				Department Selection Control Settings
			</caption>";
	echo " <thead>";
	echo "<th>#</th>
		<th>Academic Year</th>
		<th>Section</th>
		<th>Eligible Departments</th>
		<th>Choice count</th>
		<th>Schedule Status</th>
		<th>Applicable Criteria</th>
		<th>Report</th>
		";
	echo " </thead><tbody>";
	$count = 0;
	foreach ($settings as $setting) {
		echo "<tr>";
		$input = "<button class='btn btn-xs btn-link pull-right' title='See report' onclick='generateReport({$setting->id})'>Report</button>";
		// $input = "<input name='' type='button' id='cat_{$setting->id}' onchange='addTosettingList({$setting->id})'>";
		echo "
			<td>".++$count."</td>
			<td>{$setting->sectionRel->academicYearRel->getAcHeader()}</td>
			<td>{$setting->sectionRel->getClassLevelHeader()}</td>
			<td>{$setting->stringifyDepartments()}</td>
			<td style='text-align:center;'>{$setting->depts_count}</td>
			<td>{$setting->getScheduleStatus(true)}</td>
			<td>{$setting->stringifyCategories()}</td>
			<td>{$input}</td>
			";
		echo "</tr>";
	}
	echo "</tbody></table>";
}

?>

<script type="text/javascript">
	window.generateReport = (settingid) => {
		window.open('/Depal/depal-categorized-candidates/show-grouped-students?hypherMode=1&settingid='+settingid,'title:Student List','width:1090,height: 900');
	}
</script>