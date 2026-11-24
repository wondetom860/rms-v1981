<?php
/* 
 * This form generates Student copy for the selected student.
 * It has default passed argeuments wich holds POST data.
 * @WONDE-TOM
 * 
 */

use backend\models\SemesterRegistration2Detail;
use frontend\modules\ExitExam\models\EtElligiblityTest;
use frontend\modules\GC\models\GcStudent;
use frontend\modules\TRANSTUD\models\TransferStudent;

$this->title = 'Student Copy';
$date_gc = "04 July 2025";
$gc_student = GcStudent::find()->where(['std_relId' => $studentModel->id, 'reg_approved' => 1])->one();
if ($gc_student) {
    $date_gc = date('d M Y', $gc_student->entryRel->senate_approve_date);
}

if (!(isset($studentModel) || isset($semregData))) {
    echo "
		<div class='alert alert-error'>
			Error, Uknown Input for Generating Student Copy. Please retry.
		</div>
	";
    return;
}

$userModel = $studentModel->stdUserRel;
?>


<div class="container-fluid wrap" style="background-color: white;" style="margin-top: 3px;">
    <div class="row">
        <div class="col-md-12">
            <?php
            $stdDept = is_null($studentModel->std_dept_relId) ? 'Uknown' : $studentModel->stdDeptRel->dept_name;
            $spec = is_null($studentModel->std_spec_relId) ? '-' : $studentModel->stdSpecRel->spec_name;
            ?>
            <table border="1" cellpadding="1" cellspacing="0" style="font-size: 9pt; width:100%; display: table-header-group;">
                <caption style="font-weight:normal;color:#000;">
                    <table border="1" cellpadding="1" cellspacing="0" style="font-size: 9pt; width:100%;">
                        <tr style="text-align:center;">
                            <td colspan="6" style="padding-left: 1px; border: 0px;border-bottom: 1px solid black;">
                                <p style="font-size: 10pt;">P.O.Box: <b><u>27789/1000, Bishoftu, Ethiopia</u></b></p>
                            </td>
                            <td colspan="7" style="padding-left: 3px; border: 0px;border-bottom: 1px solid black;">
                                <h4>
                                    <?= strtoupper(Yii::$app->params['client']) ?><br>
                                    OFFICE OF THE REGISTRAR<br>
                                    STUDENT RECORD
                                </h4>
                            </td>
                            <td colspan="5" style="padding-left: 2px; border: 0px;border-bottom: 1px solid black; text-align: right; padding-right: 7px;">Degree Awarded date: <b><u><?= $date_gc ?></u></b></td>
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
                    $MGPA = $studentModel->calculateMGPA();
                    if ($semregData && count($semregData) > 0) {
                        $i = 0;
                        $displayedCourseList = [];
                        echo displayTransferCourses($semregData[0]);
                        while ($i <= 10) { //max loop would be nOT more than 10
                            $semreg1 = isset($semregData[$i]) ? $semregData[$i] : NULL;
                            $semreg2 = isset($semregData[$i + 1]) ? $semregData[$i + 1] : NULL;
                            if ((is_null($semreg1) && is_null($semreg2))) {
                                break;
                            }
                            $tt = displayColumns($semreg1, $semreg2, $displayedCourseList, $MGPA);
                            if (is_array($tt)) {
                                $displayedCourseList = is_array($tt) ? $tt[1] : $displayedCourseList;
                                echo $tt[0];
                            } else {
                                echo $tt;
                            }

                            // echo $tt[0];
                            $i += 2;
                        }

                        // display ExitExam Report is exists.
                        $exitExamResult = EtElligiblityTest::getPassedEtRecord($studentModel->id);
                        if ($exitExamResult) {
                            $etDetail = $exitExamResult->getEtTestDetails()->where(['pass_status' => 1, 'grade_accepted' => 1])->one();

                            if ($etDetail) {
                                // | 
                                // Test Date: <b><u>" . date('d-m-Y', $etDetail->created_at) . "</u></b> 
                                echo "<tr>
                                    <td colspan=\"18\" style=\"border: 1px solid black; text-align: center; font-weight:normal; vertical-align: middle; font-size:11pt;\">
                                        <b>Exit Exam Result</b>
                                        << Mark Obtained: <b><u>{$etDetail->mark_obtained}</u></b> | 
                                        Status: <b><u>({$etDetail->getPassStatus()})</u></b> &nbsp;&nbsp; 
                                        Test Year: <b><u>{$etDetail->acadYearRel->getAcHeader()}</u></b> >>
                                    </td>
                                </tr>";
                            }
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
	                    		<td colspan = '9' style='text-align:center; padding: 16px 2px;'>Date of issue: ______________________</td>
	                    		<td colspan = '9' style='text-align:center; padding: 16px 2px;'>Registrar: ________________________</td>
	                    </tr>";
                    } else {
                        echo "
							<div class='alert alert-error'>
								Error, Semester registration Uknown. Please retry.
							</div>
						";
                        die();
                    }
                    ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>


<?php

function displayTransferCourses($semreg1)
{
    $std = $semreg1->semregStdRel;
    $tt = "";
    $transfetrSemregs = $std->getSemesterRegistrations()
        ->where(['semreg_entry_type' => 2])
        ->orderBy([
            'semreg_clslvl_relId' => SORT_ASC, //can such case happen for transfer students'?//semreg_acadYear_relId
            'semreg_semester_relId' => SORT_ASC
        ])
        ->all();
    if ($transfetrSemregs) {
        $tt .= "<tr style='font-style: italic;'>
                <td colspan=18 style='border: 1px solid black; text-align: center; font-weight:bold; vertical-align: middle; font-size:11pt;'>
                    Exempted Courses
                </td>
            </tr>";
        $transferCourses = SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_std_relId' => $semreg1->semreg_std_relId,
                'semreg_entry_type' => 2
            ])
            ->all();
        $lastSemreg = $transfetrSemregs[count($transfetrSemregs) - 1];
        $kk = 0;

        while ($kk < count($transferCourses)) {
            $srd1Key = $transferCourses[$kk];
            $course1 = $srd1Key ? $srd1Key->srd2CourseRel : NULL;
            $grade1 = !is_null($srd1Key) ? $srd1Key->srd2GradeRel : NULL;
            $ltrGrade1 = is_null($grade1) ? '' : $grade1->gs_ltr_grade;
            $gPoint1 = is_null($grade1) ? '' : $grade1->gs_gradePoint * $course1->course_crdthr;
            $courseTitle1 = is_null($course1) ? NULL : $course1->course_title;
            $courseCode1 = is_null($course1) ? '' : $course1->course_code;
            $courseCrdt1 = is_null($course1) ? '' : $course1->course_crdthr;

            $srd2Key = $transferCourses[$kk + 1] ?? NULL;
            $course12 = $srd2Key ? $srd2Key->srd2CourseRel : NULL;
            $grade2 = !is_null($srd2Key) ? $srd2Key->srd2GradeRel : NULL;
            $ltrGrade2 = is_null($grade2) ? '' : $grade2->gs_ltr_grade;
            $gPoint2 = is_null($grade2) ? '' : $grade2->gs_gradePoint * $course12->course_crdthr;
            $courseTitle2 = is_null($course12) ? NULL : $course12->course_title;
            $courseCode2 = is_null($course12) ? '' : $course12->course_code;
            $courseCrdt2 = is_null($course12) ? '' : $course12->course_crdthr;

            $tt .= "<tr style=\"font-weight: normal; vertical-align: middle; font-style: italic\">

						<td colspan=\"2\" style=\"border-right: 1px solid black; text-align: left; padding-left:16px;\">{$courseCode1}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade1}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 2px solid black;\">{$gPoint1}</td>

                        <td colspan=\"2\" style=\"border-right: 1px solid black; text-align:left; padding-left:16px;\">{$courseCode2}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade2}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 1px solid black;\">{$gPoint2}</td>

					</tr>";

            $kk += 2;
        }
        $cgpaa = sprintf("%0.3f", $lastSemreg->semreg_CGPA);
        $tt .= "<tr>
            <td colspan=18 style='font-weight: bold; text-align: center; font-style: italic; font-size: 11pt;'>
                Cumulative GPA: <u>{$cgpaa}</u>
            </td>
        </tr>";
    } else {
        $transferStd = TransferStudent::find()->where(['std_relId' => $std->id, 'approved' => 1])->one();
        if ($transferStd) {
            $tt .= "<tr style='font-style: italic;'>
                <td colspan=18 style='border: 1px solid black; text-align: center; font-weight:bold; vertical-align: middle; font-size:11pt;'>
                    Exempted Courses from {$transferStd->transfered_from}
                </td>
            </tr>";
            $exemptedCourses = $transferStd->getExemptedCourses()
                ->where(['approved' => 1])
                ->orderBy(['course_code' => SORT_ASC])
                ->all();
            // $lastSemreg = $transfetrSemregs[count($transfetrSemregs) - 1];
            $kk = 0;

            while ($kk < count($exemptedCourses)) {
                $exCcourse1 = $exemptedCourses[$kk];
                $courseTitle1 = $exCcourse1->course_name;
                $courseCode1 = $exCcourse1->course_code;
                $courseCrdt1 = $exCcourse1->course_hours;
                $ltrGrade1 = $exCcourse1->grade_obtained;
                $gPoint1 = $exCcourse1->grade_point;

                $exCcourse2 = $exemptedCourses[$kk + 1] ?? NULL;
                $courseTitle2 = $exCcourse2 ? $exCcourse2->course_name : '';
                $courseCode2 = $exCcourse2 ? $exCcourse2->course_code : '';
                $courseCrdt2 = $exCcourse2 ? $exCcourse2->course_hours : '';
                $ltrGrade2 = $exCcourse2 ? $exCcourse2->grade_obtained : '';
                $gPoint2 = $exCcourse2 ? $exCcourse2->grade_point : '';

                $tt .= "<tr style=\"font-weight: normal; vertical-align: middle; font-style: italic\">

						<td colspan=\"2\" style=\"border-right: 1px solid black; text-align: left; padding-left:16px;\">{$courseCode1}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt1}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade1}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 2px solid black;\">{$gPoint1}</td>

                        <td colspan=\"2\" style=\"border-right: 1px solid black; text-align:left; padding-left:16px;\">{$courseCode2}</td>
						<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseTitle2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseCrdt2}</td>
						<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade2}</td>
						<td colspan=\"1\" style=\"text-align: right; padding-right: 10px; border-right: 1px solid black;\">{$gPoint2}</td>

					</tr>";

                $kk += 2;
            }
            $cgpaa = sprintf("%0.3f", $transferStd->cgpa);
            $tt .= "<tr>
            <td colspan=18 style='font-weight: bold; text-align: center; font-style: italic; font-size: 11pt;'>
                Cumulative GPA: <u>{$cgpaa}</u>
            </td>
        </tr>";
        }
    }

    return $tt;
}

