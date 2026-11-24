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

function displayCourseDetailsHeader() {//Header I think is fixed.
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
	'
    ;
}



function displaySemesterHeader($semester1, $semester2) {//most actually from semregObject

	// Incase if the student haven.t completed semester registration!
    $acadYear1 = empty($semester1)?'Uknown':\backend\models\SchoolYear::findOne($semester1['semreg_acadYear_relId'])->sch_year_desc;
    $acadYear2 = empty($semester2)?'Uknown':\backend\models\SchoolYear::findOne($semester2['semreg_acadYear_relId'])->sch_year_desc;
    return '
		<tr style="font-weight: bold; vertical-align: middle;">

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle; border-right: 1px solid black;"><u><br>First Semester - ' . $acadYear1 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 2px solid black;"></td>

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle; border-right: 1px solid black;"><u><br>Second Semester - ' . $acadYear2 . '<br><br></u></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>
			<td colspan="1"style="border-right: 1px solid black;"></td>

		</tr>
	';
}

function displaySemesterCourses($semester1, $semester2) { //Most actually from the semregDetailObject.
    // Big algorith here, vertical-align courses bottom.
    $courseList = '';
    $cc = count($semester1) > count($semester2) ? count($semester1) : count($semester2);
    $displayedRowsCount = 0;
    $gpointSum1 = 0;
    $crdtSum1 = 0;

    $gpointSum2 = 0;
    $crdtSum2 = 0;

    if (count($semester1) > count($semester2)) {
        $count = count($semester1) - count($semester2);
        $displayedRowsCount = $count;
        $i = 0;
        while ($i < $count) {
            // display only from sem1
            $srd2 = $semester1[$i];
            $course = \backend\models\Course::findOne($srd2['srd2_course_relId']);
            $grade = \backend\models\GradeScale::findOne($srd2['srd2_grade_relId']);
            $ltrGrade = NULL;
            $grPoint = 0;
            if ($grade) {
                $ltrGrade = $grade->gs_ltr_grade;
                $grPoint = $grade->gs_gradePoint;
            }

            $gPoint = $course->course_crdthr * $grPoint;

            // pass this option if the course is added and the semesterId is diffrent from this acadYear's semsetr.
            if ($srd2['srd2_corptd_flag'] == 1) {
                // the course is repeated, its credit hour must be considered at once only.
                $crdtSum1 += 0;
            } else {
                $crdtSum1 += $course->course_crdthr;
            }

            $gpointSum1 += $gPoint;


            $courseList .= "<tr style=\"font-weight: normal; vertical-align: middle;\">

					<td colspan=\"2\" style=\"border-right: 1px solid black;\">{$course->course_code}</td>
					<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$course->course_title}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$course->course_crdthr}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 2px solid black;\">{$gPoint}</td>

					<td colspan=\"2\" style=\"border-right: 1px solid black;\"></td>
					<td colspan=\"4\" style=\"border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\"></td>

				</tr>";
            $i++;
        }
    } else if (count($semester1) < count($semester2)) {
        $count = count($semester2) - count($semester1);
        $displayedRowsCount = $count;
        $i = 0;
        while ($i < $count) {
            // display only from sem2

            $srd2 = $semester2[$i];
            $course = \backend\models\Course::findOne($srd2['srd2_course_relId']);
            $grade = \backend\models\GradeScale::findOne($srd2['srd2_grade_relId']);
            $ltrGrade = NULL;
            $grPoint = 0;
            if ($grade) {
                $ltrGrade = $grade->gs_ltr_grade;
                $grPoint = $grade->gs_gradePoint;
            }

            $gPoint = $course->course_crdthr * $grPoint;

            // pass this option if the course is added and the semesterId is diffrent from this acadYear's semsetr.
            if ($srd2['srd2_corptd_flag'] == 1) {
                // the course is repeated, its credit hour must be considered at once only.
                $crdtSum2 += 0;
            } else {
                $crdtSum2 += $course->course_crdthr;
            }

            $gpointSum2 += $gPoint;

            $courseList .= "<tr style=\"font-weight: normal; vertical-align: middle;\">

					<td colspan=\"2\" style=\"border-right: 1px solid black;\"></td>
					<td colspan=\"4\" style=\"border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\"></td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 2px solid black;\"></td>

					<td colspan=\"2\" style=\"border-right: 1px solid black;\">{$course->course_code}</td>
					<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$course->course_title}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$course->course_crdthr}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGrade}</td>
					<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$gPoint}</td>

				</tr>";
            $i++;
        }
    }

    // get TINISHUS Length:

    $tinishLength = count($semester1) > count($semester2) ? count($semester2) : count($semester1);

    $i = 0;
    while ($i < $tinishLength) {// Extra part is already displayed above, 
        $A = NULL;
        $B = NULL;
        if (count($semester1) == count($semester2)) {
            // take from sem1 and put left
            // take from sem2 and put right
            $A = $semester1[$i];
            $B = $semester2[$i];
        } else {
            $tinish = count($semester1) > count($semester2) ? [2, $semester2] : [1, $semester1];
            $tilik = count($semester1) > count($semester1) ? [1, $semester1] : [2, $semester2];

            if ($tinish[0] == 1) {
                // sem1 start from 0.
                // sem2 leads by $displayed amoun.
                $A = $tinish[1][$i];
                $B = $tilik[1][$i + $displayedRowsCount];
            } else {
                // sem2 start from 0
                // sem1 leads by $displayed amount.
                $A = $tinish[1][$i + $displayedRowsCount];
                $B = $tilik[1][$i];
            }
        }

        // $srd2A = $semester2[$count+$i];
        $courseA = \backend\models\Course::findOne($A['srd2_course_relId']);
        $gradeA = \backend\models\GradeScale::findOne($A['srd2_grade_relId']);
        $ltrGradeA = NULL;
        $grPointA = 0;
        if ($gradeA) {
            $ltrGradeA = $gradeA->gs_ltr_grade;
            $grPointA = $gradeA->gs_gradePoint;
        }

        $gPointA = $courseA->course_crdthr * $grPointA;

        if ($A['srd2_corptd_flag'] == 1) {
            // the course is repeated, its credit hour must be considered at once only.
            $crdtSum1 += 0;
        } else {
            $crdtSum1 += $courseA->course_crdthr;
        }

        $gpointSum1 += $gPointA;

        /*         * ******************************************************************* */
        // 			My Mistakes are always mine, No one else can solve!       */
        //																	  */
        /*         * ******************************************************************* */

        $courseB = \backend\models\Course::findOne($B['srd2_course_relId']);
        $gradeB = \backend\models\GradeScale::findOne($B['srd2_grade_relId']);
        $ltrGradeB = NULL;
        $grPointB = 0;
        if ($gradeB) {
            $ltrGradeB = $gradeB->gs_ltr_grade;
            $grPointB = $gradeB->gs_gradePoint;
        }

        $gPointB = $courseB->course_crdthr * $grPointB;

        if ($B['srd2_corptd_flag'] == 1) {
            // the course is repeated, its credit hour must be considered at once only.
            $crdtSum2 += 0;
        } else {
            $crdtSum2 += $courseB->course_crdthr;
        }

        $gpointSum2 += $gPointB;

        $courseList .= "<tr style=\"font-weight: normal; vertical-align: middle;\">

				<td colspan=\"2\" style=\"border-right: 1px solid black;\">{$courseA->course_code}</td>
				<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseA->course_title}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseA->course_crdthr}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGradeA}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 2px solid black;\">{$gPointA}</td>

				<td colspan=\"2\" style=\"border-right: 1px solid black;\">{$courseB->course_code}</td>
				<td colspan=\"4\" style=\"border-right: 1px solid black;\">{$courseB->course_title}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$courseB->course_crdthr}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$ltrGradeB}</td>
				<td colspan=\"1\" style=\"text-align: center; border-right: 1px solid black;\">{$gPointB}</td>

			</tr>";
        $i++;
    }

    return [$courseList, $crdtSum1, $crdtSum2, $gpointSum1, $gpointSum2];
}

