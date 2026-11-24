<?php 

$model = isset($model) ? $model : new \frontend\modules\AcademicCalendar\models\AcProgramSchedule();
$model->active_program_relId = intval($pid);

echo "<div class='col-md-12' style='background-color: #ADC;'>";
if (Yii::$app->user->can('Admin')) {
	echo $this->render('../_form',['model' => $model]);
}else{
	echo "<p class='bg-warning'> Only System Administrator can add schedules.</p>";
}

echo "</div><div class='col-md-12'>";

if ($schedules && count($schedules) > 0) {
	echo "<table class='table table-striped table-condensed table-bordered table-hover'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center;'>Program Schedules - {$model->getProgramHeader()}</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Activity</th>
			<th>Date Schedule</th>
			<th>Status</th>
			<th>Option</th>
		</thead>";
	$count = 0;
	foreach ($schedules as $schedule) {
		$optBtn = Yii::$app->user->can('Admin') ? "<a href='/AcademicCalendar/ac-program-schedule/update?id=".$schedule->id."' class='btn btn-xs btn-warning pull-right'>Update</a>" : "-";
		echo "<tr>
				<td>".++$count."</td>
				<td>{$schedule->actionRel->aca_name}</td>
				<td>{$schedule->getScheduleInterval()}</td>
				<td>{$schedule->getStatus()}</td>
				<td>{$optBtn}</td>
			</tr>";
	}

	echo "</table>";
}else{
	echo "<p class='bg-warning'>No record found.</p>";
}
echo "</div>";
?>
