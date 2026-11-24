<?php
/** @var yii\web\View $this */
$this->title = 'Submit Project Course Grade';

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-5">
			<h4 class="bg-info" style="text-decoration: underline;">List of Project Courses</h4>

			<div class="container-fluid" id="projectCourseContainer">
				
			</div>
			<hr>
			<div class="container-fluid" style="background-color: #c99" id="studentListContainer">
				
			</div>
			<input type="hidden" id="course" value="">
			<input type="hidden" id="inst" value="">
		</div>
		<div class="col-md-7">
			<div class="container-fluid" id="submittedGradeReport">
				
			</div>
		</div> 
	</div>
</div>

<?php

$this->registerJs(
	"

	window.dropGrade = function(regDetailId,courseId,stdId){
    	$.post('/submit-grade2/hfgfvbsfgshgfjsdfat',{

    		regDetailId:regDetailId,
    		courseId:courseId,
    		stdId:stdId,

    		}).done(function(data){
    		loadStudent($('#inst').val(),$('#course').val());
    	});
    }
    
	window.dhfgfvbsfgshgfjsdfatydasdv = function(srd2s){
		// dhfgfvbsfgshgfjsdfatydasdv
    	studentGrades = {};
    	succ = 1;
    	$.each($('.std_name'),function(){
    		id = $(this).attr('id');
    		grade = $('#'+id+'_grade').val();

    		console.log('ID: '+id+'  Grade :'+grade);

    		if(grade != 'undefined'){
			studentGrades[id] = grade;
			}else{
				succ = 0;
				$('#'+id+'_grade').focus().prop('title','This is required.');
				return false;
			}
    	});

    	if(succ == 1){
    		gsetId = $('#_gsetId').val();
    		studentGradesString = JSON.stringify(studentGrades);
    		//console.log(studentGradesString + 'GROUP: '+gsetId+' INST: '+instId+' COURSEID: '+courseId);

    		if(confirm('Submit Grade ? ')){
    			$.post(
	    			'/submit-grade2/dhfgfvbsfgshgfjsdfaty',{
	    				srd2s:srd2s,
	    				studentList:studentGradesString,
	    				ad:1
	    			}).done(function(data){
						try{
							rData = JSON.parse(data);
							loadStudent($('#inst').val(),$('#course').val());
						}catch(e){
						$('#submittedGradeReport').html(data);
					}
	    		});
    		}

    	}else{
    		alert('Please check the grade, it is not complete');
    	}
    }

	window.loadStudent  = function(coffdetId){
    	
    	$('#studentListContainer').html('Loading .... ');
    	$('#submittedGradeReport').html('Loading .... ');
    	
    	$.post('/submit-grade2/enrstudlists2', 
    		{
    			instId:coffdetId,
    		},
    		function(data){
    			$('#studentListContainer').html(data);
    			$('#submittedGradeReport').html('Loading Submitted grades...');
    			$.post('/submit-grade2/submitted-grade-report',{instId:coffdetId}).done(function(data){
    				$('#submittedGradeReport').html(data);
    			});
    		});
    	// console.log(instId, courseId);
    }
	window.loadProjectCourses = function(){
		$('#projectCourseContainer').html('Loading Advising courses...');
		$.post('/submit-project-course-grade/get-courses').done(function(data){
			$('#projectCourseContainer').html(data);
		});
	}

	loadProjectCourses();
	",
	$this::POS_READY);
?>