<?php 


if (isset($coff2Model) && !empty($coff2Model)) {
	$coff2Dets = $coff2Model->courseOffering2Details;
	echo "<table class='table table-striped table-condensed table-bordered table-hover' style='font-family:cursive; font-size:8pt;'>";
	echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FDD'>Courses Offered - {$coff2Model->coff2GroupRel->gsGroupRel->group_name}-({$coff2Model->coff2GroupRel->getGsDisp()})</caption>";
	echo "<thead style='background-color: #4AD;'>
			<th>#</th>
			<th>Course Title</th>
			<th>Credit Hr.</th>
			<th>Instructor</th>
			<th>Tech. Asst</th>
			<th>Option</th>
		</thead>";
	$count = 0;
	foreach ($coff2Dets as $coff2det) {
		// $optBtn = Yii::$app->user->can('Admin') ? "<a href='/AcademicCalendar/ac-program-course/update?id=".$course->id."' class='btn btn-xs btn-warning pull-right'>Update</a>" : "-";
		$stdCount = $coff2det->coff2detCoff2Rel->getSemesterRegistration2Details($coff2det->coff2det_course_relId)->count();
		$course = $coff2det->coff2detCourseRel;
		$coff2det->updateFaclutyAssignmentData();
		$optBtn = "<button class='btn btn-xs btn-link' onClick='showEnrollments({$coff2det->id});return false' title='Enrolled Students'>Students : ({$stdCount})</button>";
		$inst = $coff2det->coff2det_inst_relId ? $coff2det->coff2detInstRel->getFullName_av(): ($coff2det->coff2detCourseRel->course_crdthr == 0 ? "<i>NA</i>" : 'TBA');
		echo "<tr>
				<td>".++$count."</td>
				<td>{$course->course_title}</td>
				<td>{$course->getCourseCredits()}</td>
				<td>{$inst}</td>
				<td>{$optBtn}</td>
			</tr>";
	}
	echo "</table>";
}else{
	echo "<p class='bg-warning'>No recrod found</p>";
}


?>

<script type="text/javascript">
	window.showEnrollments = (coff2DetId) => {
        // $('#modal').find('.modal-header').html('Course Enrollments');
        // $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        // $('#modalContent').html('Loading data...');
        // $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        // $('#modal').modal('show');
        // $.post('/AcademicCalendar/ac-active-programs/get-enrollments',{coff2DetId:coff2DetId}).done(function(data){
        //     $('#modalContent').html(data);
        // });
        window.open('/course-offering2-detail/get-enrollments?coff2detId='+coff2DetId+"&gsetObj=89WT09&hide=true",
					'Course Enrolled students',
					'height=750,width=800'
				);  
    }
</script>