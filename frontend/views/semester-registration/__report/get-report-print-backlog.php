<?php 

if ($model && count($model) > 0) {
	// $course = ;
	echo "<table class='table table-condensed table-bordered table-sm'>";
	echo "<caption style='color:#000; font-weight:normal; font-size: 10pt;'>
		<center><u><h4>Semester Registration Back-Log History</h4></u></center><br>";	
	echo "</caption>";
	echo __displayHeader();
	echo "<tbody>";
	echo __displayBody($model);
	echo "</tbody>";
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}

function __displayBody($model){
	$report = "";
	$count = 0;
	$modelGroupAssignment = new \backend\models\WGroupAssignment();
	$statusArray = [];
	$alreadyDisplayed = [];
	foreach ($model as $semRegs) {

		//bypass repeated student report displays :
		if (in_array($semRegs->semreg_std_relId, $alreadyDisplayed)) {
			continue;
		}else{
			$alreadyDisplayed[] = $semRegs->semreg_std_relId;
		}
		$gass = $modelGroupAssignment->getStudentGroupAssignment($semRegs->semregAcadYearRel,$semRegs->semregSemesterRel,$semRegs->semregStdRel);

		if ($gass) {
			$gs = $gass->gasGsetRel;
		}else{
			$report .= 
			"<tr>
				<td>".++$count."</td>
				<td>{$semRegs->semregStdRel->getFullName()}</td>
				<td>{$semRegs->semregAcadYearRel->sch_year_desc}-{$semRegs->semregSemesterRel->semester_name}</td>
				<td colspan='5' class='bg-warning'><span style='text-align:center;'>Group assignment entry NOT found.</span></td>
			</tr>";

			continue;
		}
		
		$clslvl = $gs->gsClslvlRel->getClslvlDisp();
		$p = $gs->gsAdmRel->admission_name.'-'.$gs->gsProgRel->program_name;
		$spec = isset($gs->gsSpecRel) ? $gs->gsSpecRel->spec_name : '';
		// $lastStatus = $semRegs->semesterLastSsnRel ? $semRegs->semesterLastSsnRel->sssn_name : '-';
		// isset($statusArray[$lastStatus]) ? $statusArray[$lastStatus]++ : ($statusArray[$lastStatus] = 1);
		$fNgs = getFNgs($semRegs,$gs);
		if (is_null($fNgs) || $fNgs[0] == false) {
			continue;
		}
		$report .= 
			"<tr>
				<td>".++$count."</td>
				<td>{$semRegs->semregStdRel->std_regId}</td>
				<td>{$semRegs->semregStdRel->getFullName()}</td>
				<td>{$p}</td>
				<td>{$gs->gsDeptRel->dept_name}</td>
				<td>{$spec}</td>
				<td>{$fNgs[1]}</td>
			</tr>";
	}
	return $report;
}

function getFNgs($semRegs,$gs){
	$gAssignmentModel = new \backend\models\WGroupAssignment();
	$studentObj = $semRegs->semregStdRel;
	$max  = "<table class='table table-condensed table-bordered table-sm table-hover' style='font-size:8pt;'>";
	$max  .= "<thead>
				<th>#</th>
				<th>Course</th>
				<th>Credit Hrs.</th>
				<th>Registration Entry and Grade</th>
				<th>Recommendations</th>
				<th>Remark</th>
			</thead><tbody>";
        
    $studentObject = $semRegs->semregStdRel;
    $catalog = $gAssignmentModel->getCatalogEntries($studentObject);
    $count = 0;
    $recordExists = false;
	$displayedCourseList =[];
	foreach ($catalog as $gsetId => $keyCatalogValues) {
		$gsetObj = \backend\models\WGroupSetup::findOne($gsetId);
		if (is_array($keyCatalogValues)) {
			foreach ($keyCatalogValues as $keyCatalog2) {
				if (!is_array($keyCatalog2)) {
						continue;
					}else{
						foreach ($keyCatalog2 as $keyCatalog) {
							if (isset($keyCatalog->cocat_course_relId) && in_array($keyCatalog->cocat_course_relId, $displayedCourseList)) {
								continue;
							}else{
								$displayedCourseList[] = $keyCatalog->cocat_course_relId;
							}

							$elective = $keyCatalog->cocat_courseelectives ? "<b>(Elective)</b>" : "";
							// $offeringDetails = $keyCatalog->courseOfferingDetails;
							$course = $keyCatalog->cocatCourseRel;
							$srd2s = __getRegistrationdetailEntry($course,$studentObject,$gsetObj);

							$semregEntries = __tablizeSemregs($srd2s,$course,$studentObject,true,$gsetObj);//[0=>count,1=>TDs]

							if (empty($semregEntries[1])) {
								continue;
							}else{
								$recordExists = true;
								$max .= "<tr>
									<td>".++$count."</td>
									<td>{$course->course_title} [{$course->course_code}]{$elective}</td>
									<td styele='white-space:pre-line;'>{$course->getCourseCredits()}</td>
									<td>{$semregEntries[1]}</td>
									<td>{$semregEntries[2]}</td>
									<td></td>
								</tr>";
							}
						}
					}
				}
			}else{
				continue;
			}
		}

    return [$recordExists,$max."</tbody></table>"];
}

