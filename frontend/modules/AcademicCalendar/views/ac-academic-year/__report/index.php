<?php 


$model = new \frontend\modules\AcademicCalendar\models\AcAcademicYear();

$records = $model->find()->where(' status <> 0 ')->orderBy(['end_date' => SORT_DESC])->all();

if ($records && count($records) > 0) {
	echo "<table class='table table-striped table-condensed table-bordered table-hover'>
			<caption style='font-weight:normal; text-align: center; color:black;'></caption>
			<thead style='background-color:#4AD;'>
				<th>College Name </th>
				<th>Academic Year</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Status</th>
				<th>Show</th>
			</thead>";

		foreach ($records as $record) {
			$btn = "<button class='btn btn-block btn-xs btn-info pull-right' onclick='loadActivePrograms({$record->id})' title='List Programs'>Programs</button>";
			$btnAct = "<button class='btn btn-block btn-xs btn-success pull-right' onclick='loadActivityForm({$record->id})' title='List Programs'>Activity</button>";
			echo "<tr>
					<td>{$record->collegeRel->abrvn}</td>
					<td>{$record->getAcHeader()}</td>
					<td>".date('d M, Y',$record->start_date)."</td>
					<td>".date('d M, Y h:i',$record->end_date)."</td>
					<td>{$record->getStatus()}</td>
					<td>{$btn}<br>{$btnAct}</td>
				</tr>";
		}
	echo "</table>";
}


?>