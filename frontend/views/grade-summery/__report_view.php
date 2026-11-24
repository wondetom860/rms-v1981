<style type="text/css">
	thead {
		vertical-align: middle;
	}
	thead > tr {
		text-align: center;
	}
	body > tr {
		font-size: 8pt;
	}
	thead > tr > th {
		text-align: center;
	}
	th.rotated-text {
		height: 250px;
		white-space: nowrap;
		padding: 5px !important;
		overflow-y: auto;
		word-break: inherit;
	}

	th.rotated-text > div {
		transform: 
			translate(5px, 0px)
			rotate(-90deg);
			width: 25px;
			font-size: 8pt;
	}

	th.rotated-text > div > span {
		padding: 4px 5px;
	}
</style>
<?php
// ini_set("memory_limit", "1024M");
use \common\components\CommonControllerFunctions as CCF;

if (isset($report) && !empty($report)) {
	// var_dump($report);
	$courses = indexCourses($report);

	$semregModel = $report[0]->srd2SemregRel;

	echo '
		<table class="table table-striped table-bordered table-hover table-sm table-condensed" style="font-size:8pt;">
	';

	echo drawCaption($semregModel);

	echo drawHeader($report,$courses);

	echo drawBody($report,$courses);

	echo '</table>';

}else{
	echo "<p class='bg-error'> No record available!</p>";
}

function drawBody($report,$courses){
	$rep = "<tbody>";
	$count = 0;
	$semregModel2 = indexStudents($report);

	foreach ($semregModel2 as $keySemreg) {
		$usr = $keySemreg->semregStdRel->stdUserRel;
		$rep .= "
			<tr>
				<td>".++$count."</td>
				<td>{$keySemreg->semregStdRel->getFullName()}</td>
				<td>{$keySemreg->semregStdRel->std_regId}</td>
				". displayGrades($keySemreg,$courses) ."
			</tr>
		";
	}

	return $rep ."</tbody>";
}

function displayGrades($keySemreg,$courses){
	$rep = "";
	$GPS = 0;
	$GPA = 0;
	$crdtSum = 0;
	$gpSum = 0;

	$dFsCount = 0;
	foreach ($courses as $keyCourse) {
		$srd2 = \frontend\models\SemesterRegistration2Detail::find()
			->where([
				'srd2_semreg_relId' => $keySemreg->id,
				'srd2_course_relId' => $keyCourse
			])
			->one();

		$grade = NULL;
		$bg = "bg-warning";
		$stylee = NULL;

		if ($srd2 && $srd2->srd2_grade_relId) {
			$grade =$srd2->srd2GradeRel->gs_ltr_grade;
			if ($grade == 'D' || $grade == 'D+' || $grade == 'F'|| $grade == 'NG') {
				$dFsCount++;
			}
			$crdtSum += $srd2->srd2CourseRel->course_crdthr;

			$gpSum += ($srd2->srd2CourseRel->course_crdthr * $srd2->srd2GradeRel->gs_gradePoint);

			$bg = $srd2->srd2GradeRel->gs_clearable == 1 ? "bg-info" : "bg-default";

			if ($grade == 'F') {
				$stylee = "background-color:#F67;";
			}
		}elseif ($srd2 && is_null($srd2->srd2_grade_relId)) {
			$dFsCount++;
			$stylee = "background-color:#F67;";
		}
		$rep .= "<td style='{$stylee}' class={$bg}>{$grade}</td>";
	}

	$semStat = CCF::getSemesterGrade($keySemreg->semreg_clslvl_relId,$keySemreg->semreg_semester_relId,$keySemreg->semreg_std_relId);

	$stat = isset($semStat[1][2][3]) && is_array($semStat[1][2][3]) && isset($semStat[1][2][3][0]) ? $semStat[1][2][3][0]->sssccdSssnRel->sssn_name : ($keySemreg->semreg_last_ssn_relId ? $keySemreg->semesterLastSsnRel->sssn_name : "");

	$acDecision = "";

	$acdec = \backend\models\StudentSemesterStatusHistory::findOne([
		'sssh_std_relId' => $keySemreg->semreg_std_relId,
		'sssh_acadYear_relId' => $keySemreg->semreg_acadYear_relId,
		'sssh_sem_relId' => $keySemreg->semreg_semester_relId,
		'sssh_clslvl_relId' => $keySemreg->semreg_clslvl_relId
	]);

	if ($acdec) {
		$acDecision = $acdec->ssshSssnRel->sssn_name;
	}

	// var_dump($semStat[1][1]);die();

	// $prevGps = $semStat[1][1][2] * $semStat[1][1][0];

	$prevGps = (isset($semStat[1][1][2]) && is_numeric($semStat[1][1][2])) && (isset($semStat[1][1][0]) && is_numeric($semStat[1][1][0])) ? $semStat[1][1][2] * $semStat[1][1][0] : '';

	$GPA = sprintf("%0.2f",$keySemreg->semreg_SGPA);
	$CGPA = sprintf("%0.2f",$keySemreg->semreg_CGPA);
	$gpSum = sprintf("%0.2f",$gpSum);
	$prevGPS = is_numeric($prevGps) ? sprintf("%0.2f",$prevGps) :'-';
	$prevGPA = isset($semStat[1][1][0]) && is_numeric($semStat[1][1][0]) ? sprintf("%0.2f",$semStat[1][1][0]) :'-';

	$rep .= "<td>{$gpSum}</td>
			 <td>{$GPA}</td>

			 <td>".$prevGPS."</td>
			 <td>".$prevGPA."</td>

			 <td>". sprintf("%0.2f",(isset($semStat[1][2][1]) && is_numeric($semStat[1][2][1])) ? $semStat[1][2][1] : 0) ."</td>
			 <td>{$CGPA}</td>

			 <td>{$dFsCount}</td>
			 <td>{$stat}</td>
			 <td>{$acDecision}</td>
			";
	return $rep;
}

