<?php
$this->title = "Course enrolled Students list";
if (!(isset($gsetObj) && isset($coffdetObj))) {
	throw new \yii\web\NotFoundHttpException("Invalid requests!", 1);
}
$stdIds = [];

$semregdets  = \frontend\models\SemesterRegistration2Detail::find()
->where([
    'srd2_course_relId' => $coffdetObj->coff2detCourseRel->id,
    'srd2_group_relId' => $gsetObj->id,
])->all();

$semModel = $coffdetObj->coff2detCoff2Rel->coff2SemRel;
$acadYearModel = $coffdetObj->coff2detCoff2Rel->coff2AcadYearRel;

$spec = isset($gsetObj->gsSpecRel) ? $gsetObj->gsSpecRel->spec_name:'-';

$stds = [];

if ($semregdets && count($semregdets) > 0) {
	foreach ($semregdets as $ke) {
		$stdIds[] = $ke->srd2SemregRel->semregStdRel->std_user_relId;
	}	
	$stds = \backend\models\Student::find()
		->innerJoin('user','user.id = student.std_user_relId')
		->where(['user.id'=>$stdIds])
		->orderBy('user.first_name')
		->all();
}else{
	throw new \yii\web\NotFoundHttpException("Course ENrolled Studemt list empty...!", 1);
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
	<table class="table table-bordered table-condensed table-striped" cellpadding="2px" cellspacing="2px" style="border-color: black; border-spacing: 0px; font-size: 10pt; width: 100%">
		<caption style="font-weight: normal; color: black;">
			<center>
				<h2><?= strtoupper(Yii::$app->params['client']) .', '. strtoupper(Yii::$app->params['college']) ?></h2>
				<h4><u>Course Title: <i><?= $coffdetObj->coff2detCourseRel->course_title.'['.$coffdetObj->coff2detCourseRel->course_code. ']' ?></i> </u></h4>
				
				<b>Department</b> : <u><?= $gsetObj->gsDeptRel->dept_name ?></u> &nbsp;&nbsp;&nbsp; 
				<b>Specialization</b> : <u><?= $spec ?></u><br>
				<b>Program</b> : <u><?= $gsetObj->gsAdmRel->admission_name ?> <?= $gsetObj->gsProgRel->program_name ?></u><br>
				<b>Class Level</b> : <u><?= $gsetObj->gsClslvlRel->clslvl_num ?></u><br>
				<i><?= $semModel->semester_name ?></i> of Academic Year <i><?= $acadYearModel->sch_year_desc ?></i>
			</center>
		</caption>	
		<thead style="background-color: #8DF;">
			<th>#</th>
			<th>Reg ID</th>
			<th>Full Name</th>
			<th>Remark</th>
		</thead>
		<tbody>
			<?php
			$count = 0;
			foreach ($stds as $key) {
				echo "<tr>
						<td>".++$count."</td>
						<td>{$key->std_regId}</td>
						<td>". ucfirst(strtolower($key->stdUserRel->first_name)) ." ".ucfirst(strtolower($key->stdUserRel->middle_name))." ".ucfirst(strtolower($key->stdUserRel->last_name))."</td>
						<td></td>
					</tr>";
			}

			echo "<tr style='font-weight:bold; text-align: center;'>
					<td colspan=2>Total</td>
					<td>{$count}</td>
					<td></td>
				</tr>";

			?>
		</tbody>
	</table>
</div>