function displaySemesterFooter($semCourseDisplay, $semester1, $semester2) {//and this is from semregModel
    $crdtSum1 = $semCourseDisplay[1];
    $crdtSum2 = $semCourseDisplay[2];
    $gpointSum1 = $semCourseDisplay[3];
    $gpointSum2 = $semCourseDisplay[4];

    $CGPA1 = $semester1['semreg_CGPA'] == 0.00 ? '-' : round($semester1['semreg_CGPA'], 2);
    $CGPA2 = $semester2['semreg_CGPA'] == 0.00 ? '-' : round($semester2['semreg_CGPA'], 2);

    $SGPA1 = $semester1['semreg_SGPA'] == 0.00 ? '-' : round($semester1['semreg_SGPA'], 2);
    $SGPA2 = $semester2['semreg_SGPA'] == 0.00 ? '-' : round($semester2['semreg_SGPA'], 2);

    $SGPA1 = is_numeric($gpointSum1) && $gpointSum1 != 0 ? (round(($gpointSum1 / $crdtSum1), 2)) : ' - ';
    $SGPA2 = is_numeric($gpointSum2) && $gpointSum2 != 0 ? (round(($gpointSum2 / $crdtSum2), 2)) : ' - ';

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
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br><br><br>Semester Average: ' . $SGPA1 . ' <br>Commulative Average: ' . $CGPA1 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 2px solid black;"></td>

			<td colspan="2" style="border-right: 1px solid black;"></td>
			<td colspan="4" style="vertical-align: middle;border-right: 1px solid black;"><u><br><br><br>
				Semester Average:' . $SGPA2 . '<br>
				Commulative Average: ' . $CGPA2 . '<br><br></u></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>
			<td colspan="1" style="border-right: 1px solid black;"></td>

		</tr>
	';
}
?>


