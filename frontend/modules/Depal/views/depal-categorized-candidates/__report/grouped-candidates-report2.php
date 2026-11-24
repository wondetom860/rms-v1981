<?php 

if (isset($settingModel)) {
	$section = $settingModel->sectionRel;
	// get all students assigned under this section.
	// $gsets = $section->groupSetups;
	if (count($settingModel->depalCategorizedCandidates) < 1) {
		echo "Record empty";
		return;
	}
	$model = $settingModel->depalCategorizedCandidates[0];
	$inst = null;
	if (Yii::$app->user->can('DepartmentHead')) {
		$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
	}
	$gAssignments = \backend\models\WGroupAssignment::find()
		->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
		->where(['gs_active_prog_relId' => $section->id])
		->all();

	if ($gAssignments) {
		echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
		echo "<caption style='color:#000; font-weight:normal;text-align: center;'>
				Department Allocation Report >>> {$section->getClassLevelHeader()}- {$section->academicYearRel->getAcHeader()}
			</caption>";
		echo " <thead>";
		echo "<th>#</th>
			<th>Reg.Id</th>
			<th>Full Name</th>
			<th>Category</th>
			<th>Ethnic Group</th>
			<th>Last Attended Class</th>
			<th>CGPA</th>
			<th>Categorized Under</th>
			<th>Choice</th>
			<th>Alloted</th>
			<th>Remark</th>";
		echo " </thead><tbody>";
		$count = 0;
		foreach ($gAssignments as $gas) {
			
			$student = $gas->gasStdRel;
			$deptChoice = $settingModel->getDeptChoiceAlloted($student);
			if ($inst && ($deptChoice['choice'] != null)) {//if there is any choice entry and alloted dept info, HoD should see only those stduents belonging to his/her department ;
				if (($deptChoice['secured'] != '-') && ($deptChoice['secured'] != $inst->instDepRel->dept_code)) {
					continue;
				}
				$term = $inst->instDepRel->dept_code;
			}else{
				$term = $deptChoice['secured'];
			}
			$deptChoice['choice'] =  preg_replace("/($term)/i",'<span class="bg-info" style="font-weight:bold; text-decoration: underline">$1</span>',$deptChoice['choice']);
			echo "<tr>";
			$region = $student->stdUserRel->ethGroupRel? substr($student->stdUserRel->ethGroupRel->name, 0,10) : '-';
			$lastAtt = $section->getAcadYearHeader()." ({$gas->gasGsetRel->gsGroupRel->group_name})";
			$recentSemreg = getCGPA($section,$student);
			$groupedUnder = $model->getCategorizedUnder($student,$settingModel);
			if (count($recentSemreg) > 1 || count($recentSemreg) < 1) {
				$CGPA = '-';
			}else{
				$semreg = $recentSemreg[0];
				$CGPA = sprintf("%0.3f",$semreg->semreg_CGPA);
			}
			echo "<td>".++$count."</td>";
			echo "<td>{$student->std_regId}</td>";
			echo "<td>{$student->getFullName()}</td>";
			echo "<td>{$student->std_type}</td>";
			echo "<td>{$region}</td>";
			echo "<td>{$lastAtt}</td>";
			echo "<td>{$CGPA}</td>";
			echo "<td>{$groupedUnder}</td>";
			echo "<td>{$deptChoice['choice']}</td>";
			echo "<td style='text-align: center; font-weight: bold;'>{$deptChoice['secured']}</td>";
			echo "<td></td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No student assigned in the selected section";
		return;
	}
}else{
	echo "Setting entry not known.";
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