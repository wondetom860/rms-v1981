<?php

$this->title = "Grouped Students";

if (isset($gsModel) && isset($stdModel)) {
	# code...
}else{
	echo "<p class='warning'>Error in selection Parameters</p>";
	echo "{$message}";
}
?>

<style media="print">
 @page {
  	size: auto;
  	margin: 0px 5px;
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
							<h3><?= strtoupper(Yii::$app->params['client']) ?></h3>
							<h4><?= strtoupper(Yii::$app->params['college']) ?></h4>
							<h5>
								Admitted Studets List for Semester <i><?= $gsModel->gsSemRel->semester_name ?></i> of Academic Year <i><?= $gsModel->gsAcadYearRel->sch_year_desc ?></i>
							</h5>
							Program : <b><u><?= $gsModel->gsAdmRel->admission_name .' '. $gsModel->gsProgRel->program_name ?></u></b>
							Class Level : <b><u><?= $gsModel->gsClslvlRel->clslvl_num ?></u></b><br>
							Department : <b><u><?= $gsModel->gsDeptRel->dept_name ?></u></b> &nbsp;&nbsp;&nbsp;&nbsp;
							Specialization : <b><u><?= $gsModel->gsSpecRel ? $gsModel->gsSpecRel->spec_name:' - ' ?></u></b><br>
							Group : <b><u><?= $gsModel->gsGroupRel->group_name ?></u></b><br>
						</center>
					</caption>
					<thead style="background-color: #8D0; border: 1px solid black;">
						<th>#</th>
						<th>Reg. ID</th>
						<th>Student Name</th>
						<th>Gender</th>
						<th>Nationality</th>
						<th>Enrolled Courses</th>
						<th>Remark</th>
					</thead>
					<tbody>
						<?php
							$count = 0;
							foreach ($stdModel as $ASkey) {
								$gset = $ASkey->stdGroupRel;
								$ccc = '-';
								$creditSum = 0;
								if($gset){
									$ccc = $gset->getStudentSemesterRegistrations2($ASkey->id)->count();
									$creditSum = $gset->getStudentSemesterRegistrations2($ASkey->id)
										->innerJoin('course','course.id=srd2_course_relId')
										->sum('course.course_crdthr');
								}
								$nation = is_null($ASkey->stdUserRel->nationality)||$ASkey->stdUserRel->nationality == 0?' ':\backend\models\Nationality::findOne($ASkey->stdUserRel->nationality)->name;
								$spec = !is_null($ASkey->stdSpecRel)?$ASkey->stdSpecRel->spec_name:'';
								echo "<tr>
									<td>".++$count."</td>
									<td>{$ASkey->std_regId}</td>
									<td>{$ASkey->stdUserRel->getFullName()}</td>
									<td>{$ASkey->stdUserRel->gender}</td>
									<td>{$nation}</td>
									<td>{$ccc} Courses ({$creditSum} Crhrs.)</td>
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