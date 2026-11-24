<?php

if (isset($semRegs) && count($semRegs) > 0) {

	if (isset($coff2Det)) {
		$addRequests = $coff2Det->getCourseAddRequests();

		if ($addRequests > 0) {
			echo "<p class='bg-danger'><b>{$addRequests}</b> Course add requests() for this course. Please check the add request list.</p>";
		}
	}
	echo "<table class='table table-bordered table-striped table-condensed table-xs'>";

	$gset = \backend\models\WGroupSetup::findOne($gsetId);
	$dept = 'Department of ' . $gset->gsDeptRel->dept_name;
	$course = \backend\models\Course::findOne($courseId);
	$prog = $gset->gsAdmRel->admission_name . ' ' . $gset->gsProgRel->program_name;
	$spec = $gset->gs_spec_relId ? $gset->gsSpecRel->spec_name : '-';
	$clslvl = $gset->gsClslvlRel->getClslvlDisp();
	// Course: <b><u>{$course->course_title} [<i>{$course->course_crdthr}-{$course->course_lecture_hour}-{$course->course_lab_hour}/{$course->course_tutorial_hour}</i>]</u></b>

	$instNmae = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
	if ($instNmae) {
		$instNmae = $instNmae->getFullName_av();
	} else {
		$instNmae = "Uknown";
	}
	echo "<caption style='font-weight:normal; color:#000; text-align:center;'>
				<p>
					<b>" . strtoupper($dept) . "</b><br>
					<u>Attendance Form</u><br>
					Program: <b><u>{$prog}</u></b>&nbsp;&nbsp;
					Specialization: <b><u>{$spec}</u></b>&nbsp;&nbsp;<br>

					Academic Year: <b><u>{$gset->gsAcadYearRel->sch_year_desc}-{$gset->gsSemRel->semester_name}</u></b>&nbsp;&nbsp;
					Group: <b><u>{$gset->gsGroupRel->group_name}</u></b>&nbsp;&nbsp;Class Level: <b><u>{$clslvl}</u></b>
				</p>
				<table class='table table-sm table-bordered' style='text-align:left;'>
					<tr>
						<td style='text-align:right;'>Course Code</td>
						<td><b><u>{$course->course_code}</u></b></td>
						<td style='text-align:right;'>Duration(Hr.)</td>
						<td style='vertical-align: bottom;'>_____________________</td> 

					</tr>
					<tr>
						<td style='text-align:right;'>Course Title</td>
						<td><b><u>{$course->course_title}</u></b></td> 
						<td style='text-align:right;'>Date</td>
						<td style='vertical-align: bottom;'>_____________________</td>
						</tr>
				</table>
				<h4>List of Students</h4>
			</caption>";
	echo "<thead>
			<th>#</th>
			<th>Reg. ID</th>
			<th>Full Name</th>
			";
	echo "<th>Signature</th>
			<th>Remark</th>
		</thead><tbody>";
	$count = 0;
	foreach ($semRegs as $semreg) {
		echo "<tr>
				<td>" . ++$count . "</td>
				<td>{$semreg->semregStdRel->std_regId}</td>
				<td>{$semreg->semregStdRel->getFullName()}</td>";
		echo "<td> </td>
				<td> </td>
			</tr>";
	}
	echo "</tbody></table>";

	if ($print == 1) {
		echo "<table class='table table-sm' style='text-align:center; font-weight:bold;'>
					<tr><td><br><u style='font-family:cursive; font-style:italic;'>{$instNmae}</u><br>Name of Instructor</td> 
					<td><br>___________________<br>Signature</td>
					<td><br>_____________________<br>Date</td></tr>
				</table>";
	}
} else {
	echo "<p class='bg-warning'>No record.</p>";
}
