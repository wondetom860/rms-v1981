<?php

$coffDetModel = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
$print = isset($print) ? $print : false;

if ($coffDetModel) {
	// $assessmentBreakDowns = $coffDetModel->continuousAssessment2s;//continousAssessments
	$assessmentCategories = $coffDetModel->getContAssessCatagories($coffDetModel); //ccont-Assess-Category2
	if (!$assessmentCategories) {
		echo "Course assessment mark break-down setting NOT done yet. Please configure <a href='/cont-assess-catagory2/'>it</a>";
		die();
	}

	$srd2s = $coffDetModel->coff2detCoff2Rel
		->getSemesterRegistration2Details($coffDetModel->coff2det_course_relId)
		->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
		->innerJoin('student', 'student.id=semester_registration_2.semreg_std_relId')
		->innerJoin('user', 'user.id=student.std_user_relId')
		->orderBy(['first_name' => SORT_ASC, 'middle_name' => SORT_ASC, 'last_name' => SORT_ASC])
		->all();
	// $imas = $assessmentBreakDowns[0]->caCatagorySetupRel->instanceMarkAssignments;

	echo "<div class='container-fluid'>";
	echo "<table class='table table-sm table-bordered table-hover table-condensed' style='border-color:#000;'>";
	echo __display_caption($coffDetModel, $print);
	echo __display_header($coffDetModel, $assessmentCategories);
	echo __display_body($srd2s, $assessmentCategories, $coffDetModel, $print);
	echo "</table>";
	echo "</div>";
} else {
	echo "Invalid Request";
	die();
}

function __display_body($srd2s, $assessmentCategories, $coffDetModel, $print)
{
	$colsCount = 0; //count of assessment entries on the header of the table header
	$contAssObj = new \backend\models\ContinuousAssessment2();
	$count = 0;
	$body = "";
	$prntGradeList = "";
	$first = true;
	foreach ($srd2s as $keySrd2) {
		$student = $keySrd2->srd2SemregRel->semregStdRel;
		$totalAssessed = 0;
		$totalObtained = 0;
		$jumped = false;
		$markedSome = false;
		$grade = '-';
		$title2 = "";
		$bg2 = 'default';
		if (!$student) {
			continue;
		}
		$marks = "";
		$message = "Missed [";
		// $markedSome = false;
		foreach ($assessmentCategories as $keyAssessmetnCategory) {
			$imas  = $keyAssessmetnCategory->instanceMarkAssignments;
			// if ($first) {
			// 	$colsCount += count($imas);
			// }
			foreach ($imas as $keyIma) {
				// $colsCount++;
				$bg = 'default';
				$stdMark = $keyIma->getContinuousAssessment2s($keySrd2->srd2SemregRel->semreg_std_relId)->one();
				$mark = 0;
				if ($keyIma->ima_assessed == 1) {
					if ($stdMark && (!is_null($stdMark->ca_markObtained))) {
						$mark = sprintf("%0.2f", $stdMark->ca_markObtained);
						$totalObtained += $stdMark->ca_markObtained;
						$dt = date_format(date_create($stdMark->ca_created_at), "D, d M/Y");
						$by = $stdMark->caCreatedBy->getFullName();
						$title = "Assessed On {$dt} by {$by}";
						$markedSome = true;
					} else {
						$jumped = true;
						$bg = 'yellow';
						$title = 'Missed Assessment';
						$mark = '-';
						$message .= "" . $keyIma->ima_instanceName . ", ";
					}
					$totalAssessed += $keyIma->ima_possiblePoint;
				} else {
					$title = 'Not Assessed Yet';
					$bg = 'warning';
					$mark = '-';
				}
				$marks .= "<td class='bg-{$bg}' title='{$title}'>{$mark}</td>";
			}
		}

		if ($totalAssessed >= 100) {
			$gradeObj = $contAssObj->setLetterGrade($totalAssessed, $totalObtained, $student->id, $coffDetModel->id, $keySrd2->id, $jumped, $markedSome);
			if ($gradeObj && !is_string($gradeObj)) {
				$title2 = "Grade updated";
				$bg2 = 'success';
				$message = "";
			} else {
				$message = str_split($message, strlen($message)-2)[0]."]";
				$title2 = $gradeObj;
				$bg2 = 'error';
			}
			$grade = $keySrd2->srd2GradeRel ? \backend\models\GradeScale::findOne($keySrd2->srd2_grade_relId)->gs_ltr_grade : "-";
			$prntGradeList = !$print ? '' : "<button class='btn btn-xs btn-warning pull-right' title='Print grade report' onclick='gradeReport({$coffDetModel->id});return false;'>Print</button>";
			$totalObtained = $jumped ? '-' : $totalObtained;
		} else {
			$grade = "N/C";
		}

		$totalObtained = is_numeric($totalObtained) ? sprintf("%0.2f", $totalObtained) : $totalObtained;

		$body .= "<tr style='text-align:center'>
				<td style='text-align:left;'>" . ++$count . "</td>
				<td style='text-align:left;'>{$student->std_regId}</td>
				<td style='text-align:left;'>{$student->getFullName()}</td>
				{$marks}
				<td class='bg-{$bg2}' style='font-weight:bold;'>{$totalObtained}</td>
				<td class='bg-{$bg2}' style='background-color:#ffd;' title='{$title2}: {$message}'>{$grade}</td>
			</tr>
			";
	}
	$body = "<tbody>{$body}</tbody>";

	$body .= __display_footer($srd2s, $coffDetModel, $prntGradeList);

	return $body;
}

