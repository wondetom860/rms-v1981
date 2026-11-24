
<?php 
use \yii\helpers\ArrayHelper;
// $statusNames;
if (isset($model) && !empty($model)) {
	if (is_array($model) && count($model) > 0) {
		
		$status = isset($statusList) && !empty($statusList) ? $statusList : [];

		// rest of successfull response operation is expected here

		echo "<table class='table table-condensed table-sm table-bordered table-hover' style='font-size:7pt;'>";
		echo drowCaption($model);
		$repo = drowTableHeader($model,$status);
		echo $repo[0];
		echo "<tbody>";
		echo drawContent($model,$status,$repo[1]);
		echo "</tbody></table>";

	}else if(is_string($model)){
		echo "<p class='bg-danger'>{$model}</p>";
	}else{
		echo "<p class='bg-danger'>INVAliD ResoPonse!</p>";
	}
}else{
	echo "<p class='bg-danger'>INVAliD RequEst!</p>";
}

function drawContent($model,$status,$statusNames){
	$statusNames = is_null($statusNames) ? getAllStatusNames($status) : $statusNames;

	$indexedDept = ArrayHelper::index($model,'srd2GroupRel.gs_dept_relId');

	$modelStatusControl = new \backend\models\StdStatusCalculationControlDefault();
	$count = [];

	$count = countStatus($statusNames, $model);

	$report = "";

	$totalStatus = [];
	foreach ($indexedDept as $keyDeptId => $valueDept) {
		// $count[$keyDeptId] = 0;

		$indexedDeptPrograms = ArrayHelper::index($model,'srd2GroupRel.gs_prog_relId');
		$indexedDeptClslvls = ArrayHelper::index($model,'srd2GroupRel.gs_clslvl_relId');
		$deptName = \backend\models\Department::findOne($keyDeptId)->dept_name;
		$deptRowSpan = count($indexedDeptClslvls) + count($indexedDeptPrograms);

		$programRowSpan = count($indexedDeptPrograms);

		$report .= "<tr>";
		$report .= "<td rowspan={$deptRowSpan}>{$deptName}</td>";

		foreach ($indexedDeptPrograms as $keyProgram => $valueProgram) {
			// $count[$keyDeptId][$keyProgram] = 0;
			$programName = \backend\models\Program::findOne($keyProgram)->program_name;
			$indexedDeptProgClslvl = ArrayHelper::index($model,'srd2GroupRel.gs_clslvl_relId');
			$clslvlRowsCount = count($indexedDeptProgClslvl);
			$report .= "<td rowspan={$clslvlRowsCount}>{$programName}</td>";
			$totalDept = 0;
			foreach ($indexedDeptProgClslvl as $keyClslvl => $valueClslvl) {
				$total = 0;
				$clslvlNum = \backend\models\ClassLevel::findOne($keyClslvl)->clslvl_name;
				$report .= "<td style='text-align:center;'>{$clslvlNum}</td>";

				foreach ($statusNames as $keyStatus) {

					$c = isset($count[$keyDeptId][$keyProgram][$keyClslvl][$keyStatus->id]) ? $count[$keyDeptId][$keyProgram][$keyClslvl][$keyStatus->id] : 0;
					$report .= "<td style='font-weight:normal; text-align:center;'>{$c}</td>";
					$total += $c;
					$totalStatus[$keyStatus->id] = isset($totalStatus[$keyStatus->id]) ? $totalStatus[$keyStatus->id] + $c : $c;
				}
				$report .= "<td>{$total}</td>";
				$totalDept += $total;
			}
			$report .= "</tr><tr>";
		}

		$report .= "</tr>";
	}

	$report .= "<tr style='font-weight:bold; background-color:#DDA; text-align:center; text-decoration:underline;'>
		<td style='font-weight:bold; text-align:right;' colspan=3>Total</td>";
	$total1 =0;
	foreach ($statusNames as $keyStatus) {
		$report .= "<td>{$totalStatus[$keyStatus->id]}</td>";
		$total1 += $totalStatus[$keyStatus->id];
	}
	$report .= "<td>{$total1}</td></tr>";

	return $report;

	// var_dump(count($indexedDept),count($indexedprogram),count($indexedClslvlAll));die();
}