<div class="container-fluid" style="background-color: white;"><br>
    <div class="row" style="text-align: center;">
        <div class="col-md-3"><br> OFFICE OF REGISTRAR <br> P.O.Box 1041, Debrezeit, Ethiopia </div>
        <div class="col-md-6" style="text-align: center;"><h1>DEFENCE UNIVERSITY<br>STUDENT RECORD</h1></div>
        <div class="col-md-3" style="text-align: left;"><br> Degree Awarded date: <b><u>________</u></b></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-sm" style="border:1px solid black;">
                <caption style="border: 0px solid black; color: black;">
                    <?php
                    $stdDept = is_null($studentModel->std_dept_relId) ? 'Uknown' : $studentModel->stdDeptRel->dept_name;

                    echo "<div class='container-fluid'>
						<div class=\"row\">
							<div class=\"col-md-3\">
								College: <span style=\"font-weight: bold; text-decoration: underline;\">College of Engineering</span><br>
								Media of Instruction: <span style=\"font-weight: bold; text-decoration: underline;\">English</span>
							</div>
							<div class=\"col-md-6\">
								Department: <span style=\"font-weight: bold; text-decoration: underline;\">{$stdDept}</span>
							</div>
							<div class=\"col-md-3\">
								Major: <span style=\"font-weight: bold; text-decoration: underline;\">{$stdDept}</span>
							</div>
						</div>
					</div>";
                    ?>
                </caption>
                <tbody>

                    <?php
                    $userModel->first_name = strtoupper($userModel->first_name);
                    $userModel->middle_name = strtoupper($userModel->middle_name);
                    $userModel->last_name = strtoupper($userModel->last_name);
                    $admission = \backend\models\Admission::findOne($studentModel->std_admission_relId)->admission_name;
                    $program = $studentModel->stdProgRel->program_name;
                    $dob = date_format(date_create($userModel->dob), 'd-m-Y');
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
								Admitted from(Institute / School) :<span style=\"font-weight: bold;\"> Soddo Comp (R)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Test Date or Dates : <span style=\"font-weight: bold;\">2013</span>
							</td>
						</tr>
						";
                    ?>
                    <?= displayCourseDetailsHeader() ?>

                    <?php
                    if ($semregData && count($semregData) > 0) {
                        foreach ($semregData as $clslvlnum => $semesterValueArray) {
                            $semester1 = isset($semesterValueArray['I']) ? $semesterValueArray['I'] : NULL;
                            $semester2 = isset($semesterValueArray['II']) ? $semesterValueArray['II'] : NULL;

                            // display semester Header
                            echo displaySemesterHeader($semester1[0], $semester2[0]);

                            // display Semester courses
                            $semCourseDisplay = displaySemesterCourses($semester1[1], $semester2[1]);
                            echo $semCourseDisplay[0];

                            // display semester footer.
                            echo displaySemesterFooter($semCourseDisplay, $semester1[0], $semester2[0]);
                        }
                    // 'gs_forProgram'=>$studentModel->std_prog_relId
                    $gradeScale = \backend\models\GradeScale::find()->where(['gs_valid'=>1,])->all();
                    $gs = '';
                    $gps = '';
                    foreach ($gradeScale as $key) {
                    	$gs .= " {$key->gs_ltr_grade} = [{$key->gs_min} - {$key->gs_max}], ";
                    	$gps .= " {$key->gs_ltr_grade} = {$key->gs_gradePoint}, ";
                    }
                    echo "<tr>
                    		<td colspan = '18' style='border-top:2px solid black; font-size:10pt;'><u>Grading System</u>: {$gs}</td>
	                    </tr>
	                    <tr>
	                    		<td colspan = '18' style='font-size:10pt;'><u>Points</u>: {$gps}. A credit hour is defined as one lecture hour or an approprite combination of two to three hours of laboratory, practical work or tutorial sessions per week for the duration of the semester (18 weeks).</td>
	                    </tr>
	                    <tr>
	                    		<td></td><td></td>
	                    		<td colspan = '17'><b>THE TRANSCRIPT IS OFFICIAL ONLY WHEN SIGNED AND SEALED BY THE REGISTRAR</b></td>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer Message  -->

