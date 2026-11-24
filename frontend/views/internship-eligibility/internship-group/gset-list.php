<?php 

if (isset($gsetModels)) {
	if ($gsetModels && count($gsetModels) > 0) {

		echo "<table class='table table-condensed table-sm table-bordered table-striped table-hover'>
				<thead style='background-color: #9DF;'>
					<th>#</th>
					<th>Academi Year</th>
					<th>Group - Department</th>
					<th>Option</th>
				</thead>
				<tbody>
				";
		$count = 0;
		foreach ($gsetModels as $keyGset) {
			$btn = "<button class='btn btn-xs btn-success' onclick='listStudents({$keyGset->id});return false;'>Show Report</button>";
			echo "<tr>
					<td>".++$count."</td>
					<td>{$keyGset->gsAcadYearRel->sch_year_desc}-{$keyGset->gsSemRel->semester_name}</td>
					<td>{$keyGset->gsGroupRel->group_name} - {$keyGset->gsDeptRel->dept_name}</td>
					<td>{$btn}</td>
				</tr>";
		}
		echo "</tbody>
			</table>";
	}else{
		die('<p class="bg-danger">No record found. No group setup is marked as Internship group.</p>');
	}	
}


?>
