<?php 


if (isset($modelCoffTemp) && !empty($modelCoffTemp)) {
	$cocatIDS = json_decode($modelCoffTemp->cot_cocat_IDS);
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FD0;'>Courses Offered</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Course Code</th>
			<th>Course Title</th>
			<th>Credit Hr.</th>
			<th>Option</th>
		</thead>";
	$count = 0;
	foreach ($cocatIDS as $cocatId) {
		// $optBtn = Yii::$app->user->can('Admin') ? "<a href='/AcademicCalendar/ac-program-course/update?id=".$course->id."' class='btn btn-xs btn-warning pull-right'>Update</a>" : "-";
		$cocat = \backend\models\CourseCatalog::findOne($cocatId);
        if ($cocat) {
            $course = $cocat->cocatCourseRel;
        }else{
        	continue;
        }
		$optBtn = '';
		echo "<tr>
				<td>".++$count."</td>
				<td>{$course->course_code}</td>
				<td>{$course->course_title}</td>
				<td>{$course->getCourseCredits()}</td>
				<td>{$optBtn}</td>
			</tr>";
	}

	echo "<tr><td colspan=5><p class='bg-info'><i>Students NOT assigned to sections/group</i></p></td></tr>";
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No recrod found</p>";
}


?>