function indexCourses($report){
	$courses = [];
	foreach ($report as $key) {
		if (!in_array($key->srd2_course_relId, $courses)) {
			$courses[] = $key->srd2_course_relId;
		}
	}

	return $courses;
}

function indexStudents($report){
	$students = [];
	$studentsIdList = [];
	foreach ($report as $key) {
		if (!in_array($key->srd2SemregRel->semreg_std_relId, $studentsIdList)) {
			// array_push();
			$students[] = $key->srd2SemregRel;
			$studentsIdList[] = $key->srd2SemregRel->semreg_std_relId;
		}
	}

	unset($studentsIdList);
	return $students;
}

function drawHeader($report, $courses){

	$courseTitles = prepareCourseHeader($courses);
	$courseCredits = prepareCourseCredit($courses);
	return '
		<thead style="text-align: center; vertical-align: middle; font-size:8pt;">
			<tr>
				<th rowspan=4>S.N.</th>
				<th rowspan=4>Name</th>
				<th rowspan=4>ID</th>
				<th colspan='.count($courses).'>Course Title and Code</th>
				<th rowspan=3 colspan=2 class="rotated-text" scope="col"><div><span>This Semester</span></div></th>
				<th rowspan=3 colspan=2 class="rotated-text" scope="col"><div><span>Previous</span></div></th>
				<th rowspan=3 colspan=2 class="rotated-text" scope="col"><div><span>Commulative</span></div></th>
				<th rowspan=4 class="rotated-text" scope="col"><div><span>No. of Ds, D+s and Fs</span></div></th>
				<th rowspan=4 class="rotated-text" scope="col"><div><span>Proposed Academic Status</span></div></th>
				<th rowspan=4 class="rotated-text" scope="col"><div><span>AC Decision</span></div></th>
			</tr>
			<tr>
				'.$courseTitles.'
			</tr>
			<tr>
				<th colspan='.count($courses).'>Course credit</th>
			</tr>
			<tr>
				'.$courseCredits.'
				<th>GPS</th>
				<th>GPA</th>
				<th>GPS</th>
				<th>GPA</th>
				<th>GPS</th>
				<th>GPA</th>
			</tr>
			
		</thead>
	';
}

function prepareCourseHeader($courses){

	$rep = "";
	foreach ($courses as $keyCourse) {
		$course = \backend\models\Course::findOne($keyCourse);
		$rep .= '<th class="rotated-text" scope="col"><div><span title="'. $course->course_title."({$course->course_code})"  .'">'. $course->course_title."({$course->course_code})" .'</span></div></th>';
	}
	return $rep;
}

function prepareCourseCredit($courses){

	$rep = "";
	foreach ($courses as $keyCourse) {
		$course = \backend\models\Course::findOne($keyCourse);
		$rep .= '<th scope="col">'. $course->course_crdthr.'</th>';
	}
	return $rep;
}

// domxml_xslt_stylesheet(xsl_buf)


function drawCaption($semregModel){
	$spec = !is_null($semregModel->semregStdRel->std_spec_relId) ? $semregModel->semregStdRel->stdSpecRel->spec_name : "-";
	$grp = isset($_SESSION['groupO']) && !empty($_SESSION['groupO']) ? intval($_SESSION['groupO']) : NULL;
	$grp = $grp?\backend\models\WGroupSetup::findOne($grp) : NULL;
	$gr = $grp ? $grp->gsGroupRel->group_name : NULL;
	$dept = $gr ? $grp->gsDeptRel->dept_name : '-';
	return '
		<caption style="border-bottom:1px solid black; margin-bottom:5px; color:#000; font-weight:normal">
			<div class="row"  style="text-align:center; border-bottom:1px solid black; justify-content: center; flex-wrap: nowrap;">
				<div class="col-3">
					
				</div>
				<div class="col-4" style="font-size:9pt;">
					'. Yii::t('rms', Yii::$app->params['client']) .'<br>
					<b>'. Yii::t('rms', Yii::$app->params['college']) .'</b><br>
					Office of the registrar<br>
					Official and final grade summery form<br>
				</div>
				<div class="col-3">
					
				</div>
			</div>
			<div class="row" style="text-align:center; flex-wrap: nowrap;">
				<div class="col-md-2">
					<u>'.$dept.'</u><br>
					<b>Department</b>
				</div>
				<div class="col-md-2">
					<u>'. $spec .'</u><br>
					<b>Major</b>
				</div>
				<div class="col-md-2">
					<u>'. $semregModel->semregClslvlRel->clslvl_num.'</u><br>
					<b>Year</b>
				</div>
				<div class="col-md-2">
					<u>'. $semregModel->semregSemesterRel->semester_name.'</u><br>
					<b>Semester</b>
				</div>
				<div class="col-md-2">
					<u>'. $semregModel->semregAcadYearRel->sch_year_desc.'</u><br>
					<b>Academic Year</b>
				</div>
				<div class="col-md-1">
					<u>'. $gr.'</u><br>
					<b>Group</b>
				</div>				
			</div>
		</caption>
	';
	// semreg_acadYear_relId
}
?>

<!-- <button style=""></button> -->
<!-- <div class="" style="">
	
</div> -->

