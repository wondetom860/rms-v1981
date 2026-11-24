<?php 
use \yii\helpers\ArrayHelper;

if (isset($model) && !empty($model)) {
	if (count($model) > 0) {

		$semesters = getSemestersInAdmission($model[0]);

		$peakAdmissionYears = getPeakAdmissionYears($model);

		$header = prepareAcadYearHeader("",$peakAdmissionYears[0],$peakAdmissionYears[1], $semesters ,'right');

		$cospan = count($header);

		$d = $cospan * count($semesters);
		$preparedHeader = getPreparedHeader($header);

		$semesterHeader = "";

		for ($i=0; $i < $cospan; $i++) { 
			$semesterHeader .= "<th style='text-align:center;'>SGPA</th><th style='text-align:center;'>CGPA</th>";
		}

		
		echo "<table class='table table-condensed table-bordered table-sm table-hover'>";
		echo "<caption style='color:#000; font-weight:bold; text-align:center;'>
				<h3>".Yii::$app->params['client'].", ".Yii::$app->params['college']."</h3>
				Graduate Student Point History
			</caption>";
		echo "<thead style='border:1px solid black; background-color:#DDE;'>
				<tr>
					<th rowspan=3>#</th>
					<th rowspan=3>Reg. Id</th>
					<th rowspan=3>Student Name</th>
					<th rowspan=3>Program</th>
					<th colspan={$d} style='text-align:center;'>Point History</th>
				</tr>
				<tr style='font-size:7pt;'>
					{$preparedHeader}
				</tr>
				<tr style='font-size:7pt;'>
					{$semesterHeader}
				</tr>
			</thead><tbody>";
			$count = 0;
		foreach ($model as $modelGs) {
			$gphModel = $modelGs->getGraduateStudentPointHistories()
			->with('gphRegistrationEntryRel')
			->all();

			echo "<tr>
					<td>".++$count."</td>
					<td>{$modelGs->gstd_regId}</td>
					<td>{$modelGs->gstdUserRel->getFullName()}</td>
					<td>{$modelGs->gstdAdmRel->admission_name} - {$modelGs->gstdProgRel->program_name}</td>";
			$indexedGphModel = ArrayHelper::index($gphModel,'gphRegistrationEntryRel.gphr_acadYear');

			// var_dump($indexedGphModel);die();

			foreach ($header as $keyHeader) {
				// academicYear in the form of 2020/21-I
				// change to 2020-I
				$keyHeader1 = explode('/', $keyHeader)[0]."-".explode('-', $keyHeader)[1];

				$SGPA = '';
				$CGPA = '';
				if (array_key_exists($keyHeader1, $indexedGphModel)) {
					// $SGPA = ;
					$CGPA = sprintf("%0.2f",$indexedGphModel[$keyHeader1]->gph_CGPA);
					$SGPA = sprintf("%0.2f",$indexedGphModel[$keyHeader1]->gph_SGPA);
				}
				echo "<td style='text-align:center; font-weight:bold;'>{$SGPA}</td><td style='text-align:center; font-weight:bold;'>{$CGPA}</td>";
			}

			echo "</tr>";

		}

		echo "</tbody></table>";

	}else{
		echo "<p class='bg-error'>No record found!</p>";
	}
}else{
	echo "<p class='bg-error'>No record found!, INValID ReqUest</p>";
}

function getPreparedHeader($header){

	$rep = "";
	if (is_array($header) && count($header) > 0) {
		foreach ($header as $keyAcadYearHeader) {
			$rep .= "<th colspan=2  style='text-align:center;'>{$keyAcadYearHeader}</th>";
		}
	}

	return $rep;
}

function getPeakAdmissionYears($model){
	$lowerAcadYear = -1;
	$upperAcadYear = -1;
	foreach ($model as $keyGstd) {
		$admYear = explode('/', $keyGstd->gstd_adm_year)[0];
        $gradYear = explode('/', $keyGstd->gstd_grad_year)[0];

		if ($lowerAcadYear == -1 || $lowerAcadYear < $admYear) {//initial case
			$lowerAcadYear = $admYear;
		}
		if ($upperAcadYear == -1 || $upperAcadYear > $gradYear) {
			$upperAcadYear = $gradYear;
		}
	}

	return [$lowerAcadYear,$upperAcadYear];
}

function getSemestersInAdmission($model){

	// get semesters in Admission
	return strtoupper($model->gstdAdmRel->admission_name) == 'REGULAR' ? ['I','II']:['I','II','III'];
}


function prepareAcadYearHeader($header,$lowerAcadYear,$upperAcadYear, $semesters, $position = 'right'){
	// 
	$lowerAcadYear = min($lowerAcadYear,$upperAcadYear);
	$upperAcadYear = max($lowerAcadYear,$upperAcadYear);

	$head1  = [];

	while ($lowerAcadYear < $upperAcadYear) {
		foreach ($semesters as $keySem) {
			$lN = substr(($lowerAcadYear+1), -2);
			$head1[] = $lowerAcadYear."/".($lN)."-".$keySem;
		}

		$lowerAcadYear++;
	}

	return $head1;
}


?>