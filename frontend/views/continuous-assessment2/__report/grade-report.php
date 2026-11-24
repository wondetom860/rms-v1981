<?php 

$this->title = 'Course Grade Report';
// global $gradeSummery;
// global $computeGradeSummery;
// global $gradesArray;

$print = isset($print) ? $print: false;

$coffDetModel = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);

$srd2s = $coffDetModel
	->coff2detCoff2Rel->getSemesterRegistration2Details($coffDetModel->coff2det_course_relId)
	->innerJoin('semester_registration_2','semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
	->innerJoin('student','student.id=semester_registration_2.semreg_std_relId')
	->innerJoin('user','user.id=student.std_user_relId')
	->orderBy('user.first_name')
	->all();

if ($srd2s) {
	echo "<div class='container-fluid'>";
	echo "<table class='table table-sm table-bordered table-hover table-condensed' style='border-color:#000;'>";
	echo __display_caption($coffDetModel,$print);
	echo __display_header();
	echo __display_body($srd2s,$coffDetModel);
	// echo __display_footer($srd2s,$coffDetModel);
	echo "</table>";
	echo "</div>";
	// return;
}else{
	echo "Invalid Request";
	die();
}

function __display_body($srd2s,$coffDetModel){
	$body = "";
	$count = 0;
	$gradesArray = __get_available_grades_from_setting($srd2s);

	foreach ($srd2s as $keySrd2) {
		$student = $keySrd2->srd2SemregRel->semregStdRel;
		if (!$student) {
			continue;
		}
		$grade = $keySrd2->srd2GradeRel ? $keySrd2->srd2GradeRel->gs_ltr_grade : '-';
		$body .= "<tr style='text-align:center'>
				<td style='text-align:left;'>".++$count."</td>
				<td style='text-align:left;'>{$student->getFullName()}</td>
				<td style='text-align:left;'>{$student->std_regId}</td>
				<td style='background-color:#ffd;'>{$grade}</td>
				<td style='background-color:#ffd;'></td>
			</tr>
			";
		if (isset($gradesArray[$grade])) {
			$gradesArray[$grade]++;
		}else{
			$gradesArray[$grade] = 1;
		}
	}
	// $body .= "<tr class='bg-warning'><td colspan='3'>{$prntGradeList}</td></tr>";
	$body = "<tbody>{$body}</tbody>";

	$body .= __display_footer($srd2s,$coffDetModel,$gradesArray);

	return $body;
}

function __display_footer($srd2s,$coffDetModel,$gradesArray){
	// $hod = $coffDetModel->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->hod;
	// $hod = $hod ? $hod->dhInstRel->getFullName() : '-';
	$hod = $coffDetModel->coff2detCourseRel->courseDeptRel->hod;
	$hod = $hod ? $hod->dhInstRel->getFullName() : '-';
	$ff = $coffDetModel->coff2detInstRel ? $coffDetModel->coff2detInstRel->getFullName() : 'UKNOWN_INST.';
	$gradeSummery = __drow_grade_summery_table($gradesArray);
	$statement = "
			<p><b>NB:</b>This form should be filled in two copies. The first copy will be sent to the Office of Registrar and second copy will remain in the department's office.</p>
			<center>
				<b>The above class roll and grades are complete and correct.</b>
				<h5>Department Offering the Course: <u><i>{$coffDetModel->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->dept_name}</i></u></h5>
			</center><br>

			Instructor: <u><i>{$ff}</i></u>&nbsp;&nbsp;&nbsp;
			Signature: _______________&nbsp;&nbsp;&nbsp;
			Date: ____________________<br><br>

			Department Head: <u><i>{$hod}</i></u>&nbsp;&nbsp;&nbsp;
			Signature: _______________&nbsp;&nbsp;&nbsp;
			Date: _______________<br>
		";
	return "<tfooter>
				<table cellpadding='5' cellspacing='3'>
					<tr>
						<td style='width:15%;'>
							{$gradeSummery}
						</td>
						<td style='width:85%; border-left:1px solid black; padding-left:5px;'>
							{$statement}
						</td>
					</tr>
				</table>
			</tfooter>
		";
}

function __drow_grade_summery_table($gradesArray){
	$gradeSummery = "<table cellpadding='5' cellspacing='3'>";
	$gradeSummery .= "<tr style='font-weight:bold; text-align:center;border:1px solid black;'>
			<td colspan='2'>Grade Summary</td>
		</tr>";
	// \yii\helpers\ArrayHelper::multisort($gradesArray,array_values($gradesArray));
	foreach ($gradesArray as $keygrade=>$count) {
		$gradeSummery .= "<tr style='border:1px solid black;'>
			<td style='border:1px solid black; text-align:center;'>{$keygrade}</td>
			<td style='border:1px solid black; text-align:center;'>{$count}</td>
		</tr>";	
	}
	$gradeSummery .= "</table>";

	return $gradeSummery;
}

function __display_header(){
	return "
			<thead style='background-color:#CEE;font-weight:bold;'>
				<tr style='text-align:center;'>
					<td>No</td>
					<td>Name of the student</td>
					<td>ID No.</td>
					<td>Grade</td>
					<td>Comments</td>
				</tr>
			</thead>
		";
}


function __display_caption($coffDetModel,$print){
	$pintBtn = $print?"<button class='btn btn-xs btn-warning pull-right' title='Print this mark sheet' onclick='markSheet({$coffDetModel->id});return false;'>Print</button>":'';
	$gset = $coffDetModel->coff2detCoff2Rel->coff2GroupRel;
	$spec = $gset->gs_spec_relId ? $gset->gsSpecRel->spec_name : '-';
	$clsYear = $gset->gsClslvlRel->getClslvlDisp();
	$header = $print ? "<h3>".strtoupper(Yii::$app->params['client']).", ".strtoupper(Yii::$app->params['college'])."</h3>" : '';
	return "
		<caption style='font-weight: normal; font-size: 10pt; color:#000;'>
			<center>
				{$header}
				<h4 style='text-decoration:none; font-weight:bolde'>Office of the Registrar</h4>
				<p>Official and Final grade Report Form</p>

				Department: <u><i>{$gset->gsDeptRel->dept_name}</i></u>&nbsp;&nbsp;
				Specialization: <u><i>{$spec }</i></u>&nbsp;&nbsp;
				Class Year: <u><i>{$clsYear }</i></u>&nbsp;&nbsp;
				Group: <u><i>{$gset->gsGroupRel->group_name}</i></u>&nbsp;&nbsp;
				<br>
				Course Number: <u><i>{$coffDetModel->coff2detCourseRel->course_code}</i></u>&nbsp;&nbsp;
				Course Title: <u><i>{$coffDetModel->coff2detCourseRel->course_title}</i></u>&nbsp;&nbsp;
				Credit Hour: <u><i>{$coffDetModel->coff2detCourseRel->course_crdthr}</i></u>&nbsp;&nbsp;
				Academic Year: <u><i>{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}</i></u><br>
				Program: <u><i>{$gset->gsAdmRel->admission_name}-{$gset->gsProgRel->program_name}</i></u>&nbsp;&nbsp;
				
			</center>{$pintBtn}
		</caption>
	";
}

function __get_available_grades_from_setting($srd2s){
	$gs = [];
	$gradeScale = \backend\models\GradeScale::find()
		->where(['gs_forProgram' => $srd2s[0]->srd2GroupRel->gs_prog_relId])
		->orderBy(['gs_pass_status' => SORT_DESC,'gs_gradePoint' => SORT_DESC])
		->all();

	if ($gradeScale) {
		foreach ($gradeScale as $keyGs) {
			$gs[$keyGs->gs_ltr_grade] = 0;
		}
	}

	return $gs;
}

?>