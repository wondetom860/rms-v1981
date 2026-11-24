<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Grade Report Submission Form';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Grade Report Submission Form</h3>

<div class="container-fluid">
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panel-heading">
					<h5>Select Group.</h5>
					<div class="row">
						<?php $form = ActiveForm::begin(); ?>
						<div class="col-md-5">
	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
	                            'onChange'=>'
	                            	loadGroup();
	                              // $.post("/w-group-setup/get-group-lists-peryear",
	                              //   {
	                              //     acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
	                              //     semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
	                              //     admId:$("#studentsemesterstatushistory-admission").val(),
	                              //     progId:$("#studentsemesterstatushistory-program").val(),
	                              //     clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
	                              //     //deptId:$("#studentsemesterstatushistory-department").val(),
	                              //     specId:$("#studentsemesterstatushistory-spec").val()
	                              //   }, 
	                              //   function( data ) {
	                              //       //console.log("Returned: "+data);
	                              //       $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
	                              //   }); 
	                            ',

	                            
	                        ])?>
                       	</div>

                       	<div class="col-md-5">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
	                            'onChange'=>'
	                            	loadGroup();
	                              // $.post("/w-group-setup/get-group-lists-peryear",
	                              //   {
	                              //     acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
	                              //     semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
	                              //     admId:$("#studentsemesterstatushistory-admission").val(),
	                              //     progId:$("#studentsemesterstatushistory-program").val(),
	                              //     clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
	                              //     //deptId:$("#studentsemesterstatushistory-department").val(),
	                              //     specId:$("#studentsemesterstatushistory-spec").val()
	                              //   }, 
	                              //   function( data ) {
	                              //       // console.log("Returned: "+data);
	                              //       $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
	                              //   }); 
	                            ',
	                        ])?>
                       	</div>
                       	<?php ActiveForm::end(); ?>
					</div>
					<div class="container-fluid" id="deptGroupContainer">
						
					</div>
                </div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-8">
							<h5 class="bg-success">Course Offering History</h5>
							<div class="container-fluid" id="groupCourseInstContainer">
								
							</div>
						</div>
						<div class="col-md-12">
							<h5 class="bg-success">Mark Submission History</h5>
							<div class="container-fluid table-responsive" id="groupCourseGradeContainer">
								
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
	window.markSheet = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-mark-sheet-detail?params='+JSON.stringify([coff2detId,23,434]), 
          'Print Students Mark Sheet Report', 
          'height=900,width=1100');
        return false;
    }

    window.gradeReport = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-grade-report-detail?params='+JSON.stringify([coff2detId,24,545]), 
          'Print Students Grade Report', 
          'height=900,width=700');
        return false;
    }
	window.showGrades = function(coffId,gsetId,courseId,academicYear,semester){

		$('#groupCourseGradeContainer').html('Loading Mark Sheet, Please wait...');
		$.post('/continuous-assessment2/enrstudlists',
			{
				instId:coffId,
				courseId:courseId,
				gsetId:gsetId,
				markProgress:1,
				academicYear:academicYear,
				semester:semester
				},
			function(data){
	            $('#groupCourseGradeContainer').html(data);
            });
	}


	window.loadInsts = function(groupId){
		// groupId = $('#studentsemesterstatushistory-group').val();
		$('#groupCourseGradeContainer').html('');

		if(groupId == '' || groupId == ' '|| typeof groupId == 'undefined'){
			alert('Group not selected.');
			$('#studentsemesterstatushistory-group').focus();
		}else{
			$('#groupCourseInstContainer').html('Loading Instructors and their held courses for the group.');
			$.post('/submitted-grades/load-group-instructors',
				{
					groupId:groupId, 
					markProgress:1
				})
				.done(function(data){
				$('#groupCourseInstContainer').html(data);
				// console.log(data);
			})
		}
	}

	window.loadDeptGroups = function(){
		$.post('/submitted-grades/ho-dept-groups',{
			academicYear:$('#studentsemesterstatushistory-sssh_acadyear_relid').val(),
			semester:$('#studentsemesterstatushistory-sssh_sem_relid').val(),
		}).done(function(data){
			$('#deptGroupContainer').html(data);
		});
	}

	window.loadGroup = function(){
		loadDeptGroups();
	}
	


	$('#loadStudents').click(function(e){
		e.preventDefault();
		loadInsts();
	})
	",
	$this::POS_READY);

?>



