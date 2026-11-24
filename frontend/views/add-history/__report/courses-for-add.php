<?php
// global $singleModel;
if (isset($catalog) && !empty($catalog)) {
	$singleModel = new \backend\models\AddHistory();
	$singleModel->addPossible($studentObject);
	if (isset($print)) {
		// code...
	} else {
		$print = true;
	}
	if (!$singleModel->possible) {
		echo $singleModel->message;
		exit();
	}
	if (is_string($catalog)) {
		echo "<p class='bg-warning'>{$catalog}</p>";
	} else {
		echo "<div style='text-align:center; border-bottom: 1px solid black;'>
				<h4>Course Catalog and Student Semester Registration History</h4>
				<h5>
					Student Name : <b><u>{$studentObject->getFullName()}</u></b>, 
					Reg. ID: <b><u>{$studentObject->std_regId}</u></b>, 
					Section: <b><u><i>{$studentObject->stdGroupRel->gsGroupRel->group_name}</i>: {$studentObject->stdGroupRel->getGsDisp()}</u></b>
				</h5>
				<center>
					<small class='bg-info'>
						Creadits Collected In This Semester: <b><i>{$singleModel->collectedCredit}</i></b>, 
						Credit Hour to Enroll: [<b><i>{$singleModel->minCredit}-{$singleModel->maxCredit}</i></b>],
						Add Approval Pending Credits: <b><i>{$singleModel->pendingCredit}</i></b>,
						Free Credit Hrs: <b><i>{$singleModel->freeCreditHrs}</i></b>,
					</small>
				</center>
			</div>
			<div style='max-height: 400px; overflow-y:auto;'>
			<table class='table table-condensed table-sm table-bordered'>";
		echo "<caption style='text-align:center; color:#000; font-weight:normal;'>
			</caption>";
		echo "<thead>
				<tr>
					<th rowspan=2>#</th>
					<th rowspan=2>Course</th>
					<th rowspan=2>Credit Hr.</th>
					<th colspan=2 style='text-align:center'>Registration History</th>
					<th rowspan=2 style='text-align:center'>Option and/ Recommendations</th>
					<th rowspan=2>Remark</th>
				</tr>
				<tr style='font-size:9pt;'>
					<th>Academic Year</th>
					<th>Grade Obtained</th>
				</tr>
			</thead><tbody>";
		$count = 0;
		$displayedCourseList = [];
		// var_dump($catalog);die();
		foreach ($catalog as $gsetId => $keyCatalogValues) {
			$gsetObj = \backend\models\WGroupSetup::findOne($gsetId);
			if (is_array($keyCatalogValues)) {
				foreach ($keyCatalogValues as $keyCatalog2) {
					if (!is_array($keyCatalog2)) {
						continue;
					} else {
						foreach ($keyCatalog2 as $keyCatalog) {
							if (isset($keyCatalog->cocat_course_relId) && in_array($keyCatalog->cocat_course_relId, $displayedCourseList)) {
								continue;
							} else {
								$displayedCourseList[] = $keyCatalog->cocat_course_relId;
							}

							$elective = $keyCatalog->cocat_courseelectives ? "<b>(Elective)</b>" : "";
							$offeringDetails = $keyCatalog->courseOfferingDetails;
							$course = $keyCatalog->cocatCourseRel;
							$srd2s = $keyCatalog->getSemesterRegistrations()
								->andFilterWhere([
									'semreg_std_relId' => $studentObject->id,
									'srd2_course_relId' => $course->id,
								])
								->all();

							$semregEntries = __tablizeSemregs($srd2s, $course, $studentObject, $print, $gsetObj, $singleModel); //[0=>count,1=>TDs]

							echo "<tr>
									<td>" . ++$count . "</td>
									<td>{$course->course_title} [{$course->course_code}]{$elective}</td>
									<td>{$course->getCourseCredits()}</td>
									<td colspan=2>{$semregEntries[1]}</td>
									<td style='white-space:break-word; font-size:8pt;'>{$semregEntries[2]}</td>
									<td></td>
								</tr>";
						}
					}
				}
			} else {
				continue;
			}
		}
		echo "</table></div>";
	}
}