function __display_footer($srd2s, $coffDetModel, $prntGradeList)
{
	$hod = $coffDetModel->coff2detCourseRel->courseDeptRel->hod;
	$hod = $hod ? $hod->dhInstRel->getFullName() : '-';
	// $gradeSummery = __drow_grade_summery_table($gradesArray);
	$ff = $coffDetModel->coff2detInstRel ? $coffDetModel->coff2detInstRel->getFullName() : 'UKNOWN_INST.';
	$statement = "
			<div class='container' style='border-top:1px solid black;border-bottom:1px solid black;'>
				<br>
				Instructor: <u><i>{$ff}</i></u>&nbsp;&nbsp;&nbsp;
				Signature: _______________&nbsp;&nbsp;&nbsp;
				Date: ____________________<br><br>

				Department Head: <u><i>{$hod}</i></u>&nbsp;&nbsp;&nbsp;
				Signature: _______________&nbsp;&nbsp;&nbsp;
				Date: _______________<br>
				<br>
			<div>
		";
	return "<tfooter>
				<table cellpadding='5' cellspacing='3'>
					<tr>
						<td style='padding-left:5px;'>
							{$statement}
						</td>
					</tr>
				</table>
				{$prntGradeList}
			</tfooter>
		";
}

function __display_header($coffDetModel, $assessmentCategories)
{
	$colsCount = 0;

	$keyAssCats = "";
	foreach ($assessmentCategories as $keyAssessmetnCategory) {
		$imas = $keyAssessmetnCategory->instanceMarkAssignments;
		foreach ($imas as $keyIma) {
			$colsCount++;
			$keyAssCats .= "<td>{$keyIma->ima_instanceName}<br><samll>{$keyIma->ima_possiblePoint}%</small></td>";
		}
	}
	return "
			<thead style='background-color:#CEE;font-weight:bold;'>
				<tr style='text-align:center;'>
					<td rowspan='2'>#</td>
					<td colspan='2'>Student Information</td>
					<td colspan='{$colsCount}'>Assessment Detail</td>
					<td rowspan='2'>Total</td>
					<td rowspan='2'>Grade</td>
				</tr>
				<tr style='text-align:center; font-size:8pt;'>
					<td>Reg. ID</td>
					<td>Full Name</td>
					{$keyAssCats}
				</tr>
			</thead>
		";
}


function __display_caption($coffDetModel, $print)
{
	$EditableBtn = "";
	$pintBtn = $print ? "<button class='btn btn-xs btn-warning pull-right' title='Print this mark sheet' onclick='markSheet({$coffDetModel->id});return false;'>&nbsp; &nbsp; Print&nbsp; &nbsp;  </button>" : '';
	if ((($coffDetModel->coff2detInstRel && $coffDetModel->coff2detInstRel->inst_user_relId == Yii::$app->user->identity->id) || \Yii::$app->user->can('DepartmentHead')) && $print) {
		$EditableBtn = "<button class='btn btn-xs btn-info pull-right' title='Update entry on mark sheet' onclick='editMarkSheet({$coffDetModel->id});return false;'> &nbsp; &nbsp; Edit &nbsp;</button> ";
	}
	$gset = $coffDetModel->coff2detCoff2Rel->coff2GroupRel;
	$header = $print ? "<h3>" . strtoupper(Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "</h3>" : '';
	$ff = $coffDetModel->coff2detInstRel ? $coffDetModel->coff2detInstRel->getFullName() : 'UKNOWN_INST.';
	return "
		<caption style='font-weight: normal; font-size: 10pt; color:#000;'>
			<center>
				{$header}
				<h4>Mark Sheet Report</h4>
				Department: <b><u><i>{$gset->gsDeptRel->dept_name}</i></u></b>&nbsp;&nbsp;
				Program: <b><u><i>{$gset->gsAdmRel->admission_name}-{$gset->gsProgRel->program_name}</i></u></b><br>
				Course Title: <b><u><i>{$coffDetModel->coff2detCourseRel->course_title}&nbsp;&nbsp;[{$coffDetModel->coff2detCourseRel->getCourseCredits()}]</i></u></b>&nbsp;&nbsp;
				Instructor Name: <b><u><i>{$ff}</i></u></b><br>
				Academic Year: <b><u><i>{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}</i></u></b>&nbsp;&nbsp;
				Group: <b><u><i>{$gset->gsGroupRel->group_name}</i></u></b>&nbsp;&nbsp;
				Class Level: <b><u><i>{$gset->gsClslvlRel->getClslvlDisp()}</i></u></b><br>
			</center><p class='pull-right'>{$pintBtn}&nbsp;&nbsp;&nbsp;{$EditableBtn}</p>
		</caption>
	";
}
