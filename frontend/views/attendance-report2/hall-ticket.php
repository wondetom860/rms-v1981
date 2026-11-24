<?php
// use yii\helpers\Html;
// use yii\grid\GridView;
// use yii\widgets\Pjax;
$this->title = "Exam Hall Ticket";

if(is_array($adReport) && isset($stdId)){

	// print_r($adReport);
	// die();
	$stdModel = \backend\models\Student::findOne($stdId);
	$this->title .= $stdModel->stdUserRel->first_name;
}else{
	throw new yii\web\NotFoundHttpException("Error Processing Request", 1);
	
}

?>

<div class="container-fluid">
	<div class="row">
		<table class="table table-striped table-bodered" style="width:100%;">
			<caption style="background-color: #78D">
				Student Exam Hall Ticket
				<p>Reg.Id : <i><u><?= $stdModel->std_regId ?></u></i>  Student Name : <i><u><?= $stdModel->stdUserRel->first_name. ' '.$stdModel->stdUserRel->middle_name  ?></u></i></p>
			</caption>
			<tbody>
				<tr>
					<td style="width: 30%">
						<img src="/uploads/<?= $stdModel->stdUserRel->photo_num ?>" height='180' width='140'>
					</td>
					<td style="width: 70%">
						<table class="table table-condensed table-bordered" style="font-size: 9pt; width:100%;" border="1px" cellpadding="3" cellspacing="0">
							<thead>
								<th>Course Title</th>
								<th>P</th>
								<th>A</th>
								<th>T</th>
								<th>%(p)</th>
								<th>Remark</th>
							</thead>
							<tbody>
								<?php
								foreach ($adReport as $adReportKey) {
									$course = \backend\models\Course::findOne($adReportKey['courseId']);
									$p = intval($adReportKey['p']);
									$total = intval($adReportKey['total']);
									$a = $total-$p;

									$prsnt = $total == 0?'-' : sprintf("%0.2f",($p/$total)*100);
									echo "<tr>";
									echo "<td>{$course->course_title}</td>";
									echo "<td>{$p}</td>";
									echo "<td>{$a}</td>";
									echo "<td>{$total}</td>";
									echo "<td>{$prsnt}</td>";
									echo "<td>   </td>";
									echo "</tr>";

									// $p = $adReportKey['attendance']['p'];
									// $total = $adReportKey['attendance']['total'];
									// echo "<p>{}-----({$p}/{$total})</p>";
								}
								?>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
