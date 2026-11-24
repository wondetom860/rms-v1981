<?php
/** @var yii\web\View $this */
$this->title='Assign Instructor.'
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Assign Instructors for active courses in this semester.
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responive" id="courseListContainer">
						Loading....
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12" id="studentListContainer">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Assigned Instructors.
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responive" id="instructorAssignedCoursesContainer">
						Loading....
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- courseListContainer -->
<!-- instructorAssignedCoursesContainer -->

<?php

$this->registerJs(
	"

	window.filterByDeptForTA = function(deptId,id){
		$.post('/instructor/instlist2',{deptId:deptId,instType:'TA'}).done(function(data){
			$('#'+id+'_TA').html(data);
		});
	}

	window.filterByDeptForINST = function(deptId,id){
		$.post('/instructor/instlist2',{deptId:deptId,instType:'INST'}).done(function(data){
			$('#'+id+'_INST').html(data);
		});
	}
	window.assignInstructorFor = function(coffdetid){
		instId = $('#'+coffdetid+'_INST').val();

		if(instId == ''|| typeof instId == 'undefined'){
			alert('Select Instructor!');
			$('#'+coffdetid+'_INST').focus();
		}else{
			if($('#'+coffdetid+'_TA'). length){
				taId =  $('#'+coffdetid+'_TA').val();

				if(taId == '' || typeof taId == 'undefined'){
					alert('Please Select TA.');
					$('#'+coffdetid+'_TA').focus();
				}else{
					dhgfhgdsgfhsdgfhsjdgg(coffdetid,instId,taId);
				}
			}else{
				dhgfhgdsgfhsdgfhsjdgg(coffdetid,instId,null);
			}
		}
	}

	window.dhgfhgdsgfhsdgfhsjdgg = function(coffdetId,instId,taId){
		console.log(coffdetId,instId,taId);
		$.post('/assign-instructor/assign-instructor', {coffdetId:coffdetId,instId:instId,taId:taId}).done(function(data){
			if(data == 1){
				loadCourseDepts();
				loadAssignmentReport();
			}else{
				alert('MESSAGE'+data);
			}
		})
	}
	// get-course-offering-depts 
	window.loadCourseDepts = function(){
		$('#courseListContainer').html('Loading Course Offering depts...');
		$.post('/assign-instructor/get-course-offering-depts',{}).done(function(data){
			$('#courseListContainer').html(data);
			})
	}

	// Get instructor assignemtn reports.
	window.loadAssignmentReport = function(){
		// 
		$('#instructorAssignedCoursesContainer').html('Loading Course Offering depts...');
		$.post('/assign-instructor/get-assignment-report',{}).done(function(data){
			$('#instructorAssignedCoursesContainer').html(data);
			})
	}

	

	loadCourseDepts();
	loadAssignmentReport();

	",
	$this::POS_READY);
?>