function __checkifAddRequested($keyCoff2det,$studentObject){
	return \backend\models\AddHistory::findOne([
		'ah_course_relId' => $keyCoff2det->coff2det_course_relId,
		'ah_std_relId' => $studentObject->id,
		'ah_approved' => NULL,
	]);
}

function __tablizeOfferingDetails($offeringDetails,$studentObject,$print=NULL){
	$count = 0;
	$tds = "";
	$requested = false;
	$addBtn = "";
	// return [0,""];
	if ($offeringDetails && count($offeringDetails) > 0) {
		foreach ($offeringDetails as $keyCoff2det) {
			$count++;
			$inst = $keyCoff2det->coff2det_inst_relId ? $keyCoff2det->coff2detInstRel->getFullName() : "-";
			$exists = __checkifAddRequested($keyCoff2det,$studentObject);
			if($exists){
				$requested = true;
				$addBtn = $exists->ah_coff_relId ==  $keyCoff2det->id ? "Requested" : '';
			}else{

				// chech for colected credits in this semester:
				if (Yii::$app->user->can('Student') && $print && !$requested) {
					$addBtn = "<button class='btn btn-xs btn-primary' title='Add course with the group?' onclick='requestAdd({$keyCoff2det->id},{$studentObject->id});return false'>Add Course</button>";
				}else{
					$addBtn = $requested && $exists && ($exists->ah_coff_relId ==  $keyCoff2det->id) ? "Requested":"";
				}
			}
			
			$tds .= "<div class='container-fluid' style='border-bottom:1px solid black; font-size:7pt;'>
					<div class='row' style='padding:5px; background-color: #EFDDFE'>
						<div class='col-lg-4'>
							{$keyCoff2det->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc}-{$keyCoff2det->coff2detCoff2Rel->coff2SemRel->semester_name}
						</div>
						<div class='col-lg-4'>
							<i>{$keyCoff2det->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}</i>-{$keyCoff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}
						</div>
						<div class='col-lg-4'>
							<b>{$inst}</b>
						</div>
					</div>
				</div>";
		}
	}

	if ($count == 0) {
		return "<span style='text-style:italic'>Course NOT offered in this semester</span>";
	}else{
		return $tds;
	}
}

function __checkOffering($keySrd2=null,$course,$studentObject,$print,$gsetObject){
	$keySrd2 = $keySrd2 ? $keySrd2 : new \backend\models\SemesterRegistration2Detail();

	// check if cleared grade:
	if ($keySrd2->srd2_grade_relId) {
		if (__clearedGrade($keySrd2)) {
			return "Cleared";
		}
	}
	$preModel = new \backend\models\PreRequisitController();
	$safe = $preModel->safeOfPrerequisit($course,$studentObject,$gsetObject);
	if (is_array($safe) && $safe[0] == TRUE) {
		$offeringInThisSemester = $keySrd2->getActiveOffering($course,$studentObject);

		if ($offeringInThisSemester && count($offeringInThisSemester) > 0) {
			return __tablizeOfferingDetails($offeringInThisSemester,$studentObject,$print);
		}else{
			return "Not available for adding";
		}	
	}else{
		return $safe[1];
	}

}

