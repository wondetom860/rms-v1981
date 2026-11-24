<?php 

if (isset($gsetModels)) {
	if ($gsetModels && count($gsetModels) > 0) {

		echo "<table class='table table-condensed table-sm table-bordered table-striped table-hover'>
				<thead style='background-color: #9DF;'>
					<th>#</th>
					<th>Academi Year</th>
					<th>Program</th>
					<th>Class Level</th>
					<th>Group - Department</th>
					<th>Option</th>
				</thead>
				<tbody>
				";
		$count = 0;
		foreach ($gsetModels as $keyGset) {
			$btn = is_null($keyGset->gs_internship) ? "<input type='checkbox' onchange='addToList({$keyGset->id})' />" : "<button class='btn btn-xs btn-warning' onclick='unmarkInternship({$keyGset->id});return false;'>Un-mark</button>";
			echo "<tr>
					<td>".++$count."</td>
					<td>{$keyGset->gsAcadYearRel->sch_year_desc}-{$keyGset->gsSemRel->semester_name}</td>
					<td>{$keyGset->gsAdmRel->admission_name}-{$keyGset->gsProgRel->program_name}</td>
					<td>Year - {$keyGset->gsClslvlRel->clslvl_num}</td>
					<td>{$keyGset->gsGroupRel->group_name} - {$keyGset->gsDeptRel->dept_name}</td>
					<td>{$btn}</td>
				</tr>";
		}
		echo "</tbody>
			</table>";
	}
}


?>
