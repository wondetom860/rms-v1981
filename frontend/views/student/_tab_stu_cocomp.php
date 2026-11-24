<?php
if ($modelStudent) {
	// code...
} else {
	echo "No Record Found";
	return;
}

use backend\models\GradeScale;
use yii\helpers\Html;

$modleSemreg = $modelStudent->getSemesterRegistrations()
	->orderBy([
		'semreg_acadYear_relId' => SORT_ASC,
		'semreg_clslvl_relId' => SORT_ASC,
		'semreg_semester_relId' => SORT_ASC,
	])
	->all();
// $gc = $modelStudent->stdGroupRel->gsActiveProgRel->graduate_class; //returns 0 or 1.
$gc = $modelStudent->stdAcProgSchedRel ? $modelStudent->stdAcProgSchedRel->activeProgramRel->graduate_class : NULL;
$lastSemreg = $modelStudent->getLastSemreg();
$one_course_enrolled = $lastSemreg && (count($lastSemreg->semesterRegistration2Details) == 1);
$last_course = $one_course_enrolled ? $lastSemreg->semesterRegistration2Details[0] : NULL;
$last_srd2_id = $last_course ? $last_course->id : NULL;
$fail_graded = $last_course ? (($last_course->srd2_grade_relId == NULL) || (in_array($last_course->srd2_grade_relId, GradeScale::getInvalidGradeIds()))) : false;
$projectCourses = [];
$btnPrint4 = (isset($printing) && ($printing == 1)) ? "" :(($fail_graded && $gc)? "<button class='btn btn-danger btn-xs pull-right' onclick='requestReRegistration({$modelStudent->id},{$last_srd2_id})' title='Re-Registration request' style='margin: 2px 4px;'> Re-Register </button>":'');
$btnPrint3 = isset($printing) && ($printing == 1) ? "" : "<button class='btn btn-success btn-xs pull-right' onclick='requestExitExam({$modelStudent->id})' title='Exit Exam Result Request' style='margin: 2px 4px;'> Exit Exam Request </button>";
$btnPrint2 = isset($printing) && ($printing == 1) ? "" : "<button class='btn btn-primary btn-xs pull-right' onclick='printResult2({$modelStudent->id})' title='Print course completion report(2)' style='margin: 0px 4px;'> Course Completion(2) </button>";
$btnPrint = isset($printing) && ($printing == 1) ? "" : "<button class='btn btn-warning btn-xs pull-right' onclick='printResult({$modelStudent->id})' title='Print course completion report' style='margin: 0px 4px;'> Course Completion </button>";
$btnPrintSC = isset($printing) && ($printing == 1) ? "" : "&nbsp;<button class='btn btn-info btn-xs pull-right' onclick='printStudentCopy({$modelStudent->id},{$modelStudent->std_group_realId})' title='Print student copy' style='margin: 0px 4px;'> Student Copy </button>";
?>

<div class="row">
	<div class="col-xs-12">
		<h4 class="h4">
			<i class="fa fa-grade"></i>
			<center><?= Html::encode(Yii::t('rms', 'Course Completion Report')) ?></center>
		</h4>
		<?php

		if (isset($printing) && $printing == 1) {
			$spec = $modelStudent->std_spec_relId ? "(" . $modelStudent->stdSpecRel->spec_name . ")" : "";
			echo "
			<center>
				<u>Student's Full Name</u> : <b>{$modelStudent->getFullName()}</b><br>
				<u>Reg. No.</u> : <b>{$modelStudent->std_regId}</b> &nbsp;&nbsp;&nbsp;
				<u>Department</u> : <b>{$modelStudent->stdDeptRel->dept_name}{$spec}</b><br>
				<u>Program </u>: <b>{$modelStudent->stdAdmissionRel->admission_name}-{$modelStudent->stdProgRel->program_name}</b><br>
			</center>
		";
		} else {
			echo $btnPrintSC . $btnPrint2 . $btnPrint . $btnPrint3 . $btnPrint4;
		}

		?>
	</div>
