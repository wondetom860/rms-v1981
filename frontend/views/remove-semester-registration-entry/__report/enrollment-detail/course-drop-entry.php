<?php if (isset($srd2s) && count($srd2s) > 0): 
	$semregObj = $srd2s[0]->srd2SemregRel; 
	$dropModel = new \backend\models\CourseDropHistory();
	$dropHistory= $dropModel->getDroppedCourses($semregObj); 
	if ($dropHistory && count($dropHistory) > 0) {
		$count1 = 0;
		echo "<table class='table table-condensed table-bordered table-sm' cellpadding='1' cellspacing='0'>";
		foreach ($dropHistory as $keyDrop) {
			$gname = $keyDrop->cdhGroupRel ? ", with group ".$keyDrop->cdhGroupRel->group_name." students" : '';
			$clslvl = $keyDrop->cdhClslvlRel->getClslvlDisp();
			$course = $keyDrop->cdhSrd2Rel? $keyDrop->cdhSrd2Rel->srd2CourseRel : $keyDrop->cdhCourseRel;
			$msg = "Dropped the course enrolled in {$clslvl} {$gname}.";
			echo "<tr>
					<td title='{$course->course_title}'>{$course->course_code}</td>
					<td>{$msg}</td>
				</tr>";
		}
		echo "</table>";
	}else{
		echo "<p style='padding:0px;'>None</p>";
	}
?>
<?php endif ?>