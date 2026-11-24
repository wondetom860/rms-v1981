<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Grade Submission Summery';

?>

<h2>Grade Submission Summary - <b>For Registrar</b></h2>

<div class="container-fluid">
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panel-heading">
               	<?php $form = ActiveForm::begin(); ?>
                   <div class="row">
		                <div class="col-md-2">

		                  <?= $form->field($model, 'admission')->dropdownList(
		                      ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                      [
		                      'prompt'=>'Select admission.',
		                      'required'=>TRUE,
		                      'onChange'=>'loadGroup();',
		                  ])->label('Admission') ?>
		                  
		                </div>
	                   	<div class="col-md-2">
	                    	<?= $form->field($model, 'program')->dropdownList(
		                        ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
			                        [
			                        'prompt'=>'Select Program',
			                        'required'=>TRUE,
			                            'onChange'=>' 
			                                $.post( "/program/clslvllists",
			                                    {
			                                        progId:$(this).val()
			                                    }, 
			                                  function( data ) {
			                                      //console.log("Returned: "+data);
			                                      $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data ).trigger("change");
			                                      loadGroup();       
			                                }); ',

		                    	])->label('Program') 
	                    	?>

	                   	</div>

                      	<div class="col-md-2">

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

	                            
	                        ])->label('Academic Year') ?>
                       	</div>

                       	<div class="col-md-2">
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
	                        ])->label('Semester') ?>
                       	</div>

                       	<div class="col-md-2">
	                        <?= 
	                        $form->field($model, 'department')->dropdownList(
	                          ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
	                            [
	                            'prompt'=>'Select Department.',
	                            'required'=>TRUE,
	                            'onChange'=>'loadGroup();',
	                        ])->label('Department') 
	                        ?>

                       	</div>
                       	<div class="col-md-2"><br>             
	                        <button class="btn btn-success btn-sm" id="loadStudents" style="margin-top: 5px;">Load Grades</button>
	                       </div>
                      	</div>
	                   	<?php ActiveForm::end(); ?>
                   </div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<i id="printIcon" style="cursor: pointer; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
							<div class="container-fluid" id="groupCourseInstContainer" style="border: 1px dashed black;">
								
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
	show = 0;
	window.showList = function(p1,p2){
		// console.log('#'+p1+'_'+p2);
		if(show == 0){
			$('#'+p1+'_'+p2).parent().show();
			show = 1;
		}else{
			$('#'+p1+'_'+p2).parent().hide();
			show = 0;
		}
		
	}
	window.changeStatus = function(coffId,courseId,option){
		msg = option ? 'Are you sure to lock grade':'Are you sure to un-lock grade';
		if(confirm(msg)){
			$.post('/grade-submission-summery/lock-grade',
			{
				coffId:coffId,
				courseId:courseId,
				option:option
				
			}).done(function(data){
				if(data == 1){
					loadInsts();
				}else{
					alert(data);
				}
			});
		}
	}
	window.changeStatusSingle = function(coffId,courseId,option){
			msg = option ? 'Are you sure to lock grade':'Are you sure to un-lock grade';
			if(confirm(msg)){
				$.post('/grade-submission-summery/lock-grade-single',
				{
					coffId:coffId,
					courseId:courseId,
					option:option
					
				}).done(function(data){
					if(data == 1){
						loadInsts();
					}else{
						alert(data);
					}
				});
			}
		}

	window.printCont = function(){
        // $('#groupCourseInstContainer').html('Loading Report...');
		admission = $('#studentsemesterstatushistory-admission').val();
		program = $('#studentsemesterstatushistory-program').val();
		department = $('#studentsemesterstatushistory-department').val();
		clslvl = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();

		acadYear = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
		semester = $('#studentsemesterstatushistory-sssh_sem_relid').val();


    	params = {
				'adm':admission,
				'prog':program,
				'dept':department,
				'acadYear':acadYear,
				'sem':semester
			};
		myWindow = window.open(
          '/submitted-grades/load-group-instructors-dept-print?params='+JSON.stringify(params), 
          'Print Students Grade Report', 
          'height=600,width=700');

        myWindow.print();
        // myWindow.close();
        return false;
    }


    window.loadGroup = function(){
    	loadInsts();
		// $.post('/w-group-setup/get-group-lists-peryear',
	 //        {
	 //          acadYearId:$('#studentsemesterstatushistory-sssh_acadyear_relid').val(),
	 //          semId:$('#studentsemesterstatushistory-sssh_sem_relid').val(),
	 //          admId:$('#studentsemesterstatushistory-admission').val(),
	 //          progId:$('#studentsemesterstatushistory-program').val(),
	 //          clslvlId:$('#studentsemesterstatushistory-sssh_clslvl_relid').val(),
	 //          //deptId:$('#studentsemesterstatushistory-department').val(),
	 //          specId:$('#studentsemesterstatushistory-spec').val()
	 //        }, 
	 //        function( data ) {
	 //            $('select#studentsemesterstatushistory-group').html( data ).trigger('change');
	 //        }); 
	}

	window.showGrades = function(coffId,gsetId,courseId){
		$('#groupCourseGradeContainer').html('Loading Grades of the group...');
		$.post('/submitted-grades/load-submitted-grades',{coffId:coffId,gsetId:gsetId,courseId:courseId}).done(function(data){
			$('#groupCourseGradeContainer').html(data);

		});
	}


	window.loadInsts = function(){

		$('#groupCourseInstContainer').html('Loading Report...');
		admission = $('#studentsemesterstatushistory-admission').val();
		program = $('#studentsemesterstatushistory-program').val();
		department = $('#studentsemesterstatushistory-department').val();
		clslvl = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();

		acadYear = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
		semester = $('#studentsemesterstatushistory-sssh_sem_relid').val();

		$('#groupCourseInstContainer').html('Loading Grade Submission Summery.');
		$.post('/submitted-grades/load-group-instructors-dept',
			{
				adm:admission,
				prog:program,
				dept:department,
				acadYear:acadYear,
				sem:semester
			})
		.done(function(data){
			$('#groupCourseInstContainer').html(data);
		});
	}


	$('#loadStudents').click(function(e){
		e.preventDefault();
		loadInsts();
	})
	",
	$this::POS_READY);

?>




