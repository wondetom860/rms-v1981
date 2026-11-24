<?php 

$all = \frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory::find()
	->groupBy('swh_acadYear_relId,swh_sem_relId')
	->orderBy('swh_acadYear_relId DESC, swh_sem_relId DESC')
	->all();
$modelWD = new \frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory();
$modelRA = new \frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory();
if ($all) {
	echo "<table class='table table-striped table-condensed table-bordered table-hover'>";
	echo "<thead>
			<th>#</th>
			<th>Academic Year</th>
			<th>Semester</th>
			<th>Withdrawal Count</th>
			<th>Re-admitted</th>
			<th>Remark</th>
		<thead><tbody>";
		$count = 0;
	foreach ($all as $year_index) {
		$c_w = count($modelWD->find()->where([
			'swh_acadYear_relId' => $year_index->swh_acadYear_relId,
			'swh_sem_relId' => $year_index->swh_sem_relId,
		])->all());
		// $c_w_r = count($modelRA->find()->where(['srh_acadYear_relId' => $year_index->swh_acadYear_relId,'srh_swh_relId' => $year_index->id])->all());
		$c_r = count($modelRA->find()->where(['srh_acadYear_relId' => $year_index->swh_acadYear_relId,'srh_sem_relId' => $year_index->swh_sem_relId,])->all());
		echo "<tr>
				<td>".++$count."</td>
				<td>{$year_index->swhAcadYearRel->sch_year_desc}</td>
				<td>{$year_index->swhSemRel->semester_name}</td>
				<td>{$c_w}</td>
				<td>{$c_r}</td>
				<td>-</td>
			</tr>";
	}
	echo "</tbody></table>";
}else{
	echo "No record.";
}
return;


?>