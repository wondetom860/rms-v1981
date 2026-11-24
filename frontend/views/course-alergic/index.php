<?php

$this->title = 'Course Alergic';
?>
<hr>
<div class="contianer-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4>Course Alergic - Students List</h4>
		</div>
		<div class="panel-body">
			<div class="contianer-fluid" id="courseAlergicStdudentsContainer">
				
			</div>
		</div>
	</div>
</div>

<?php

$this->registerJs(
	"

	window.loadCourseAlergicStudents = function(){
		$.post('/course-alergic/get-ca-students').done(function(data){//
			$('#courseAlergicStdudentsContainer').html(data);
		});
	}
	
	loadCourseAlergicStudents();
	",
	$this::POS_READY);

?>