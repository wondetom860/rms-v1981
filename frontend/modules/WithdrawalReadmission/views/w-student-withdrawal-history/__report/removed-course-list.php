<?php 

if (isset($model)) {
	$courses = $model->removedCourses;
	if ($courses && count($courses) > 0) {
		echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;' id='54566562'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>
				<h4>List of Courses enrollments entries removed</h4>
				Student Name: {$model->swhStdRel->getFullName()}<br>
			</caption>";
		echo "<thead style='background-color: #4AD;'>
				<th>#</th>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Remark</th>
			</thead><tbody>";
		$count = 0;
		foreach ($courses as $rCourse) {
			echo "
				<td>".++$count."</td>
				<td>{$rCourse->rcCourseRel->getCourseCredits()}</td>
				<td>{$rCourse->rcCourseRel->course_title}</td>
				<td></td>
				";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "No course regsitration removed.";
		return;
	}
}else{
	echo "Invalid request";
	return;
}


 ?>