</div>
<script type="text/javascript">
	const requestReRegistration = function(std_id,srd2_id){
		$.ajax({
			url: '<?= \yii\helpers\Url::to(['/re-registration/create']) ?>',
			type: 'POST',
			data: {
				'srd2_id' : srd2_id,
				'student_id': std_id
			},
			dataType: 'html',
			success: function(data) {
				$('#modal').modal('show')
					.find('#modalContent')
					.html(data);
			}
		});
	}
	const requestExitExam = function(std_id) {
		$.ajax({
			url: '<?= \yii\helpers\Url::to(['/ExitExam/et-elligiblity-test/create']) ?>',
			type: 'POST',
			data: {
				'EtElligiblityTest[reason]': 1,
				'student_id': std_id
			},
			dataType: 'html',
			success: function(data) {
				$('#modal').modal('show')
					.find('.modal-content')
					.html(data);
			}
		});
	}
	const printResult2 = (stdId) => {
		myw2 = window.open(
			'/course-completion/report-table4?studentId=' + stdId,
			'title=Course Completion Report',
			'height=900,width=1000');
	}
	const printResult = (stdId) => {
		myw2 = window.open(
			'/student/get-course-completion-report?stdId=' + stdId,
			'title=Course Completion Report',
			'height=900,width=1000');
	}

	const printStudentCopy = (stdId, gsetId) => {
		myw3 = window.open(
			'/student-copy/report-table?studentId=' + stdId + "&gsetId=" + gsetId,
			'title=Course Completion Report',
			'height=900,width=1300');
	}