function __tablizeSemregs($srd2s,$course,$studentObject,$print,$gsetObject){
	$tds ="";
	$recommend = "";
	// $srd2s = $semregs[0]->getSemesterRegistrations()->andFilterWhere(['srd2_course_relId' => $course->id])->all();
	if ($srd2s && count($srd2s) > 0) {
		foreach ($srd2s as $keySrd2) {
			if ($keySrd2->srd2_grade_relId) {
				if (in_array($keySrd2->srd2GradeRel->gs_ltr_grade, ['F','NG','I'])) {
					if (__clearedGrade($keySrd2)) {
						continue;
					}
				}else{
					continue;
				}
			}else{
				continue;
			}
			
			$rep = "";
			if ($gsetObject->id != $keySrd2->srd2_group_relId) {
				$rep = "Enrolled In other Group";
			}
			$rptFlg = $keySrd2->srd2_corptd_flag ? "*":'';
			$grade = $keySrd2->srd2_grade_relId ? $keySrd2->srd2GradeRel->gs_ltr_grade : "-";
			$tds .= "<div class='container-fluid' style='border-bottom:1px solid black;'>
					<div class='row'>
						<div class='col-lg-8' title='{$rep}'>
							{$keySrd2->srd2SemregRel->semregAcadYearRel->sch_year_desc}-{$keySrd2->srd2SemregRel->semregSemesterRel->semester_name}
						</div>
						<div class='col-lg-4'>
							<i>{$grade}{$rptFlg}
						</div>
					</div>
				</div>";
			if ($keySrd2->srd2_grade_relId && !$keySrd2->srd2_corptd_flag && $keySrd2->srd2GradeRel->gs_clearable) {
				// check rept status:
				// check if offered in this semester:
				$recommend .= __checkOffering($keySrd2,$course,$studentObject,$print,$gsetObject);
			}
		}
	}else{
		// check if offered for this semester
		$keySrd2 = new \backend\models\SemesterRegistration2Detail();
		$offeringForGroup = $keySrd2->checkIfOfferedForThisSemester($course,$studentObject);
		if ($offeringForGroup && count($offeringForGroup) > 0) {
			return [1,"",$recommend];
		}
		$tds .= "<div class='container-fluid'>
				<div class='row'>
					<div class='col-lg-12 bg-warning'>
						Course NOT enrolled
					</div>
				</div>
			</div>";
		$recommend .= __checkOffering($srd2s,$course,$studentObject,$print,$gsetObject);
	}

	return [1,$tds,$recommend];
}

function __getRegistrationdetailEntry($courseObj,$studentObject,$gsetObject){
	return \backend\models\SemesterRegistration2Detail::find()
		->innerJoin('semester_registration_2','semester_registration_2.id=srd2_semreg_relId')
		->where([
			'srd2_course_relId' => $courseObj->id,
			'semreg_std_relId' => $studentObject->id,
		])
		->all();
}

function __clearedGrade($keySrd2){
	return $keySrd2->find()
		->innerJoin('grade_scale','grade_scale.id=srd2_grade_relId')
		->innerJoin('semester_registration_2','semester_registration_2.id=srd2_semreg_relId')
		->where([
			'srd2_course_relId' => $keySrd2->srd2_course_relId,
			'semreg_std_relId' => $keySrd2->srd2SemregRel->semreg_std_relId,
		])
		->andWhere('gs_pass_status <> 0 AND gs_ltr_grade <> "F" AND semester_registration_2_detail.id <> '.$keySrd2->id)
		->count() > 0;
}


function __displayHeader(){
	return "
		<thead>
			<th>#</th>
			<th>ID No.</th>
			<th>Student Name</th>
			<th>Program</th>
			<th>Department</th>
			<th>Specialization</th>
			<th>Fs & NGs</th>
		</thead>
		";
}

?>