function __checkifAddRequested($keyCoff2det, $studentObject)
{
	return \backend\models\AddHistory::findOne([
		'ah_course_relId' => $keyCoff2det->coff2det_course_relId,
		'ah_std_relId' => $studentObject->id,
		'ah_acadYear_relId' => $keyCoff2det->coff2detCoff2Rel->coff2_acadYear_relId,
		'ah_sem_relId' => $keyCoff2det->coff2detCoff2Rel->coff2_sem_relId
	]);
}

function __tablizeOfferingDetails($offeringDetails, $studentObject, $print = NULL, $freeCreditHrs)
{
	$count = 0;
	$tds = "";
	$requested = false;
	$addBtn = "";
	$actionName = 'Course Add';
	$open = NULL;
	$action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
	if ($action && Yii::$app->user->can('Student')) { //check schedule if STUDENT && such activity is configured to be scehduled.
		$open = \common\components\CommonControllerFunctions::getScheduleForAction($studentObject->stdGroupReal, $action->id);
	}
	// return [0,""];
	if ($offeringDetails && count($offeringDetails) > 0) {
		$requestedForAdd = false;
		foreach ($offeringDetails as $keyCoff2det) {
			$count++;
			$inst = $keyCoff2det->coff2det_inst_relId ? $keyCoff2det->coff2detInstRel->getFullName() : "-";
			$exists = __checkifAddRequested($keyCoff2det, $studentObject);
			if ($exists) {
				$requested = true;
				$addBtn = $exists->ah_coff_relId ==  $keyCoff2det->id ? "Requested" : '';
				return "Requested for add";
			} else {
				// chech for colected credits in this semester, and the schedule:
				if (Yii::$app->user->can('Student') && $print && !$requested) {
					if ($open && count($open) > 0) {
						// check for max credit:
						if ($keyCoff2det->coff2detCourseRel->course_crdthr > $freeCreditHrs) {
							$addBtn = "Can NOT add this course, Max Credit Hr. Limit";
						} else {
							$addBtn = "<button class='btn btn-xs btn-primary' title='Add course with the group?' onclick='requestAdd({$keyCoff2det->id},{$studentObject->id});return false'>Add Course</button>";
						}
					} else {
						$addBtn = "Schedule is NOT open course adding.";
					}
				} else {
					// && ($exists->ah_coff_relId ==  $keyCoff2det->id) ?
					$addBtn = $requested ?  "Requested" : "";
				}
			}

			$tds .= "<div class='container-fluid' style='border-bottom:1px solid black;'>
					<div class='row' style='padding:5px; background-color: #EFDDFE'>
						<div class='col-lg-2'>
							{$keyCoff2det->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}-{$keyCoff2det->coff2detCoff2Rel->coff2SemRel->semester_name}
						</div>
						<div class='col-lg-4'>
							<i>{$keyCoff2det->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}</i>-{$keyCoff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}
						</div>
						<div class='col-lg-3'>
							<b>{$inst}</b>
						</div>
						<div class='col-lg-3'>
							{$addBtn}
						</div>
					</div>
				</div>";
		}
	}

	if ($count == 0) {
		return "<span style='text-style:italic'>Course NOT offered in this semester</span>";
	} else {
		return $tds;
	}
}