</script>
<div class="row hidden">
	<div class="col-xs-12">
		<h5 class="page-header bg-info">Course Completion Report</h5>
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive" style="<?= isset($printing) ?  '' : 'max-height: 500px; overflow: auto;' ?>" style="background-color: #FFF; margin-top: 5px;">
	<?php if ($modleSemreg && count($modleSemreg) > 0) {
	?>
		<table border="1" cellpadding="18" cellspacing="0" style="width: 100%;">
			<thead style="background-color: #DFD; text-align: center;">
				<th style="text-align:center;">Class Level</th>
				<th style="text-align:center;">Academic Year</th>
				<th style="text-align:center;">SGPA</th>
				<th style="text-align:center;">CGPA</th>
				<th style="text-align:center;">GPS</th>
				<th style="text-align:center;">Status</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				$sgpa = " - ";
				$cgpa = " - ";
				$status = '-';
				// $srd2Ids = [];

				$course_work_crhr = [0, 0, 0, 0];
				$project_work_crhr = [0, 0, 0, 0];
				$total_crhr_sum = [0, 0, 0, 0];
				$rptd_wourse_work_crhr = [0, 0, 0, 0];
				$rptd_project_work_crhr = [0, 0, 0, 0];
				$grade_point_sum = 0;
				$rptd_grade_point_sum = 0;
				$maxSemReg = $modleSemreg[0];
				$sum = 0;
				foreach ($modleSemreg as $semreg) {
					if ($semreg->id > $maxSemReg->id) {
						$maxSemReg = $semreg;
					}
					// $grade_point_sum += $semreg->semreg_
					if (count($semreg->semesterRegistration2Details) < 1) {
						continue;
					}
					$courses = getSemesterCoursesWithGrades($semreg);
					if ($semreg->semreg_gradeCompleted == 1) {
						$sgpa = sprintf("%0.2f", $semreg->semreg_SGPA);
						$cgpa = sprintf("%0.2f", $semreg->semreg_CGPA);
						$gps = sprintf("%0.2f", $semreg->semreg_gradePoint_sum);
						$status = $semreg->semesterLastSsnRel ? $semreg->semesterLastSsnRel->sssn_name : '';
					} else {
						$sgpa = " - ";
						$cgpa = " - ";
						$status = '-';
						$gps = "-";
					}
				?>
					<tr style="background-color: #889;">
						<td style="text-align:center; padding=2px;"><?= $semreg->semregClslvlRel->getClslvlDisp() ?></td>
						<td style="text-align:center; padding=2px;"><?= $semreg->semregAcadYearRel->sch_year_desc . "-" . $semreg->semregSemesterRel->semester_name ?></td>
						<td style="text-align:center; padding=2px;"><?= $sgpa ?></td>
						<td style="text-align:center; padding=2px;"><?= $cgpa ?></td>
						<td style="text-align:center; padding=2px;"><?= $gps ?></td>
						<td style="text-align:center; padding=2px;"><?= $status ?></td>
					</tr>
					<tr>
						<td colspan="6" style="background-color: white;">
							<?= $courses[1] ?>
						</td>
					</tr>
				<?php
					$projectCourses[] = $courses[0];
					$sum = $courses[2];

					$total_crhr_sum[0] += $sum[0][0];
					$total_crhr_sum[1] += $sum[0][1];
					$total_crhr_sum[2] += $sum[0][2];
					$total_crhr_sum[3] += $sum[0][3];

					$project_work_crhr[0] += $sum[1][0];
					$project_work_crhr[1] += $sum[1][1];
					$project_work_crhr[2] += $sum[1][2];
					$project_work_crhr[3] += $sum[1][3];

					$rptd_wourse_work_crhr[0] += $sum[3][1][0];
					$rptd_wourse_work_crhr[1] += $sum[3][1][1];
					$rptd_wourse_work_crhr[2] += $sum[3][1][2];
					$rptd_wourse_work_crhr[3] += $sum[3][1][3];

					$rptd_project_work_crhr[0] += $sum[3][2][0];
					$rptd_project_work_crhr[1] += $sum[3][2][1];
					$rptd_project_work_crhr[2] += $sum[3][2][2];
					$rptd_project_work_crhr[3] += $sum[3][2][3];

					// $rptd_crdt_hrs = 
					$grade_point_sum += $sum[2][0];
					$rptd_grade_point_sum += $sum[2][1];
				} ?>
				<?php if (count($projectCourses) > 0) : ?>
					<?php
					echo "<tr style='background-color: #889;'>
                		<td style='text-align:center;' colspan=7>Project/Thesis course titles submitted</td>
                	</tr>
                	<tr><td colspan=7><br></td></tr>";
					$countt = 0;
					foreach ($projectCourses as $key => $srd2Id) {
						$srd2Obj = \backend\models\SemesterRegistration2Detail::findOne($srd2Id);
						// var_dump($srd2Obj);
						if (is_null($srd2Obj)) {
							continue;
						}

						$projectTitles = $srd2Obj->getThesisTitles()->andFilterWhere(['tt_approved' => 1, 'tt_hod_approved' => 1])->all();
						if ($projectTitles && count($projectTitles) > 0) {
							foreach ($projectTitles as $tTitle) {
								$pTitle = "Project/Thesis title not found";
								if (file_exists('uploads/thesisTitles/' . $tTitle->tt_title)) {
									$pTitle = file_get_contents('uploads/thesisTitles/' . $tTitle->tt_title);
									if (!$tTitle->tt_graded) {
										$tTitle->tt_graded = 1;
										$tTitle->update(false);
									}
								}
								echo "
										<tr style='background-color: white; font-size: 8pt;'>
											<td colspan=7 style='padding-left: 30px;'>
												{$tTitle->ttSrd2Rel->srd2CourseRel->course_code} : {$pTitle}
											</td>
										</tr>
									";
							}
						} else {
							echo "
								<tr style='background-color: white; font-size: 8pt;'>
									<td colspan=7 style='padding-left: 30px;'>
										{$srd2Obj->srd2CourseRel->course_code} :  -  
									</td>
								</tr>
							";
						}
					} ?>
				<?php endif ?>

				<!-- Display Summary row here -->
				<?php
				// Summary
				$summary = "";
				$summary .= "<table border=1 celspacing=0 cellpadding=19 style='font-size: 8pt; width:100%;'>";
				$summary .= "<thead>
								<th>#</th>
								<th>Work</th>
								<th>Sum total</th>
								<th>Remark</th>
							</thead><tbody>";
				$grade_point_sum = sprintf("%0.2f", ($grade_point_sum - $rptd_grade_point_sum));
				for ($i = 0; $i < 4; $i++) {
					$course_work_crhr[$i] = $total_crhr_sum[$i] - $project_work_crhr[$i];

					$course_work_crhr[$i] -= $rptd_wourse_work_crhr[$i];
					$project_work_crhr[$i] -= $rptd_project_work_crhr[$i];

					$total_crhr_sum[$i] = $course_work_crhr[$i] + $project_work_crhr[$i];
				}
				if ($maxSemReg->semreg_gradeCompleted) {
					$cgpa2 = sprintf("%0.3f", $maxSemReg->semreg_CGPA);
				} else {
					$cgpa2 = "<i class='bg-error'>Pending course grade</i>";
				}
				$summary .= "<tr>
										<td>1</td>
										<td style='text-align: left;'>Course Work Crdt. Hr.</td>
										<td style='font-weight: bold;'>{$course_work_crhr[0]}-{$course_work_crhr[1]}-{$course_work_crhr[2]}/{$course_work_crhr[3]}</td>
										<td>-</td>
								</tr><tr>
										<td>2</td>
										<td style='text-align: left;'>Project/Thesis Work Crdt Hr.</td>
										<td style='font-weight: bold;'>{$project_work_crhr[0]}-{$project_work_crhr[1]}-{$project_work_crhr[2]}/{$project_work_crhr[3]}</td>
										<td>-</td>
								</tr><tr>
										<td>3</td>
										<td style='text-align: left;'>Total Credit Hour</td>
										<td style='font-weight: bold;'>{$total_crhr_sum[0]}-{$total_crhr_sum[1]}-{$total_crhr_sum[2]}/{$total_crhr_sum[3]}</td>
										<td>-</td>
								</tr><tr>
										<td>4</td>
										<td style='text-align: left;'>Grade point sum</td>
										<td style='font-weight: bold;'>{$grade_point_sum}</td>
										<td>-</td>
								</tr><tr>
										<td>5</td>
										<td style='text-align: left;'>Commulative GPA</td>
										<td style='font-weight: bold;'>{$cgpa2}</td>
										<td>-</td>
								</tr>";
				$summary .= "</tbody></table>";
				echo "<tr style='background-color: #889;'>
                		<td style='text-align:center;' colspan=7>Credit hour summary</td>
                	</tr><tr>
                	<td style='text-align:center; background-color: white;' colspan=7>{$summary}</td>
                </tr>";

				?>
			</tbody>
		</table>
	<?php
	} else {
		echo "<p class='bg-warning'>NOT AVAILABLE</p>";
	} ?>
