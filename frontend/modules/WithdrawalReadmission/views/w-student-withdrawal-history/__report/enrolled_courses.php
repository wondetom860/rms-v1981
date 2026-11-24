<?php 

if (isset($model)) {
	$courses = $model->semesterRegistration2Details;
	if ($courses && count($courses) > 0) {
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				<h4>List of Courses enrollments entries</h4>
				Student Name: {$model->semregStdRel->getFullName()}<br>
				Academic Year : {$model->semregAcadYearRel->sch_year_desc}-{$model->semregSemesterRel->semester_name}
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Assessment</th>
				<th>Grade</th>
				<th>Remark</th>
			</thead><tbody>";
		$count = 0;
		foreach ($courses as $srd2) {
			$grade = $srd2->srd2GradeRel ? $srd2->srd2GradeRel->gs_ltr_grade : '-';
			echo "
				<td>".++$count."</td>
				<td>{$srd2->srd2CourseRel->getCourseCredits()}</td>
				<td>{$srd2->srd2CourseRel->course_title}</td>
				<td>{$srd2->getTotalMark()}</td>
				<td>{$grade}</td>
				<td></td>
				";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No course regsitration entry found.";
		return;
	}
}else{
	echo "Invalid request";
	return;
}


 ?>