function displayColumns($semreg1, $semreg2, $rowCourses, $MPGA)
{
    // return ["<tr><td colspan=9>jdbgnbfngmbdfmgb gdmgdfnbgndfggdmgdfnbgndfg</td><td colspan=9>jdbgnbfngmbdfmgb gdmgdfnbgndfggdmgdfnbgndfg</td></tr>", []];
    // $srd2Model = new Seme
    // var_dump($semreg1->semesterRegistration2Details);exit;

    $_MGPA = '';
    // display semester and academic year header here
    if (!is_null($semreg1) || !is_null($semreg2)) {
        if ($semreg2 && ($semreg2->semregClslvlRel->clslvl_num == $semreg2->semregStdRel->stdProgRel->program_duration) && ($semreg2->semregSemesterRel->semester_name == 'II')) {
            $_MGPA = "Major GPA: " . $MPGA;
        }
        $f1 = !is_null($semreg1);
        $f2 = !is_null($semreg2);
        $academicYear1 = $f1 ? $semreg1->semregAcadYearRel->sch_year_desc : NULL;
        $arrayClassLevel1 = $f1 ? $semreg1->semesterRegistration2Details : NULL;
        // $semreg1 = $f1 ? array_keys($arrayClassLevel1)[0] : NULL;

        // $semesterValueArray1 = $f1 ? $arrayClassLevel1[$semreg1] : NULL;

        $academicYear2 = $f2 ? $semreg2->semregAcadYearRel->sch_year_desc : NULL;
        $arrayClassLevel2 = $f2 ? $semreg2->semesterRegistration2Details : NULL;

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
        while ($j < 1) {

            $semName1 = $f1 ? $semreg1->semregSemesterRel->semester_name : NULL;
            $semregObj1 = $f1 ? $semreg1 : NULL;
            $srd2Obj1 = $f1 ? $arrayClassLevel1 : NULL;

            $semName2 = $f2 ? $semreg2->semregSemesterRel->semester_name : NULL;
            $semregObj2 = $f2 ? $semreg2 : NULL;
            $srd2Obj2 = $f2 ? $arrayClassLevel2 : NULL;

            // $semreg11 = is_null($semregObj1)?'':'Year - '.$semregObj1->semregClslvlRel->clslvl_num;
            // $semreg12 = is_null($semregObj2)?'':'Year - '.$semregObj2->semregClslvlRel->clslvl_num;

            $academicYear11 = is_null($semregObj1) ? '' : '- ' . $semregObj1->semregAcadYearRel->sch_year_desc;
            $academicYear12 = is_null($semregObj2) ? '' : '- ' . $semregObj2->semregAcadYearRel->sch_year_desc;

            $sem11 = is_null($semregObj1) ? '' : 'Semester ' . $semName1;
            $sem12 = is_null($semregObj2) ? '' : 'Semester ' . $semName2;

            $SGPA1 = is_null($semregObj1) ? '' : sprintf("%0.2f", $semregObj1->semreg_SGPA);
            $SGPA2 = is_null($semregObj2) ? '' : sprintf("%0.2f", $semregObj2->semreg_SGPA);

            $CGPA1 = is_null($semregObj1) ? '' : sprintf("%0.2f", $semregObj1->semreg_CGPA);
            $CGPA2 = is_null($semregObj2) ? '' : sprintf("%0.2f", $semregObj2->semreg_CGPA);

            $courseList .= '<tr style="font-weight: bold; vertical-align: middle;">

				<td colspan="2" style="border-right: 1px solid black;"></td>
				<td colspan="4" style="vertical-align: middle; border-right: 1px solid black; text-align: center;"><u>' . $sem11 . '  ' . $academicYear11 . '</u></td>
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

                if ($grade1 && $srd1Key->courseIsRepeated()) {
                    $ltrGrade1 = "<del>{$ltrGrade1}</del>";
                    $gPoint1 = "<del>{$gPoint1}</del>";
                }

                if ($grade2 && $srd2Key->courseIsRepeated()) {
                    $ltrGrade2 = "<del>{$ltrGrade2}</del>";
                    $gPoint2 = "<del>{$gPoint2}</del>";
                }


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
						Cumulative Average: ' . $CGPA1 . '<br></u>
					</td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 1px solid black;"></td>
					<td colspan="1" style="border-right: 2px solid black;"></td>

					<td colspan="2" style="border-right: 1px solid black;"></td>
					<td colspan="4" style="vertical-align: middle;border-right: 1px solid black; text-align: right;"><u>
						Semester Average:' . $SGPA2 . '<br>
						Cumulative Average: ' . $CGPA2 . '<br>' . $_MGPA . '
                        </u></td>
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


function displaySemesterFooter($semCourseDisplay, $semreg1, $semreg2)
{ //and this is from semregModel
    $crdtSum1 = $semreg1->semreg_totalCrdtHrs;
    $crdtSum2 = $semreg2->semreg_totalCrdtHrs;

    $gpointSum1 = $semreg1->semreg_gradePoint_sum;
    $gpointSum2 = $semreg2->semreg_gradePoint_sum;

    $CGPA1 = $semreg1->semreg_CGPA == 0.00 ? '-' : round($semreg1->semreg_CGPA, 2);
    $CGPA2 = $semreg2->semreg_CGPA == 0.00 ? '-' : round($semreg2->semreg_CGPA, 2);

    $SGPA1 = $semreg1->semreg_SGPA == 0.00 ? '-' : round($semreg1->semreg_SGPA, 2);
    $SGPA2 = $semreg1->semreg_SGPA == 0.00 ? '-' : round($semreg1->semreg_SGPA, 2);

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
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br>Semester Average: ' . $SGPA1 . ' <br>Cumulative Average: ' . $CGPA1 . '<br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 2px solid black;"></td>

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br>
				Semester Average:' . $SGPA2 . '<br>
				Cumulative Average: ' . $CGPA2 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>

		</tr>
	';
}


function displaySemesterHeader($semreg1, $semreg2) //both semesters are of the same class level.
{ //most actually from semregObject

    // Incase if the student haven.t completed semester registration!
    $acadYear1 = empty($semreg1) ? 'Uknown' : $semreg1->semregAcadeYearRel->sch_year_desc;
    $acadYear2 = empty($semreg2) ? 'Uknown' : $semreg2->semregAcadYearRel->sch_year_desc;
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