</div>
<?php
function getSemesterCoursesWithGrades($semreg)
{
	// find semester registration details per course:
	$srd2s = $semreg->getSemesterRegistration2Details()
		// ->andWhere(['srd2_corptd_flag' => 0])
		->groupBy('srd2_course_relId')
		->all();
	$rep = "";
	$projectCourses = [];
	// $sum = [0,0,0,0];
	// $sum = [4];
	// fetch coff2 record from srd2:
	if ($srd2s && count($srd2s) > 0) {
		$rep .= "<table border='1' cellpadding='3' cellspacing='0'  style='width:100%; font-size: 8pt; margin: 5px;'>";
		$rep .= "<thead>
					<th>#</th>
					<th>Course Code</th>
					<th>Course Title</th>
					<th>Crdt.Hrs.</th>
					<th>Category</th>
					<th>Grade</th>
					<th>Grd. Pt.</th>
					<th>Remark</th>
				</thead><tbody>";
		$count = 0;
		$repeatedSum_projw = [0, 0, 0, 0];
		$repeatedSum_courw = [0, 0, 0, 0];
		$crdtHrSum = [0, 0, 0, 0];
		$projectCoursesSum = [0, 0, 0, 0];
		$grdPtSum = 0;
		$rptd_grdPtSum = 0;
		foreach ($srd2s as $srd2) {
			$coff2details = $srd2->srd2CoffRel
				->getCourseOffering2Details()
				->andFilterWhere(['coff2det_course_relId' => $srd2->srd2_course_relId])
				->groupBy('coff2det_course_relId')
				->all();
			if (!$coff2details) {
				continue;
			} else {
				$coff2detail = $coff2details[0];
			}
			$category = $coff2detail->coff2detCocatRel->courseCategoryRel;
			$category = $category ? $category->catgeory_name : $coff2detail->coff2detCocatRel->determineCategory();
			$course = $coff2detail->coff2detCourseRel;
			$projCourseMark = "";
			$elective = $coff2detail->coff2detCocatRel->cocat_courseelectives;
			$elective2 = $elective ? "<b><i>(Elective)</i></b>" : "";
			// if the course is project course: we expect project/thesis title to be submitted
			if ($coff2detail->coff2detCocatRel->cocat_courseProject && strtoupper($semreg->semregStdRel->stdProgRel->program_name) == "MASTERS") {
				// $projectCourses[] = $srd22;
				$projCourseMark = "*";
				// $sum[1] += $course->course_crdthr;
			} else {
				// $sum[0] += $course->course_crdthr;
			}
			$rep .= "<tr>";
			$rep .= "<td style='padding:2px;'>" . ++$count . "</td>
					<td style='padding:2px;'>{$course->course_code}{$projCourseMark}</td>
					<td style='padding:2px;'>{$course->course_title}{$elective2}</td>
					<td style='padding:2px;'>{$course->getCourseCredits(1)}</td>
					<td style='padding:2px;'>{$category}</td>
				";
			// if (!$srd22->srd2_corptd_flag) {

			// }
			// check if the course is enrolled by the student: 

			// Student can enroll the course in diffrent semester that it is offered or cataloged.
			// $srd2Obj = 
			$srd22 = \backend\models\SemesterRegistration2Detail::find()
				->innerJoinWith('srd2SemregRel')
				->where([
					'semreg_std_relId' => $semreg->semreg_std_relId,
					'srd2_course_relId' => $course->id
				])
				->one();
			$cc = $course->course_equivalet ? json_decode($course->course_equivalet, true) : [];
			$cc = array_merge($cc, [$course->id]);

			$srd22 = $semreg
				->getSemesterRegistration2Details()
				->andFilterWhere(['srd2_course_relId' => $coff2detail->coff2det_course_relId])
				->one();

			if (!$srd22) {
				$srd22 = \backend\models\SemesterRegistration2Detail::find()
					->innerJoinWith('srd2SemregRel')
					->where([
						'semreg_std_relId' => $semreg->semreg_std_relId,
						'srd2_course_relId' => $cc
					])
					->one();
			}
			$catalogPass = "";
			// consider only one semregDetail record under the same academic year and semester
			if ($srd22) {
				$ltrgrade = "-";
				$grdPt = "-";
				$rptFlg = "";

				$crdtHrSum[0] += $course->course_crdthr;
				$crdtHrSum[1] += $course->course_lecture_hour;
				$crdtHrSum[2] += $course->course_lab_hour;
				$crdtHrSum[3] += $course->course_tutorial_hour;

				if ($projCourseMark == "*" && strtoupper($semreg->semregStdRel->stdProgRel->program_name) == "MASTERS") {
					if (!in_array($srd22->id, $projectCourses)) {
						$projectCourses[] = $srd22->id;
					}
					if (!$srd22->srd2_corptd_flag) {
						$projectCoursesSum[0] += $course->course_crdthr;
						$projectCoursesSum[1] += $course->course_lecture_hour;
						$projectCoursesSum[2] += $course->course_lab_hour;
						$projectCoursesSum[3] += $course->course_tutorial_hour;
					} else { //course enrolled but repeated 
						// enrolled repeated course:
						if ($projCourseMark == "*") {
							$repeatedSum_projw[0] += $course->course_crdthr;
							$repeatedSum_projw[1] += $course->course_lecture_hour;
							$repeatedSum_projw[2] += $course->course_lab_hour;
							$repeatedSum_projw[3] += $course->course_tutorial_hour;
						}
						// else{
						// 	$repeatedSum_courw[0] += $course->course_crdthr;
						// 	$repeatedSum_courw[1] += $course->course_lecture_hour;
						// 	$repeatedSum_courw[2] += $course->course_lab_hour;
						// 	$repeatedSum_courw[3] += $course->course_tutorial_hour;
						// }
					}
				}
				$rptFlg = $srd22->srd2_corptd_flag ? "<span class='pull-right'>(*)</span>" : "";
				if (!is_null($srd22->srd2_grade_relId)) {
					$ltrgrade = $srd22->srd2GradeRel->gs_ltr_grade;
					// if grade is F, look for other entry on which student has repeated the course.
					// if ($ltrgrade == 'F' || 1) { //course can be repeated even it has Non-F grade, such as GC and C-,C, for major courses:
					// 	$cc = $course->course_equivalet ? json_decode($course->course_equivalet, true) : [];
					// 	// $cc = array_merge($cc, [$course->id]);
					// 	$srd221 = \backend\models\SemesterRegistration2Detail::find()
					// 		->innerJoinWith('srd2SemregRel')
					// 		->innerJoinWith('srd2GradeRel')
					// 		->where([
					// 			'semreg_std_relId' => $semreg->semreg_std_relId,
					// 			'srd2_course_relId' => $course->id,
					// 			'srd2_corptd_flag' => 1,
					// 		])
					// 		->andWhere(" gs_ltr_grade <> 'F' AND semester_registration_2_detail.id <> {$srd22->id} ")
					// 		->orderBy([
					// 			'semreg_acadYear_relId' => SORT_DESC,
					// 			'semreg_semester_relId' => SORT_DESC,
					// 			'gs_gradePoint' => SORT_DESC,
					// 		])
					// 		->one();
					// 	if ($srd221) {
					// 		$srd22 = $srd221;
					// 		$ltrgrade = $srd22->srd2GradeRel->gs_ltr_grade;
					// 	} else {
					// 		// check if equivale exists and graded pass:
					// 		$srd221 = \backend\models\SemesterRegistration2Detail::find()
					// 			->innerJoinWith('srd2SemregRel')
					// 			->innerJoinWith('srd2GradeRel')
					// 			->where([
					// 				'semreg_std_relId' => $semreg->semreg_std_relId,
					// 				'srd2_course_relId' => $cc,
					// 			])
					// 			->andWhere(" gs_ltr_grade <> 'F' AND semester_registration_2_detail.id <> {$srd22->id} ")
					// 			->orderBy([
					// 				'semreg_acadYear_relId' => SORT_DESC,
					// 				'semreg_semester_relId' => SORT_DESC,
					// 				'gs_gradePoint' => SORT_DESC,
					// 			])
					// 			->one();
					// 		if ($srd221) {
					// 			$srd22 = $srd221;
					// 			$ltrgrade = $srd22->srd2GradeRel->gs_ltr_grade;
					// 		}
					// 	}
					// }
					// // student, specially GC can add courses with grades_point < 3 to compensate CGPA & CGPA for graduation requirement.
					// else if ($srd22->srd2GradeRel->gs_gradePoint <= 3) {
					// 	// cehck if any entry exists:
					// 	$srd221 = \backend\models\SemesterRegistration2Detail::find()
					// 		->innerJoinWith('srd2SemregRel')
					// 		->innerJoinWith('srd2GradeRel')
					// 		->where([
					// 			'semreg_std_relId' => $semreg->semreg_std_relId,
					// 			'srd2_course_relId' => $course->id,
					// 			'srd2_corptd_flag' => 1,
					// 		])
					// 		->andWhere(" semester_registration_2_detail.id <> {$srd22->id} ")
					// 		->one();
					// 	if ($srd221) {
					// 		$srd22 = $srd221;
					// 		$ltrgrade = $srd22->srd2GradeRel->gs_ltr_grade;
					// 	}
					// }

					$grdPt = $srd22->srd2GradeRel->gs_gradePoint * $srd22->srd2CourseRel->course_crdthr;
					$rptFlg = $srd22->srd2_corptd_flag ? "<span class='pull-right'>(*)</span>" : "";
					// $rptSpan = "";
					$grdPtSum += $grdPt;
					$rptd_grdPtSum += $srd22->srd2_corptd_flag ? $grdPt : 0;
					$grdPt = sprintf("%0.2f", $grdPt);
					$catalogPass = $srd22->catalogMinPassGradeNotAchieved() ? "<small>Below Min</small>" : '';
					// $sum[2] += $course->course_crdthr;
					if ($srd22->srd2_corptd_flag) {
						$repeatedSum_courw[0] += $course->course_crdthr;
						$repeatedSum_courw[1] += $course->course_lecture_hour;
						$repeatedSum_courw[2] += $course->course_lab_hour;
						$repeatedSum_courw[3] += $course->course_tutorial_hour;
					}
				}
				$rep .= "<td style='text-align: center; font-style: italic; font-weight: bold;'>{$ltrgrade}{$rptFlg}</td>
				<td style='text-align: center; font-style: italic; font-weight: bold;'>{$grdPt}</td>";
			} else { //course is not enrolled
				// if course elective, student maight not be needed have grade.
				if ($elective) {
					$rep .= "<td colspan=2 style='text-align: center;'>Course is elective</td>";
				} else {
					// check if any equivalent is set and enrolled.
					$rep .= "<td colspan=2 style='color:red; text-align: center;'>Not Enrolled</td>";
				}

				// $repeatedSum[0] += $course->course_crdthr;
				// $repeatedSum[1] += $course->course_lecture_hour;
				// $repeatedSum[2] += $course->course_lab_hour;
				// $repeatedSum[3] += $course->course_tutorial_hour;

			}
			$rep .= "<td style='text-align: center;'>{$catalogPass}</td>";
			$rep .= "</tr>";
		}
		$sum[0] = $crdtHrSum;
		$sum[1] = $projectCoursesSum;
		$sum[2] = [$grdPtSum, $rptd_grdPtSum];
		$sum[3] = [0, $repeatedSum_courw, $repeatedSum_projw];
		$grdPtSum = sprintf("%0.2f", $grdPtSum);
		$rep .= "<tr>
			<td colspan=3 style='text-align: center; font-weight: bold'>Total</td>
			<td style='text-align: center; font-weight: bold'>{$crdtHrSum[0]}-{$crdtHrSum[1]}-{$crdtHrSum[2]}/{$crdtHrSum[3]}</td>
			<td colspan=2 style='text-align: right; font-weight: bold'>{$grdPtSum}</td>
		</tr>";
		$rep .= "</tbody></table>";
	} else {
		$rep = "<p class='bg-danger'><i>All Courses are repeated in this semester</i></p>";
	}

	return [$projectCourses, "<div class='container-fluid' style='vertical-align: middle;'>{$rep}</div>", $sum];
}


?>