function __tablizeSemregs($srd2s, $course, $studentObject, $print, $gsetObj, $singleModel)
{
	$tds = "";
	$recommend = "";
	// $freeCreditHrs = $singleModel->freeCreditHrs;
	// $srd2s = $semregs[0]->getSemesterRegistrations()->andFilterWhere(['srd2_course_relId' => $course->id])->all();
	if ($srd2s && count($srd2s) > 0) {
		foreach ($srd2s as $keySrd2) {
			$gsetObj = $keySrd2->srd2GroupRel;
			$rptFlg = $keySrd2->srd2_corptd_flag ? "*" : '';
			$grade = '';
			if ($keySrd2->srd2_evaluationCompleted) {
				if ($keySrd2->srd2_grade_relId) {
					$grade = $keySrd2->srd2GradeRel->gs_ltr_grade;
				} else {
					$grade = 'NE';
				}
			} else {
				if (\Yii::$app->user->can('Admin') || \Yii::$app->user->can('Registrar')) {
					if ($keySrd2->srd2_grade_relId) {
						$grade = $keySrd2->srd2GradeRel->gs_ltr_grade;
					} else {
						$grade = 'NE';
					}
				} else {
					$grade = 'NE';
				}
			}
			// $grade =  ? ():() ? ( ?  : "-"):'';
			$tds .= "<div class='container-fluid' style='border-bottom:1px solid black;'>
					<div class='row'>
						<div class='col-lg-6'>
							{$keySrd2->srd2SemregRel->semregAcadYearRel->sch_year_desc}-{$keySrd2->srd2SemregRel->semregSemesterRel->semester_name}
						</div>
						<div class='col-lg-6'>
							<i>{$grade}{$rptFlg}
						</div>
					</div>
				</div>";
			if ($keySrd2->srd2_grade_relId && $keySrd2->srd2GradeRel->gs_clearable) {
				// 1. If GC student allow adding clearable grades
				// cehck if the student is GraduatingClass
				if ($keySrd2->srd2SemregRel->semregStdRel->stdGroupRel->gsActiveProgRel->graduate_class) {
					$recommend .= __checkOffering($keySrd2, $course, $studentObject, $print, $gsetObj, $singleModel);
				}

				// cehck if gradePoitnt <2.0, then allow.
				elseif ($keySrd2->srd2GradeRel->gs_gradePoint < 2) { //clerable and less than C
					$recommend .= __checkOffering($keySrd2, $course, $studentObject, $print, $gsetObj, $singleModel);
				}
				// check rept status:
				// check if offered in this semester:
			}

			// 2. If catalog[minPassGrade] is set and not achieved by the student, allow adding
			elseif ($keySrd2->catalogMinPassGradeNotAchieved()) { //srd2->grade < $catalog->minPassgrade
				$recommend .= __checkOffering($keySrd2, $course, $studentObject, $print, $gsetObj, $singleModel);
			}
		}
	} else {
		$tds .= "<div class='container-fluid'>
				<div class='row'>
					<div class='col-lg-12 bg-warning'>
						Course NOT enrolled
					</div>
				</div>
			</div>";
		$recommend .= __checkOffering($srd2s, $course, $studentObject, $print, $gsetObj, $singleModel);
	}

	return [1, $tds, $recommend];
}

function __clearedGrade($keySrd2)
{
	return $keySrd2->clearedFailGrade();
}

function __checkOffering($keySrd2 = null, $course, $studentObject, $print, $gsetObj, $singleModel)
{
	$keySrd2 = $keySrd2 ? $keySrd2 : new \backend\models\SemesterRegistration2Detail();

	// check if cleared grade:
	if ($keySrd2->srd2_grade_relId) {
		if (__clearedGrade($keySrd2)) {
			return "Cleared";
		}
	}
	$preModel = new \backend\models\PreRequisitController();
	$safe = $preModel->safeOfPrerequisit($course, $studentObject, $studentObject->stdGroupReal);
	// var_dump($safe);
	// if($course->course_code == "ECNG 4111"){
	// 	var_dump($safe);exit;
	// }
	if (is_array($safe) && $safe[0] == TRUE) {
		// var_dump("{$course->course_title} => {$safe[0]}<br>");
		// check if the course is offered in enrollable way:
		$offeringForGroup = $keySrd2->checkIfOfferedForThisSemester($course, $studentObject);
		if ($offeringForGroup && count($offeringForGroup) > 0) { //&& is_null($keySrd2)
			// check prev. year and semester of registration:
			// return "<span class='bg-success'>Course can be enrolled in this semester</span>";

			if (!is_null($keySrd2->srd2CourseRel)) {
				if ((
					$singleModel->acadYearId != $keySrd2->srd2SemregRel->semreg_acadYear_relId || 
					$singleModel->semId != $keySrd2->srd2SemregRel->semreg_semester_relId)) {
					// code...
					return "Course NOT offered for this academic year";
				} else {
					return "<span class='bg-success'>Course can be enrolled in this semester</span>";
				}
			} else {
				return "<span class='bg-success'>Course can be enrolled in this semester</span>";
			}
		}

		$offeringInThisSemester = $keySrd2->getActiveOffering($course, $studentObject);

		if ($offeringInThisSemester && count($offeringInThisSemester) > 0) {
			return __tablizeOfferingDetails($offeringInThisSemester, $studentObject, $print, $singleModel->freeCreditHrs);
		} else {
			return "<span class='bg-warning'>Not available for adding</span>";
		}
	} else {
		return $safe[1];
	}
}
