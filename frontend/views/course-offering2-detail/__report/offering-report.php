<?php 

// var_dump($hod);die();

if ($model) {
	$offering = $model;
	$hod = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

	$model = \backend\models\CourseOffering2Detail::find()
		->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
		->where([
			'coff2_acadYear_relId' => $offering->coff2detCoff2Rel->coff2_acadYear_relId,
			'coff2_sem_relId' => $offering->coff2detCoff2Rel->coff2_sem_relId,
			'coff2det_course_relId' => $offering->coff2det_course_relId
		])
		->all();
	if (!($model)) {
		echo 'No offering record.';
		return;
	}
	// $course = ;
	echo "<table class='table table-condensed table-bordered table-sm'>";
	echo "<caption style='color:#000; font-weight:normal'><center>Course Title : <u>{$model[0]->coff2detCourseRel->course_title}</u></center></caption>";
	echo "<thead>
			<th>Group</th>
			<th>Instructor</th>
			<th>TA</th>
			<th>Evaluation</th>
		</thead>";
	echo "<tbody>";
	$report = "";
	foreach ($model as $keycoff2det) {
		if (($keycoff2det->coff2detCoff2Rel->coff2GroupRel->gs_dept_relId != $hod->inst_dep_relId) && 
			($keycoff2det->coff2detCourseRel->course_dept_relId != $hod->inst_dep_relId)) {
			continue;
		}
		$inst = $keycoff2det->coff2detInstRel ? $keycoff2det->coff2detInstRel->getFullName() : '-';
		$TA = $keycoff2det->coff2detTARel ? $keycoff2det->coff2detTARel->getFullName() : '-';

		$btnEvaluationReportInst = $keycoff2det->coff2detInstRel && $keycoff2det->coff2detCourseRel->course_dept_relId == $hod->inst_dep_relId ? "<button class='btn btn-xs btn-info pull-right' onclick='showEvaluation($keycoff2det->id,0);return false;'>Evaluation</button>" : '';
		$btnEvaluationReportTA = $keycoff2det->coff2detTARel && $keycoff2det->coff2detCourseRel->course_dept_relId == $hod->inst_dep_relId ? "<button class='btn btn-xs btn-info pull-right' onclick='showEvaluation($keycoff2det->id,1);return false;'>Evaluation</button>" : '';
		// $totalStudents = count($keycoff2det->coff2detCoff2Rel->coff2GroupRel->wGroupAssignments);

		// $enrolledStudents = count($keycoff2det->coff2detCoff2Rel->getSemesterRegistration2Details($keycoff2det->coff2det_course_relId)->all());

		// $maxGrade = $keycoff2det->coff2detCoff2Rel->getMaxGrade($keycoff2det->coff2det_course_relId);
		// $maxGrade = $maxGrade && count($maxGrade) > 0 ? $maxGrade[0]->srd2GradeRel->gs_ltr_grade : '-';
		$gs = $keycoff2det->coff2detCoff2Rel->coff2GroupRel;
		$gName = $gs->gsGroupRel->group_name;
		$clslvl = $gs->gsClslvlRel->getClslvlDisp();
		$p = $gs->gsDeptRel->dept_code.'-'.$gs->gsProgRel->program_name;
		$report .= 
			"<tr>
				<td>{$gName}-(<i>{$p} {$clslvl}</i>)</td>
				<td>{$inst}{$btnEvaluationReportInst}</td>
				<td>{$TA}{$btnEvaluationReportTA}</td>
				<td>-</td>
			</tr>";
	}

	echo $report;
	echo "</tbody>";
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}
?>