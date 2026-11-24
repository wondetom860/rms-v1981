<?php
/* 
 * This form generates Student copy for the selected student.
 * It has default passed argeuments wich holds POST data.
 * @WONDE-TOM
 * 
 */

$this->title = 'Student Copy';

if (!(isset($userModel) || isset($studentModel) || isset($semregData))) {
	echo "
		<div class='alert alert-error'>
			Error, Uknown Input for Generating Student Copy. Please retry.
		</div>
	";
	die();
}

function displayCourseDetailsHeader()
{ //Header I think is fixed.
	return '
		<tr style="font-weight: normal;">

			<td colspan="2"  style="border: 1px solid black;">Course Code</td>
			<td colspan="4"  style="border: 1px solid black;">Course Title</td>
			<td colspan="1"  style="border: 1px solid black;">Cr. Hrs.</td>
			<td colspan="1"  style="border: 1px solid black;">Grade</td>
			<td colspan="1"  style="border: 1px solid black; border-right: 2px solid black;">Gr. Pts</td>

			<td colspan="2"  style="border: 1px solid black;">Course Code</td>
			<td colspan="4"  style="border: 1px solid black;">Course Title</td>
			<td colspan="1"  style="border: 1px solid black;">Cr. Hrs.</td>
			<td colspan="1"  style="border: 1px solid black;">Grade</td>
			<td colspan="1"  style="border: 1px solid black;">Gr. Pts</td>

		</tr>
	';
}

function displaySemesterHeader($semester1, $semester2)
{ //most actually from semregObject

	// Incase if the student haven.t completed semester registration!
	$acadYear1 = empty($semester1) ? 'Uknown' : \backend\models\SchoolYear::findOne($semester1['semreg_acadYear_relId'])->sch_year_desc;
	$acadYear2 = empty($semester2) ? 'Uknown' : \backend\models\SchoolYear::findOne($semester2['semreg_acadYear_relId'])->sch_year_desc;
	return '
		<tr style="font-weight: bold; vertical-align: middle;">

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle; border-right: 1px solid black; text-align:center;"><u>First Semester - ' . $acadYear1 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 2px solid black;"></td>

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle; border-right: 1px solid black; text-align:center;"><u>Second Semester - ' . $acadYear2 . '<br><br></u></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>

		</tr>
	';
}


function displaySemesterFooter($semCourseDisplay, $semester1, $semester2)
{ //and this is from semregModel
	$crdtSum1 = $semCourseDisplay[1];
	$crdtSum2 = $semCourseDisplay[2];
	$gpointSum1 = $semCourseDisplay[3];
	$gpointSum2 = $semCourseDisplay[4];

	$CGPA1 = $semester1['semreg_CGPA'] == 0.00 ? '-' : round($semester1['semreg_CGPA'], 2);
	$CGPA2 = $semester2['semreg_CGPA'] == 0.00 ? '-' : round($semester2['semreg_CGPA'], 2);

	$SGPA1 = $semester1['semreg_SGPA'] == 0.00 ? '-' : round($semester1['semreg_SGPA'], 2);
	$SGPA2 = $semester2['semreg_SGPA'] == 0.00 ? '-' : round($semester2['semreg_SGPA'], 2);

	// $SGPA1 = is_numeric($gpointSum1) && $gpointSum1 != 0 ? (round(($gpointSum1 / $crdtSum1), 2)) : ' - ';
	// $SGPA2 = is_numeric($gpointSum2) && $gpointSum2 != 0 ? (round(($gpointSum2 / $crdtSum2), 2)) : ' - ';

	// $grpt1 = 
	return '
	<tr style="font-weight: bold; vertical-align: middle; text-align: center;">

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="text-align: center; border-right: 1px solid black; border-top: 1px solid black;">' . $crdtSum1 . '</td>
			<td colspan="1" style="text-align: center; border-right: 1px solid black; border-top: 1px solid black;">' . $SGPA1 . '</td>
			<td colspan="1" style="text-align: center; border-right: 2px solid black; border-top: 1px solid black;">' . $gpointSum1 . '</td>

			<td colspan="2" style="border-right: 1px solid black; "></td>
			<td colspan="4" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $crdtSum2 . '</td>
			<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $SGPA2 . '</td>
			<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $gpointSum2 . '</td>

		</tr>
		<tr style="font-weight: bold; vertical-align: middle;">

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br>Semester Average: ' . $SGPA1 . ' <br>Commulative Average: ' . $CGPA1 . '<br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 2px solid black;"></td>

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br>
				Semester Average:' . $SGPA2 . '<br>
				Commulative Average: ' . $CGPA2 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>

		</tr>
	';
}

