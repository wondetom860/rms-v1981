<?php 

if (isset($model) && count($model) > 0) {

	$model1 = $model[0]->gasGsetRel;

	$progra = $model1->gsAdmRel->admission_name .' '.$model1->gsProgRel->program_name;
	$clslvl = $model1->gsClslvlRel->clslvl_name;
	$gdept = $model1->gsGroupRel->group_name .' '.$model1->gsDeptRel->dept_name;
	$acadYear = $model1->gsAcadYearRel->sch_year_desc .'-'.$model1->gsSemRel->semester_name;

	echo "<table class='table table-condensed table-sm table-bordered table-striped table-hover' style='border:1px solid black;'>
			<caption style='color:#000; font-weight:normal; text-align:center; border-bottom:1px solid black;'>
				<h3>
					".strtoupper(\Yii::$app->params['client']).", ".strtoupper(\Yii::$app->params['college'])."
				</h3>
				<h5>Internship Eligibility Report</h5>
				Program : <b><u>{$progra}</u></b>
				Class Level : <b><u>{$clslvl}</u></b><br>
				Group - Department : <b><u>{$gdept}</u></b><br>
				Academic Year : <b><u>{$acadYear}</u></b>

			</caption>
			<thead style='background-color: #9DF;border:1px solid black;'>
				<tr>
					<th rowspan=2>#</th>
					<th rowspan=2>Reg. ID</th>
					<th rowspan=2>Full Name</th>
					<th rowspan=2>Status</th>
					<th colspan=2>Remark</th>
				</tr><tr>
					<th>NGs.</th>
					<th>Fs.</th>
				</tr>
			</thead>
			<tbody>
			";
	$count = 0;
	foreach ($model as $keyGset) {
		$std = $keyGset->gasStdRel;
		$status = $std->checkIfEligible();
		$stat = "";
		$remark = "";
		if (is_array($status)) {
			$stat = $status[0];
			$remark = $status[1];
		}
		echo "<tr>
				<td>".++$count."</td>
				<td>{$std->std_regId}</td>
				<td>{$std->getFullName()}</td>
				<td>{$stat}</td>
				{$remark}
			</tr>";
	}
	echo "</tbody>
		</table>";
}else{
	die("<p class='bg-danger'>No record .</p>");
}

?>