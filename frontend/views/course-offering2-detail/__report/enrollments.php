<?php
// use yii\helpers\Html;
$modleSrd2 = $enrollments;
$group = $model->coff2detCoff2Rel->coff2GroupRel;
$course = $model->coff2det_course_relId;
// $coff2 = $model->coff2detCoff2Rel;

/**
 * @var $model \backend\models\CourseOffering2Detail
 * @var $modleSrd2 \backend\models\SemesterRegistration2Detail[]
 */

if (isset($print) && $print == false) {
	$print = false;
} else {
	$print = true;
}
?>

<div class="row">
	<?php if ($print): ?>
		<button class="btn btn-link btn-xs pull-right" onclick="printList();return false;" title="print list">Prit</button>
	<?php endif ?>
	<div class="col-xs-12">
		<h5 class="bg-info" style="padding: 2px; font-size:9pt; text-align: center;">
			<u><b style="font-weight: bold; font-size: 12pt;">Course Registration Detail</b></u> <br>
			Course Title: <b><u><?= $model->coff2detCourseRel->course_title . " [{$model->coff2detCourseRel->course_code}({$model->coff2detCourseRel->course_crdthr}-{$model->coff2detCourseRel->course_lecture_hour}-{$model->coff2detCourseRel->course_lab_hour})]" ?></u></b>
			<br>
			Course Instructor: <b><u><?= $model->coff2detInstRel ? $model->coff2detInstRel->getFullName() : '-'  ?></u></b>&nbsp;&nbsp;&nbsp;
			Academic Year: <b><u><?= $model->coff2detAcProgRel ? $model->coff2detAcProgRel->getAcadYearHeader() : $group->getAcadYearHeader() ?></u></b>
			<br>
			Department: <b><u><?= $group->gsDeptRel->dept_name ?></u></b>&nbsp;&nbsp;&nbsp;
			Specialization: <b><u><?= $group->gsSpecRel ? $group->gsSpecRel->spec_name : '-' ?></u></b>&nbsp;&nbsp;&nbsp;<br><br>
			Class Year : <b><u><?= $model->coff2detAcProgRel ? $model->coff2detAcProgRel->getClassLevel() : $group->gsClslvlRel->getClslvlDisp() ?></u></b>&nbsp;&nbsp;&nbsp;
			Program: <b><u><?= $group->gsAdmRel->admission_name . "-" . $group->gsProgRel->program_name ?></u></b><br>
			Group: <?= $group->gsGroupRel->group_name ?>
		</h5>

	</div><!-- /.col -->
</div>
<style>
	/*
	table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
	}
	*/