?>

<div class="container-fluid wrap" style="background-color: white;" style="margin-top: 5px;">
	<div class="row">
		<div class="col-md-12">
			<?php
			$stdDept = is_null($studentModel->std_dept_relId) ? 'Uknown' : $studentModel->stdDeptRel->dept_name;
			$spec = is_null($studentModel->std_spec_relId) ? '-' : $studentModel->stdSpecRel->spec_name;
			?>
			<table border="1" cellpadding="2" cellspacing="0" style="font-size: 9pt; width:100%; display: table-header-group;">
				<caption style="font-weight:normal;color:#000;">
					<table border="1" cellpadding="2" cellspacing="0" style="font-size: 9pt; width:100%;">
						<tr style="text-align:center;">
							<td colspan="5" style="padding-left: 1px; border: 0px;border-bottom: 1px solid black;">
								OFFICE OF THE REGISTRAR <br> P.O.Box 1041, Debrezeit, Ethiopia
							</td>
							<td colspan="8" style="padding-left: 3px; border: 0px;border-bottom: 1px solid black;">
								<h3><?= strtoupper(Yii::$app->params['client']) ?><br>STUDENT RECORD</h3>
							</td>
							<td colspan="5" style="padding-left: 2px; border: 0px;border-bottom: 1px solid black;">Degree Awarded date: <b><u>________</u></b></td>
						</tr>
						<tr style="text-align:center; border: 1px;">
							<td colspan="6" style="padding-left: 9px; border: 0px;">
								<?= "College: <span style=\"font-weight: bold; text-decoration: underline;\">" . Yii::$app->params['college'] . "</span>";
								?>
							</td>
							<td colspan="4" style="padding-left: 9px; border: 0px;"><?= "Medium of Instruction: <span style=\"font-weight: bold; text-decoration: underline;\">English</span>" ?></td>
							<td colspan="4" style="padding-left: 9px; border: 0px;">
								<?= "Department: <span style=\"font-weight: bold; text-decoration: underline;\">{$stdDept}</span>" ?>
							</td>
							<td colspan="4" style="padding-left: 9px; border: 0px;"><?= "Major: <span style=\"font-weight: bold; text-decoration: underline;\">{$spec}</span>" ?></td>
						</tr>
					</table>
				</caption>
				<thead>
					<?php
					$userModel->first_name = strtoupper($userModel->first_name);
					$userModel->middle_name = strtoupper($userModel->middle_name);
					$userModel->last_name = strtoupper($userModel->last_name);
					$admission = \backend\models\Admission::findOne($studentModel->std_admission_relId)->admission_name;
					$program = $studentModel->stdProgRel->program_name;
					$dob = date_format(date_create($userModel->dob), 'd-m-Y');
					$admittedFrom = $studentModel->getAdmittedFrom2($studentModel->id);
					$admissionDate = date_format(date_create($studentModel->std_created_at), 'd-m-Y');
					echo "
						<tr>
							<td colspan=\"18\"  style=\"border: 1px solid black;\">&nbsp;&nbsp;&nbsp;
								Name: <span style=\"font-weight: bold;\">{$userModel->first_name}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Father's Name : <span style=\"font-weight: bold;\">{$userModel->middle_name} {$userModel->last_name}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
								Date of Birth :<span style=\"font-weight: bold;\"> {$dob}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Admission Classification : <span style=\"font-weight: bold;\">{$admission} {$program}</span>
							</td>
						</tr>
						<tr style=\"border: 1px solid black;\">
							<td colspan=\"18\"  style=\"border: 1px solid black;\">&nbsp;&nbsp;&nbsp;
								ID. Number: <span style=\"font-weight: bold;\">{$studentModel->std_regId}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Gender : <span style=\"font-weight: bold;\">{$userModel->gender}</span>&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
								Admitted from(Institute / School) :<span style=\"font-weight: bold;\"> {$admittedFrom}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Test Date: <span style=\"font-weight: bold;\">{$admissionDate}</span>
							</td>
						</tr>
						</thead><tbody>
						";
					?>
					<?= displayCourseDetailsHeader() ?>

					<?php
					if ($semregData && count($semregData) > 0) {

						$i = 1;
						$displayedCourseList = [];
						while ($i <= 10) { //max loop would be nOT more than 10
							$clslvl1 = isset($semregData[$i]) ? $semregData[$i] : NULL;
							$clslvl2 = isset($semregData[$i + 1]) ? $semregData[$i + 1] : NULL;
							// $clslvl2 = $semregData[$i+1];    

							$tt = displayColumns($clslvl1, $clslvl2, $displayedCourseList);
							if (is_array($tt)) {
								$displayedCourseList = is_array($tt) ? $tt[1] : $displayedCourseList;
								echo $tt[0];
							} else {
								echo $tt;
							}

							// echo $tt[0];
							$i += 2;
						}

						// 'gs_forProgram'=>$studentModel->std_prog_relId
						$gradeScale = \backend\models\GradeScale::find()
							->where([
								'gs_valid' => 1, 
								'gs_scale_status' => 1,
								'gs_forProgram' => $studentModel->std_prog_relId
								])
							->all();
						$gs = '';
						$gps = '';
						foreach ($gradeScale as $key) {
							$gs .= " {$key->gs_ltr_grade} = [" . sprintf("%0.2f", $key->gs_min) . " - " . sprintf("%0.2f", $key->gs_max) . "], ";
							$gps .= " {$key->gs_ltr_grade} = " . sprintf("%0.2f", $key->gs_gradePoint) . ", ";
						}
						echo "<tr>
                    		<td colspan = '18' style='border-top:2px solid black; font-size:10pt;'><u>Grading System</u>: {$gs}</td>
	                    </tr>
	                    <tr>
	                    		<td colspan = '18' style='font-size:10pt;'><u>Points</u>: {$gps}. A credit hour is defined as one lecture hour or an approprite combination of two to three hours of laboratory, practical work or tutorial sessions per week for the duration of the semester (18 weeks).</td>
	                    </tr>
	                    <tr>
	                    		<td colspan = '18' style='text-align: center;'><b>THE TRANSCRIPT IS OFFICIAL ONLY WHEN SIGNED AND SEALED BY THE REGISTRAR</b></td>
	                    </tr>
	                    <tr>
	                    		<td colspan = '9' style='text-align:center;'>Date of issue: ______________________</td>
	                    		<td colspan = '9' style='text-align:center;'>Registrar: ________________________</td>
	                    </tr>
                    ";
					} else {
						echo "
							<div class='alert alert-error'>
								Error, Semester registration Uknown. Please retry.
							</div>
						";
						die();
					}
					?>
					</h>
					</thea>
			</table>
		</div>
	</div>
