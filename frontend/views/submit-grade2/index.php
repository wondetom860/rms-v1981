<?php
/** @var yii\web\View $this */
$this->title = "Submit Letter grade";
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Submit grade for students on you course
				</div>
				<div class="panel-body">
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								Select your course
							</div>
							<div class="panel-body">
								<div class="container-fluid" id="selectCourseContainer">
									Loading your courses
								</div>
								<hr>
								<div class="container-fluid" style="background-color: #c99" id="studentListContainer">
									
								</div>
							</div>
							<div class="panel-footer">
								<input type="hidden" id="course" value="">
								<input type="hidden" id="inst" value="">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								Submitted Course of the course
							</div>
							<div class="panel-body">
								<div class="container-fluid" id="submittedGradeReport">
									
								</div>
							</div>
							<div class="panel-footer">
								
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
	</div>
</div>

<!-- selectCourseContainer -->
<!-- studentListContainer -->

<?=
$this->registerJs(
    "
    instId = '';
    courseId = '';
    gsetId = '';

    window.dropGrade = function(regDetailId,courseId,stdId){
    	$.post('/submit-grade2/hfgfvbsfgshgfjsdfat',{

    		regDetailId:regDetailId,
    		courseId:courseId,
    		stdId:stdId,

    		}).done(function(data){
    		loadStudent($('#inst').val(),$('#course').val());
    	});
    }
    window.dhfgfvbsfgshgfjsdfatydasdv = function(){
    	studentGrades = {};
    	succ = 1;
    	$.each($('.std_name'),function(){
    		id = $(this).attr('id');
    		grade = $('#'+id+'_grade').val();

    		//console.log('ID: '+id+'  Grade :'+grade);

    		if(grade != '' && grade != 'undefined'){
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

    		$.post(
    			'/submit-grade2/dhfgfvbsfgshgfjsdfaty',{
    				instId:$('#inst').val(),
    				courseId:$('#course').val() ,
    				gsetId:gsetId,
    				studentList:studentGradesString,
    			}).done(function(data){
    				rData = JSON.parse(data);
    			loadStudent($('#inst').val(),$('#course').val());
    		});

    	}else{
    		alert('Please check the grade, it is not complete');
    	}
    }

    window.loadStudent  = function(instId,courseId){
    	// $('#studentListContainer').show();

    	$('#inst').val(instId);
    	$('#course').val(courseId);
    	
    	$.post('/submit-grade2/enrstudlists',{instId:instId,courseId:courseId},function(data){
    		$('#studentListContainer').html(data);
    		$.post('/submit-grade2/submitted-grade-report',{instId:instId,courseId:courseId}).done(function(data){
    			$('#submittedGradeReport').html(data);
    			});
    		});
    	console.log(instId, courseId);
    }
    // console.log('Generated Url: '+url);
    $.post('/course2/courselists',{},function(data){
    	//console.log(data);
    	$('#selectCourseContainer').html(data);
    	});
    ",
    $this::POS_READY
);
?>