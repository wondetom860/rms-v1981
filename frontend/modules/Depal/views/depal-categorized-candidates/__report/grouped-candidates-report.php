<?php 

if (isset($model)) {
	$candidates = json_decode(trim($model->candidates,'"'));
	echo "<div class='container-fluid' style='font-size: 0.87em'>";
	if (count($candidates) > 0) {
		echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
		echo " <thead>";
		echo "<th>#</th>
			<th>Reg.Id</th>
			<th>Full Name</th>
			<th>Gender</th>
			<th>Category</th>
			<th>Ethnic Group</th>
			<th>Last Attended Class</th>
			<th>CGPA</th>
			<th>Categorized Under</th>
			<th>Dept. Choice</th>
			<th>Alloted</th>
			<th>Remark</th>";
		echo " </thead><tbody>";
		$count = 0;
		foreach ($candidates as $candidate) {
			echo "<tr>";
			$student = \backend\models\Student::findOne($candidate);
			$region = $student->stdUserRel->ethGroupRel ? substr($student->stdUserRel->ethGroupRel->name, 0,10) : '-';
			$lastAtt = $model->settingRel->sectionRel->getAcadYearHeader();
			$recentSemreg = getCGPA($model->settingRel->sectionRel,$student);
			$groupedUnder = $model->getCategorizedUnder($student,$model->settingRel);
			$deptChoice = "-";
			$secured = "-";
			$choice = $model->settingRel->getDepalChoices()->andFilterWhere(['std_relId' => $candidate])->one();
			if ($choice) {
				$deptChoice = $choice->stringifyDepartments();
				$secured = $choice->secured_dept_relId ? $choice->securedDeptRel->dept_code : '-';
			}
			if (count($recentSemreg) > 1 || count($recentSemreg) < 1) {
				$CGPA = '-';
			}else{
				$CGPA = $recentSemreg[0]->semreg_gradeCompleted?$recentSemreg[0]->semreg_CGPA : NULL;
				$CGPA = $CGPA ? sprintf("%0.3f",$CGPA) : "-";
			}
			echo "<td>".++$count."</td>
				<td>{$student->std_regId}</td>
				<td>{$student->getFullName()}</td>
				<td>{$student->stdUserRel->gender}</td>
				<td>{$student->std_type}</td>
				<td>{$region}</td>
				<td>{$lastAtt}</td>
				<td>{$CGPA}</td>
				<td>{$groupedUnder}</td>
				<td>{$deptChoice}</td>
				<td>{$secured}</td>
				<td></td>
			</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "-";
	}
	echo "</div>";
	return;
}

function getCGPA($section,$student){
	return \backend\models\SemesterRegistration2::find()
        ->where([
            'semreg_acadYear_relId' => $section->academicYearRel->schoolYearRel->id,
            'semreg_semester_relId' => $section->semester_relId,
            'semreg_clslvl_relId' => $section->class_level_relId,
            'semreg_std_relId' => $student->id,
        ])
        ->all();
}

?>