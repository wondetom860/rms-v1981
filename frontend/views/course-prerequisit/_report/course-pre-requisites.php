<?php 


if (isset($model) && !empty($model)) {
	echo "
		<table class='table table-condensed table-hover table-border' style='font-size:9pt;'>
		 	<thead style='background-color:#8D9;'>
		 		<th>Department</th>
		 		<th>Specialization</th>
		 		<th>Pre-requisite Course</th>
		 		<th>Credit Hrs.</th>
		 	</thead>
		 	<tbody>
		";	
	$preqs = $model->preqMainCourseRel->coursePrerequisits;
	if ($preqs && count($preqs) > 0) {
		foreach ($preqs as $keyPreq) {
			$spec = $keyPreq->preq_spec_relId ? $keyPreq->preqSpecRel->spec_name : '-';
			$dept = $keyPreq->preq_dept_relId ? $keyPreq->preqDeptRel->dept_name : 'For all department';
			$pCourse = $keyPreq->preqPreqCourseRel;
			$title = $pCourse ? "{$pCourse->course_title}[{$pCourse->course_code}]" : "-";
			$crdt = $pCourse ? "{$pCourse->course_crdthr}-{$pCourse->course_lecture_hour}-{$pCourse->course_lab_hour}/{$pCourse->course_tutorial_hour}":"-";
			echo "<tr>
				<td>{$dept}</td>
				<td>{$spec}</td>
				<td>{$title}</td>
				<td>{$crdt}</td>
			</tr>";
		}
	}else{
		echo "<tr> <td colspan=4>No pre-requisite course exists</td> </tr>";
	}
	echo "</tbody></table>";
}else{
	echo "No pre-requisite record available";
}

 ?>