<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Submited Grades';

?>

<h2>Submitted Grades List - <b>For Department Head</b></h2>

<div class="container-fluid">
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panel-heading">
					<h5>Select from groups assigned to your department in this semester.</h5>
					<div class="container-fluid" id="deptGroupContainer">
						
					</div>
                </div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<h5 class="bg-success">Instructors and their held courses for the group.</h5>
							<div class="container-fluid" id="groupCourseInstContainer">
								
							</div>
						</div>
						<div class="col-md-6">
							<h5 class="bg-success">Submitted Grade for the group students by the instructor</h5>
							<div class="container-fluid" id="groupCourseGradeContainer">
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- groupCourseInstContainer -->
<!-- groupCourseGradeContainer -->

<?php

$this->registerJs(
	"
	window.showGrades = function(coffId,gsetId,courseId){
		console.log(coffId,gsetId,courseId);
		$('#groupCourseGradeContainer').html('Loading Grades of the group...');
		$.post('/submitted-grades/load-submitted-grades',{coffId:coffId,gsetId:gsetId,courseId:courseId}).done(function(data){
			$('#groupCourseGradeContainer').html(data);

		});
	}


	window.loadInsts = function(groupId){
		// groupId = $('#studentsemesterstatushistory-group').val();

		if(groupId == '' || groupId == ' '|| typeof groupId == 'undefined'){
			alert('Group not selected.');
			$('#studentsemesterstatushistory-group').focus();
		}else{
			$('#groupCourseInstContainer').html('Loading Instructors and their held courses for the group.');
			$.post('/submitted-grades/load-group-instructors',{groupId:groupId}).done(function(data){
				$('#groupCourseInstContainer').html(data);
				// console.log(data);
			})
		}
	}

	window.loadDeptGroups = function(){
		$.post('/submitted-grades/ho-dept-groups',{}).done(function(data){
			$('#deptGroupContainer').html(data);
		});
	}

	loadDeptGroups();


	$('#loadStudents').click(function(e){
		e.preventDefault();
		loadInsts();
	})
	",
	$this::POS_READY);

?>



