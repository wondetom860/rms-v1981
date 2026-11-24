<?php

$this->title = "Grouped Students";

if (isset($stdModel)) {
	# code...
	$spec = is_null($specModel)?' - ':$specModel->spec_name;
}else{
	echo "<p class='warning'>Error in selection Parameters</p>";
	echo "{$message}";
}
?>

<style media="print">
 @page {
  	size: auto;
  	margin: 0px 10px;
    }
</style>

<style type="text/css">
	.table-bordered > thead > tr > th,
	.table-bordered > tbody > tr > th,
	.table-bordered > tfoot > tr > th,
	.table-bordered > thead > tr > td,
	.table-bordered > tbody > tr > td,
	.table-bordered > tfoot > tr > td {
	    border: 1px solid #00a65a;
	    padding: 5px;
	    border-collapse: collapse;
	}
</style>
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="container-fluid">
				<table class="table table-bordered table-condensed table-striped" cellpadding="2px" cellspacing="2px" style="border-color: black; border-spacing: 0px; font-size: 10pt; width: 100%">
					<caption style="font-weight: normal; color: black;">
						<center>
							<h2><?= strtoupper(Yii::$app->params['client']) .', '. strtoupper(Yii::$app->params['college']) ?></h2>
							<h4><u>Course Enrolled Students List for Semester <i><?= $semModel->semester_name ?></i> of Academic Year <i><?= $acadYearModel->sch_year_desc ?></i> </u></h4>
							
							<b>Department</b> : <u><?= $deptModel->dept_name ?></u> &nbsp;&nbsp;&nbsp; 
							<b>Specialization</b> : <u><?= $spec ?></u><br>
							<b>Program</b> : <u><?= $amdModel->admission_name ?> <?= $progModel->program_name ?></u><br>
							<b>Class Level</b> : <u><?= $clslvlModel->clslvl_num ?></u>
						</center>
					</caption>
					<thead style="background-color: #8D0; border: 1px solid black;">
						<th>#</th>
						<th>Reg. ID</th>
						<th>Student Name</th>
						<th>Gender</th>
						<th>Nationality</th>
						<th>Group</th>
						<th>Remark</th>
					</thead>
					<tbody>
						<?php
							$count = 0;
							foreach ($stdModel as $ASkey) {
								$nation = is_null($ASkey->stdUserRel->nationality)||$ASkey->stdUserRel->nationality == 0?' ':\backend\models\Nationality::findOne($ASkey->stdUserRel->nationality)->name;
								$spec = !is_null($ASkey->stdSpecRel)?$ASkey->stdSpecRel->spec_name:'';
								$g = '';
								$g = is_null($ASkey->std_group_realId)?' ':$ASkey->stdGroupReal->gsGroupRel->group_name;
								echo "<tr>
									<td>".++$count."</td>
									<td>{$ASkey->std_regId}</td>
									<td>{$ASkey->stdUserRel->first_name} {$ASkey->stdUserRel->middle_name} {$ASkey->stdUserRel->last_name}</td>
									<td>{$ASkey->stdUserRel->gender}</td>
									<td>{$nation}</td>
									<td>{$g}</td>
									<td></td>
								</tr>";
							}
							echo "<tr>
								<td style='font-weight:bold; text-align:center;' colspan = '3'>Total</td>
								<td  style='font-weight:bold; text-align:center;' colspan = '2'><u>".$count."</u></td>
								<td></td>
							</tr>";
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>