</style>
<div class="container-fluid table-responsive">
	<?php if ($modleSrd2 && count($modleSrd2) > 0) {
	?>
		<table class="table table-bordered" cellspacing=0 cellpadding=3 border=1 style="width: 100%; border-collapse:collapse; font-size: 9pt;">
			<tbody>
				<tr>
					<th>#</th>
					<th>Reg. ID</th>
					<th>Student Name</th>
					<th>Gender</th>
					<th>Attendance(%)</th>
					<th>TotalMark</th>
					<th>Grade</th>
					<th>Approval</th>
					<th>Option</th>
					<th>Remark</th>
				</tr>

				<?php
				$c = 0;
				$actionForAll = '';
				$un_approved_exists = 0;
				$ungraded = 0;
				$display_for_all_case = false;
				$Hod = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
				foreach ($modleSrd2 as $srd2) {
					$btn = "";
					$btnLock = "";
					$_message = "";

					// checks if the enrollment is fine of pre-requisite control
					$preqCheck = $srd2->checkEnrollmentForPrequisiteValidity();
					if (is_array($preqCheck)) {
						if ($preqCheck[0] == FALSE) {
							$_message = $preqCheck[1];
						}
					}
					if (Yii::$app->user->can('DepartmentHead')) {
						if ($Hod && $Hod->inst_dep_relId == $srd2->srd2CourseRel->course_dept_relId) {
							// !is_null($srd2->srd2_grade_relId) && 
							if (($srd2->srd2_gradeLocked != 1) && $print) {
								//if there is grade not locked yet
								$display_for_all_case = true;
								if ($srd2->srd2_gradeApproved) {
									$btn = "<button class='btn btn-xs btn-warning' onclick='CancelSingle({$srd2->id});return false;'  title='Cancel grade approval'>Cancel</button>";
								} else {
									if (!is_null($srd2->srd2_grade_relId) && $srd2->srd2_gradeApproved != 1) {
										$btn = "<button class='btn btn-xs btn-primary' onclick='ApproveSingle({$srd2->id});return false;'  title='Approve Course Grade'>Approve</button>";
										$un_approved_exists += 1;
										// this also counts ungraded srd2s.
									}
								}
								if (is_null($srd2->srd2_grade_relId)) {
									$ungraded++;
								}
							} else {
								$btn = "Locked by Registrar";
							}
						} else {
							$btn = "{$srd2->srd2CourseRel->courseDeptRel->dept_code} Course.";
						}
					} else if (Yii::$app->user->can('Registrar')) {
						if (is_null($srd2->srd2_grade_relId) && !$srd2->srd2_gradeApproved && $print) {
							$btn = "<button class='btn btn-xs btn-warning' onclick='RemoveEntry({$srd2->id});return false;'  title='Remove course registration entry'>Delete</button>";
						} else {
							if ($print) { //display an option for [Un]Locking the course grade.
								$display_for_all_case = true;
								if ($srd2->srd2_gradeLocked) {
									// $un_approved_exists -= 1;
									$btnLock = " | <button class='btn btn-xs btn-danger' onclick='CancelSingle({$srd2->id});return false;'  title='Unlock Course Grade'>Unlock</button>";
								} else {
									if ($srd2->srd2_gradeApproved && !$srd2->srd2_gradeLocked) {
										$btnLock = " | <button class='btn btn-xs btn-warning' onclick='ApproveSingle({$srd2->id});return false;'  title='Lock Course Grade'>Lock</button>";
										$un_approved_exists += 1;
									} else {
										$btnLock = " | Grade not approved.";
									}
								}
							}
						}
					}

					$student = $srd2->srd2SemregRel->semregStdRel;
					$ltGrade = $srd2->srd2GradeRel ? $srd2->srd2GradeRel->gs_ltr_grade : '-';
					// $course = $srd2->srd2CourseRel;
					$crf = $srd2->srd2_corptd_flag ? $srd2->srd2_corptd_flag : '';

					$totalMark = $srd2->getTotalMark();
					$a_rep = $model->getAttendaceReport($student);
					$attendance = is_numeric($a_rep[0]) ? sprintf("%0.2f", $a_rep[0]) : $a_rep[0];
					$attendance = "{$attendance}|{$a_rep[1]}";
				?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $student->std_regId ?></td>
						<td><?= $student->getFullName() ?></td>
						<td><?= $student->stdUserRel->gender ?></td>
						<td style="text-align: right; padding-right: 4px;"><?= $attendance ?></td>
						<td style="text-align: right; padding-right: 4px;"><?= $totalMark ?></td>
						<td style="text-align: center;"><?= $ltGrade ?></td>
						<td style="text-align: center;"><?= $srd2->srd2_gradeApproved ?></td>
						<td><?= $print ? "{$btn} {$btnLock}" : '' ?></td>
						<td style="font-size: 9pt;"><?= $_message ?></td>
					</tr>
				<?php
				}

				if ($display_for_all_case) {
					if (\Yii::$app->user->can('DepartmentHead')) {
						if ($un_approved_exists == 0 && $ungraded != count($enrollments)) {
							//there are graded and un-approved srd2s.
							$actionForAll = "<button class='btn btn-xs btn-warning pull-right' onclick='CancelAll({$group->id},{$course});return false;' title='Cancel all grade approvals'>Cancel All</button>";
						} else if ($un_approved_exists == count($enrollments)) { //all grades are not approved
							$actionForAll = "<button class='btn btn-xs btn-danger pull-right' onclick='ApproveAll({$group->id},{$course});return false;'  title='Approve all course grades'>Approve All</button>";
						}
					} else if (\Yii::$app->user->can('Registrar')) {
						if ($un_approved_exists == 0) { //all the grades are approved
							$actionForAll = "<button class='btn btn-xs btn-danger pull-right' onclick='CancelAll({$group->id},{$course});return false;' title='Unlock all grades'>Unlock All</button>";
						} else if ($un_approved_exists == count($enrollments) || 1) { //all grades are not approved
							$actionForAll = "<button class='btn btn-xs btn-warning pull-right' onclick='ApproveAll({$group->id},{$course});return false;'  title='Lock all course grades'>Lock All</button>";
						}
					}
				}
				?>
				<tr>
					<td colspan="8" style="background-color: #ace;"><br><?= $actionForAll ?></td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			window.printList = () => {
				window.open('/course-offering2-detail/get-enrollments-print',
					'Course Enrolled students',
					'height=700,width=800'
				);
			}

			window.RemoveEntry = (srd2Id) => {
				if (confirm('Are you sure to delete this entry?')) {
					$('#modalContent').html('removing entry...');
					// ChangeApproveGradeStatusSingle
					$.post('/semester-registration2-detail/delete?id=' + srd2Id).done((respt) => {
						if (respt == 1) {
							showEnrollments(__coff2detId, );
						} else {
							alert(respt);
							showEnrollments(__coff2detId, );
						}
					})
				}
			}

			window.ApproveSingle = (srd2Id) => {
				if (confirm('Are you sure to approve the grade?')) {
					$('#modalContent').html('approving grade...');
					// ChangeApproveGradeStatusSingle
					$.post('/semester-registration2-detail/change-approve-grade-status-single', {
						srd2Id: srd2Id,
						option: 1
					}).done((respt) => {
						if (respt == 1) {
							showEnrollments(__coff2detId, );
						} else {
							alert(respt);
							showEnrollments(__coff2detId, );
						}
					})
				}
			}

			window.ApproveAll = (groupId, courseId) => {
				if (confirm('Are you sure to approve all grades?')) {
					$.post('/semester-registration2-detail/change-approve-grade-status-batch', {
						groupId: groupId,
						courseId: courseId,
						option: 1
					}).done((respt) => {
						if (respt == 1) {
							showEnrollments(__coff2detId, );
						} else {
							alert(respt);
							showEnrollments(__coff2detId, );
						}
					})
				}
			}

			// ____________________________________________________________________

			window.CancelSingle = (srd2Id) => {
				if (confirm('Are you sure to cancel the grade approval?')) {
					$.post('/semester-registration2-detail/change-approve-grade-status-single', {
						srd2Id: srd2Id,
						option: 2
					}).done((respt) => {
						if (respt == 1) {
							showEnrollments(__coff2detId, );
						} else {
							alert(respt);
							showEnrollments(__coff2detId, );
						}
					})
				}
			}

			window.CancelAll = (groupId, courseId) => {
				if (confirm('Are you sure to cancel all grade approvals?')) {
					$.post('/semester-registration2-detail/change-approve-grade-status-batch', {
						groupId: groupId,
						courseId: courseId,
						option: 2
					}).done((respt) => {
						if (respt == 1) {
							showEnrollments(__coff2detId, );
						} else {
							alert(respt);
							showEnrollments(__coff2detId, );
						}
					})
				}
			}
		</script>
	<?php
	} else {
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>