function countStatus($statusNames, $model){

	$report = [];
	$report[0] = 0;

	$statusIds = [];
	$semregIds = [];
		
	foreach ($statusNames as $keyStatus) {
		$statusIds[] = $keyStatus->id;
	}

	$modelSsn = new \backend\models\StudentSemesterStatusHistory();

	foreach ($model as $keySrd2Model) {
		if (in_array($keySrd2Model->srd2_semreg_relId, $semregIds)) {
			continue;
		}else{
			$semregIds[] = $keySrd2Model->srd2_semreg_relId;
		}

		$keyDeptId = $keySrd2Model->srd2GroupRel->gs_dept_relId;
		$keyProgram = $keySrd2Model->srd2GroupRel->gs_prog_relId;
		$keyClslvl = $keySrd2Model->srd2GroupRel->gs_clslvl_relId;

		$ssn = $modelSsn->getModifiedStatus($keySrd2Model);

		if ($ssn && in_array($ssn->sssh_sssn_relId, $statusIds)) {
			$report[$keyDeptId][$keyProgram][$keyClslvl][$ssn->sssh_sssn_relId] = isset($report[$keyDeptId][$keyProgram][$keyClslvl][$ssn->sssh_sssn_relId]) ? $report[$keyDeptId][$keyProgram][$keyClslvl][$ssn->sssh_sssn_relId] + 1 : 1;
			continue;
		}

		if ( $keySrd2Model->srd2SemregRel->semreg_gradeCompleted ) {
			$sId = $keySrd2Model->srd2SemregRel->semreg_last_ssn_relId;
			if (in_array($sId, $statusIds)) {
				$report[$keyDeptId][$keyProgram][$keyClslvl][$sId] = isset($report[$keyDeptId][$keyProgram][$keyClslvl][$sId]) ? $report[$keyDeptId][$keyProgram][$keyClslvl][$sId] + 1 : 1;
			}else{
				$report[0]++;

				// This status may be in the student_semester_status_history entry - that are changed by registrar staffs because of some cases [personal dropout, withdrawn, deciplinary dismissal, ] 
			}				
		}else{
			// some thing did with this student profile.

		}
	}

	return $report;

}

function drowTableHeader($model,$status){
	$statusNames = getAllStatusNames($status);

	$repStatName = "";
	$colsapn = 0;

	if ($statusNames && count($statusNames) > 0) {
		$colspan = count($statusNames);
		foreach ($statusNames as $keySn) {
			$repStatName .= "<th>{$keySn->sssn_name}</th>";
		}
		// $repStatName .= "<th>Uknown</th>";
	}
	return ["
			<thead class='table-head' style='font-weight:normal; font-size:7pt; background-color:#cc3;'>
				<tr style='font-size:8pt;'>
					<th rowspan=2>Department</th>
					<th rowspan=2>Program</th>
					<th rowspan=2>Year</th>
					<th colspan={$colspan} style='text-align:center;'>Status</th>
					<th rowspan=2>Total</th>
				</tr><tr style='font-size:5pt;'>
					{$repStatName}
				</tr>
			</thead>
	",$statusNames];
}

function getAllStatusNames($status){

	// var_dump($status);die();
	$statusModel = NULL;
	if (is_array($status) && count($status) > 0) {
		$statusModel = \backend\models\StudentSemesterStatusName::find()->where(['id' => $status])->orderBy([
			'sssn_pass_status'=>SORT_ASC,'sssn_name' => SORT_ASC])->all();
	}else{
		$statusModel = \backend\models\StudentSemesterStatusName::find()->orderBy([
			'sssn_pass_status'=>SORT_ASC,'sssn_name' => SORT_ASC])->all();
	}

	return $statusModel;

}


function drowCaption($model){
	$program = \backend\models\Program::findOne(intval($_SESSION['post']['prog']));
	$admission = \backend\models\Admission::findOne(intval($_SESSION['post']['adm']));
	return "
		<caption style='color:#000; font-weight:normal; text-align:center; border-bottom:1px solid black'>
			". strtoupper(Yii::$app->params['client']) ." 
			,". strtoupper(Yii::$app->params['college']) ."<br>
			<b>Semester Status Report</b><br>
			Academic Year : <b><u>". $model[0]->srd2SemregRel->semregAcadYearRel->sch_year_desc.'-'.$model[0]->srd2SemregRel->semregSemesterRel->semester_name ."</u></b>
			Program : <b><u>{$admission->admission_name} - {$program->program_name}</u></b>
		</caption>
	";	
}

?>