</div>

<?php

function displayColumns($clslvl1, $clslvl2, $rowCourses)
{
	// $srd2Model = new Seme
	// display semester and academic year header here
	if (!is_null($clslvl1) || !is_null($clslvl2)) {
		$f1 = !is_null($clslvl1);
		$f2 = !is_null($clslvl2);
		$academicYear1 = $f1 ? array_keys($clslvl1)[0] : NULL;
		$arrayClassLevel1 = $f1 ? $clslvl1[$academicYear1] : NULL;
		$clslvl1 = $f1 ? array_keys($arrayClassLevel1)[0] : NULL;

		$semesterValueArray1 = $f1 ? $arrayClassLevel1[$clslvl1] : NULL;

		$academicYear2 = $f2 ? array_keys($clslvl2)[0] : NULL;
		$arrayClassLevel2 = $f2 ? $clslvl2[$academicYear2] : NULL;
		$clslvl2 = $f2 ? array_keys($arrayClassLevel2)[0] : NULL;
		$semesterValueArray2 = $f2 ? $arrayClassLevel2[$clslvl2] : NULL;

		// echo "<pre>";
		// var_dump($arrayClassLevel1);
		// echo "</pre>";
		// return;

		// $tilikuSemreg = max(count($arrayClassLevel1), count($arrayClassLevel2));
		// 
		$j = 0;
		$courseList = "";
		$tilikuSemreg = $f1 ?
			($f2 ?
				(max(count($arrayClassLevel1), count($arrayClassLevel2))) : (count($arrayClassLevel1))) : (
				$f2 ?
				(count($arrayClassLevel1)) :
				1
			);
		// $displayedCourses = [];
		while ($j < $tilikuSemreg) {

			// $semName1 = $f1 ? array_keys($arrayClassLevel1)[$j] : NULL;
			// $semregObj1 = $f1 ? $arrayClassLevel1[$semName1][0] : NULL;
			// $srd2Obj1 = $f1 ? $arrayClassLevel1[$semName1][1] : NULL;

			$semName1 = $f1 && isset(array_keys($arrayClassLevel1)[$j]) ? array_keys($arrayClassLevel1)[$j] : NULL;
			$semregObj1 = $f1 && isset($arrayClassLevel1[$semName1][0]) ? $arrayClassLevel1[$semName1][0] : NULL;
			$srd2Obj1 = $f1 && isset($arrayClassLevel1[$semName1][1]) ? $arrayClassLevel1[$semName1][1] : NULL;

			// echo "<pre>";
			// var_dump($semesterValueArray1[0]);
			// echo "</pre>";
			// return;

			$semName2 = $f2 && isset(array_keys($arrayClassLevel2)[$j]) ? array_keys($arrayClassLevel2)[$j] : NULL;
			$semregObj2 = $f2 && isset($arrayClassLevel2[$semName2][0]) ? $arrayClassLevel2[$semName2][0] : NULL;
			$srd2Obj2 = $f2 && isset($arrayClassLevel2[$semName2][1]) ? $arrayClassLevel2[$semName2][1] : NULL;

			// $clslvl11 = is_null($semregObj1)?'':'Year - '.$semregObj1->semregClslvlRel->clslvl_num;
			// $clslvl12 = is_null($semregObj2)?'':'Year - '.$semregObj2->semregClslvlRel->clslvl_num;

			$academicYear11 = is_null($semregObj1) ? '' : '- ' . $semregObj1->semregAcadYearRel->sch_year_desc;
			$academicYear12 = is_null($semregObj2) ? '' : '- ' . $semregObj2->semregAcadYearRel->sch_year_desc;

			$sem11 = is_null($semregObj1) ? '' : 'Semester ' . $semregObj1->semregSemesterRel->semester_name;
			$sem12 = is_null($semregObj2) ? '' : 'Semester ' . $semregObj2->semregSemesterRel->semester_name;

			$SGPA1 = is_null($semregObj1) ? '' : sprintf("%0.2f", $semregObj1->semreg_SGPA);
			$SGPA2 = is_null($semregObj2) ? '' : sprintf("%0.2f", $semregObj2->semreg_SGPA);

			$CGPA1 = is_null($semregObj1) ? '' : sprintf("%0.2f", $semregObj1->semreg_CGPA);
			$CGPA2 = is_null($semregObj2) ? '' : sprintf("%0.2f", $semregObj2->semreg_CGPA);

			$courseList .= '<tr style="font-weight: bold; vertical-align: middle;">

				<td colspan="2" style="border-right: 1px solid black;"></td>
				<td colspan="4" style="vertical-align: middle; border-right: 1px solid black; text-align: center;"><u>' . $sem11 . '  ' . $academicYear1 . '</u></td>
				<td colspan="1" style="border-right: 1px solid black;"></td>
				<td colspan="1" style="border-right: 1px solid black;"></td>
				<td colspan="1" style="border-right: 2px solid black;"></td>

				<td colspan="2" style="border-right: 1px solid black;"></td>
				<td colspan="4" style="vertical-align: middle; border-right: 1px solid black; text-align: center;"><u>' . $sem12 . ' ' . $academicYear12 . '</u></td>
				<td colspan="1"style="border-right: 1px solid black;"></td>
				<td colspan="1"style="border-right: 1px solid black;"></td>
				<td colspan="1"style="border-right: 1px solid black;"></td>

			</tr>';
			$i = 0;

			$titlikuLength = max($srd2Obj2 ? count($srd2Obj2) : 1, $srd2Obj1 ? count($srd2Obj1) : 1);

			$gpointSum1 = 0;
			$gpointSum2 = 0;
			$crdtSum1 = 0;
			$crdtSum2 = 0;
			$displayed = false;
			$enrolledThesis = false;
			$lastSemester = NULL;
			$thesisTitle = '';
			// $lastSemester = $srd2Obj2[0]->srd2SemregRel->semregSemesterRel->semester_name == 'II' && $srd2Obj2[0]->srd2SemregRel->semregClslvlRel->clslvl_num == 2;
			$thesis_course_displayed = false;
			// $rowCourses = [];
			// $titlikuLength += $lastSemester ? 1 : 0;
			while ($i < $titlikuLength) {

				$srd1Key = isset($srd2Obj1[$i]) ? $srd2Obj1[$i] : NULL;
				$course1 = !is_null($srd1Key) ? $srd1Key->srd2CourseRel : NULL;
				$grade1 = !is_null($srd1Key) ? $srd1Key->srd2GradeRel : NULL;
				$r1rptd = false;
				if ($srd1Key) {
					if (in_array($srd1Key->srd2_course_relId, $rowCourses)) {
						$r1rptd = true;
						$srd1Key = NULL;
						$course1 =  NULL;
						// continue;
					} else {
						// array_push($rowCourses, $srd1Key->srd2_course_relId);

						// if repeated course: read cleared grade:
						// if ($grade1 && in_array(strtoupper($grade1->gs_ltr_grade), ['F', 'I', 'NG', 'FAIL'])) {
						// 	// look for semester registration where student has cleared the Faile Grade:
						// 	$srd2Cleared = SemesterRegistration2Detail::find()
						// 		->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
						// 		->innerJoin('grade_scale', 'grade_scale.id=semester_registration_2_detail.srd2_grade_relId')
						// 		->where([
						// 			'semreg_std_relId' => $srd1Key->srd2SemregRel->semreg_std_relId,
						// 			'srd2_course_relId' => $srd1Key->srd2_course_relId,
						// 			'gs_pass_status' => 1,
						// 			'srd2_corptd_flag' => 1
						// 		])
						// 		->andWhere(' semester_registration_2_detail.id <> ' . $srd1Key->id)
						// 		->one();
						// 	if ($srd2Cleared) {
						// 		$srd1Key = $srd2Cleared;
						// 		$grade1 = !is_null($srd1Key) ? $srd1Key->srd2GradeRel : NULL;
						// 	}
						// }
					}
				}
				$grade1 = !is_null($srd1Key) ? $srd1Key->srd2GradeRel : NULL;
				$ltrGrade1 = is_null($grade1) ? '' : $grade1->gs_ltr_grade;
				$gPoint1 = is_null($grade1) ? '' : $grade1->gs_gradePoint * $course1->course_crdthr;
				$courseTitle1 = is_null($course1) ? NULL : $course1->course_title;
				$courseCode1 = is_null($course1) ? '' : $course1->course_code;
				$courseCrdt1 = is_null($course1) ? '' : $course1->course_crdthr;


				$srd2Key = isset($srd2Obj2[$i]) ? $srd2Obj2[$i] : NULL;
				$course2 = !is_null($srd2Key) ? $srd2Key->srd2CourseRel : NULL;
				$grade2 = !is_null($srd2Key) ? $srd2Key->srd2GradeRel : NULL;

				$lSem = $srd2Key ? "{$srd2Key->srd2SemregRel->semregSemesterRel->semester_name}-{$srd2Key->srd2SemregRel->semregClslvlRel->clslvl_num}" : '';
				if ($srd2Key) {
					if (in_array($srd2Key->srd2_course_relId, $rowCourses)) {
						if ($r1rptd) {
							$i++;
							$srd2Key = isset($srd2Obj2[$i]) ? $srd2Obj2[$i] : NULL;
							$course2 = !is_null($srd2Key) ? $srd2Key->srd2CourseRel : NULL;
						} else {
							$srd2Key = NULL;
							$course2 = NULL;
						}
					} else {
						// array_push($rowCourses, $srd2Key->srd2_course_relId);

						// if repeated course: read cleared grade:
						// if ($grade2 && $grade2->gs_ltr_grade && in_array(strtoupper($grade2->gs_ltr_grade), ['F', 'I', 'NG', 'FAIL'])) {
						// 	// look for semester registration where student has cleared the Faile Grade:
						// 	$srd2Cleared = SemesterRegistration2Detail::find()
						// 		->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
						// 		->innerJoin('grade_scale', 'grade_scale.id=semester_registration_2_detail.srd2_grade_relId')
						// 		->where([
						// 			'semreg_std_relId' => $srd2Key->srd2SemregRel->semreg_std_relId,
						// 			'srd2_course_relId' => $srd2Key->srd2_course_relId,
						// 			'gs_pass_status' => 1,
						// 			'srd2_corptd_flag' => 1
						// 		])
						// 		->andWhere(' semester_registration_2_detail.id <> ' . $srd2Key->id)
						// 		->one();
						// 	if ($srd2Cleared) {
						// 		$srd2Key = $srd2Cleared;
						// 		$grade2 = !is_null($srd2Key) ? $srd2Key->srd2GradeRel : NULL;
						// 	}
						// }
					}
				}

				$grade2 = !is_null($srd2Key) ? $srd2Key->srd2GradeRel : NULL;
				$ltrGrade2 = is_null($grade2) ? '' : $grade2->gs_ltr_grade;
				$gPoint2 = is_null($grade2) ? '' : $grade2->gs_gradePoint * $course2->course_crdthr;
				$courseTitle2 = is_null($course2) ? NULL : $course2->course_title;
				$courseCode2 = is_null($course2) ? '' : $course2->course_code;
				$courseCrdt2 = is_null($course2) ? '' : $course2->course_crdthr;

				$courseTitle1 .= !is_null($srd1Key) && $srd1Key->srd2_corptd_flag == 1 ? '*' : '';
				$courseTitle2 .= !is_null($srd2Key) && $srd2Key->srd2_corptd_flag == 1 ? '*' : '';
				// $courseTitle2 .= !is_null($srd2Key) && !is_null($srd2Key->srd2_corptd_flag) && !is_null($course2) ? '':'*';

				$enrolledThesis = ($enrolledThesis == false) && ((str_contains(strtoupper($courseTitle1), 'THESIS PHASE II') !== false || str_contains(strtoupper($courseTitle2), 'THESIS PHASE II') !== false) ? true : false);
				$thesisCourse = (str_contains(strtoupper($courseTitle1), 'THESIS PHASE II') !== false || str_contains(strtoupper($courseTitle2), 'THESIS PHASE II') !== false) ? true : false;
				$gpointSum1 += is_numeric($gPoint1) ? $gPoint1 : 0;
				$gpointSum2 += is_numeric($gPoint2) ? $gPoint2 : 0;

				$crdtSum1 += is_null($course1) ? 0 : $course1->course_crdthr;
				$crdtSum2 += is_null($course2) ? 0 : $course2->course_crdthr;

				$gPoint1 = is_numeric($gPoint1) ? sprintf("%0.2f", $gPoint1) : $gPoint1;
				$gPoint2 = is_numeric($gPoint2) ? sprintf("%0.2f", $gPoint2) : $gPoint2;


				$courseList .= "<tr style=\"font-weight: normal; vertical-align: middle;\">

						<td colspan=\"2\" style=\"border-right: 1px solid black; text-align: left; padding-left:16px;\">{$courseCode1}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade1}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 2px solid black;\">{$gPoint1}</td>
						";
				if (!$displayed && $thesis_course_displayed) {
					// display thesis title
					$displayed = true;
					$courseList .= "<td colspan=\"9\" style=\"border-right: 1px solid black; text-align:left; padding-left:16px; font-size: 10pt; font-weight: bold;\">
						<strong>Thesis Title: </strong> {$thesisTitle}
					</td></tr>";
				} else {
					if ($thesisCourse) {
						$thesis_course_displayed = true;
						$thesisTitle = strtoupper(getThesisTitle($srd2Key));
					}
					$courseList .= "<td colspan=\"2\" style=\"border-right: 1px solid black; text-align:left; padding-left:16px;\">{$courseCode2}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade2}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 1px solid black;\">{$gPoint2}</td>

					</tr>";
				}
				$i++;
			}

			// append footer

			$gpointSum1 = is_numeric($gpointSum1) ? sprintf("%0.2f", $gpointSum1) : $gpointSum1;
			$gpointSum2 = is_numeric($gpointSum2) ? sprintf("%0.2f", $gpointSum2) : $gpointSum2;

			$courseList .= '
				<tr style="font-weight: bold; vertical-align: middle; text-align: center;">

					<td colspan="2" style="border-right: 1px solid black;"></td>
					<td colspan="4" style="border-right: 1px solid black; text-align:right">Total</td>
					<td colspan="1" style="text-align: center; border-right: 1px solid black; border-top: 1px solid black;">' . $crdtSum1 . '</td>
					<td colspan="1" style="text-align: center; border-right: 1px solid black; border-top: 1px solid black;">' . $SGPA1 . '</td>
					<td colspan="1" style="text-align: center; border-right: 2px solid black; border-top: 1px solid black;">' . $gpointSum1 . '</td>

					<td colspan="2" style="border-right: 1px solid black; "></td>
					<td colspan="4" style="border-right: 1px solid black; text-align:right">Total</td>
					<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $crdtSum2 . '</td>
					<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $SGPA2 . '</td>
					<td colspan="1" style="text-align: center;border-right: 1px solid black; border-top: 1px solid black;">' . $gpointSum2 . '</td>

				</tr>
				<tr style="font-weight: bold; vertical-align: middle;">

					<td colspan="2" style="border-right: 1px solid black;"></td>
					<td colspan="4" style="vertical-align: middle;border-right: 1px solid black; text-align:right;"><u>
						Semester Average: ' . $SGPA1 . ' <br>
						Commulative Average: ' . $CGPA1 . '<br></u>
					</td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 2px solid black;"></td>

					<td colspan="2" style="border-right: 1px solid black;"></td>
					<td colspan="4" style="vertical-align: middle;border-right: 1px solid black; text-align: right;"><u>
						Semester Average:' . $SGPA2 . '<br>
						Commulative Average: ' . $CGPA2 . '<br></u></td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 1px solid black;"></td>

				</tr>';

			$j++;
		}

		return [$courseList, $rowCourses];
	}

	return;
}

function getThesisTitle($srd2Obj)
{
	$pTitle = "-";
	$projectTitles = $srd2Obj->getThesisTitles()->andFilterWhere(['tt_approved' => 1, 'tt_hod_approved' => 1])->all();
	if ($projectTitles && count($projectTitles) > 0) {
		foreach ($projectTitles as $tTitle) {
			$pTitle = "-";
			if (file_exists('uploads/thesisTitles/' . $tTitle->tt_title)) {
				$pTitle = file_get_contents('uploads/thesisTitles/' . $tTitle->tt_title);
				if (!$tTitle->tt_graded) {
					$tTitle->tt_graded = 1;
					$tTitle->update(false);
				}
				break;
			}
		}
	}

	return $pTitle;
}

?>