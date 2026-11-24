
<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Semester Course Assignment');
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="semester-course-assignment-index">
   <div class="container-fluid">
       <div class="row">
           <div class="panel panel-info">
               <div class="panel-heading">
               	<h4 style="text-align: center; text-decoration: underline; font-stretch: semi-condensed; font-weight: bolder;">Semester course assignment report</h4>
               	<?php $form = ActiveForm::begin(); ?>
               		<div class="row">
                      	<div class="col-md-2">

	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
	                            'onChange'=>'
	                              loadStudentsInGroup();
	                            ',

	                            
	                        ])->label('Academic Year') ?>
                       	</div>

                       	<div class="col-md-2">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
	                            'onChange'=>'
	                              loadStudentsInGroup();
	                            ',
	                        ])->label('Semester') ?>
                       	</div>
		                <div class="col-md-2">

		                  <?= $form->field($model, 'admission')->dropdownList(
		                      ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                      [
		                      'prompt'=>'Select admission.',
		                      'required'=>TRUE,
		                      'onChange'=>'loadStudentsInGroup();',
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
			                                     console.log("Returned: "+data);
			                                      $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data ).trigger("change");
			                                      //loadGroup();       
			                                }); ',

		                    	])->label('Program') 
	                    	?>

	                   	</div>
              
                      	<div class="col-md-2">
                         <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
                          ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                     'prompt'=>'Select Class Level',

                                      'onChange'=>'
                                       $.post("/w-group-setup/get-group-lists-peryear",
		                                {
		                                  acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
		                                  semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
		                                  admId:$("#studentsemesterstatushistory-admission").val(),
		                                  progId:$("#studentsemesterstatushistory-program").val(),
		                                  clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
		                                  //deptId:$("#studentsemesterstatushistory-department").val(),
		                                  specId:$("#studentsemesterstatushistory-spec").val()
		                                }, 
		                                function( data ) {
		                                    console.log("Returned: "+data);
		                                    $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
		                                });

                                      ',
                            ])->label('ClassLevel') ?>
                      	</div>

                       	<div class="col-md-2">
	                        <?= 
	                        $form->field($model, 'group')->dropdownList(
	                          ArrayHelper::map(\backend\models\WGroup::find()->all(),'id','group_name'),
	                            [
	                            'prompt'=>'Select group.',
	                            'required'=>TRUE,
	                            'onChange'=>'loadStudentsInGroup()',
	                        ])->label('Group') 
	                        ?>

                       	</div>

                      	</div>
	                    <div class="row">
	                       <div class="col-md-9">                  
	                        <button class="btn btn-primary btn-md" id="loadStudents">Show</button>
	                       </div>
		                </div>
	                   	<?php ActiveForm::end(); ?>
                   </div>
	               <div class="panel-body">
		               	<!-- <div class="col-md-4 col-lg-4 col-sm-4">
		               		<div class="container-fluid" id="StudentListContainer" style="background-color: #dd9">
		               			Waiting...
		               		</div>
		               	</div> -->
	                    <div class="col-md-12 col-lg-12 colsm-12">
	                   		<i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('studentSemesterGradeReportContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
		                       	<div class="container-fluid" id="studentSemesterGradeReportContainer" style="border: 1px dashed black;">
		                           Waiting for your selection...
		                    	</div>
	                    </div>
                   </div>
               </div>
           </div>
       </div>
   </div>


   <?php
	$this->registerJs(
		"
		window.showGrade = function(gsetId,studentId){

			$('#studentSemesterGradeReportContainer').html('Loading.....');
			$.post('/student-semester-grade-report/report-table',
				{
					gsetId:gsetId, 
					studentId:studentId

					}).done(function(data){
						$('#studentSemesterGradeReportContainer').html(data);
						$('#printIcon').show();

				});
		}

		window.printCont = function(el){
			$('.non-print').hide();
			acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
		    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
		    admId = $('#studentsemesterstatushistory-admission').val();
		    progId = $('#studentsemesterstatushistory-program').val();
		    clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
		    gsetId = $('#studentsemesterstatushistory-group').val(); 
		    //requiredStatus = $('#studentsemesterstatushistory-status').val();

		    params={
		    	'acadYearId':acadYearId,
	      		'semId':semId,
	      		'admId':admId,
	      		'progId':progId,
	      		'clslvlId':clslvlId,
	      		'gsetId':gsetId,
		    };
	        myWindow = window.open(
	            '/semester-course-assignment/report-print?params='+JSON.stringify(params), 
	            'Print Students Admission Report', 
	            'height=900,width=800');

	        myWindow.print();
	        // myWindow.close();
	        return false;
	    }


		window.loadStudentsInGroup = function(){
			$('#printIcon').hide();
			$('#studentSemesterGradeReportContainer').html('Calculating Instructors Load in this semester.');
			// $('#StudentListContainer').html('Loading Instructors for your selection.');

			// Load students assigned to the group in the selected academic year adn semester.

				  acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
			      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
			      admId = $('#studentsemesterstatushistory-admission').val();
			      progId = $('#studentsemesterstatushistory-program').val();
			      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
			      gsetId = $('#studentsemesterstatushistory-group').val(); 
			      //requiredStatus = $('#studentsemesterstatushistory-status').val();

			 $.post('/semester-course-assignment/report',
			      	{
			      		acadYearId:acadYearId,
			      		semId:semId,
			      		admId:admId,
			      		progId:progId,
			      		clslvlId:clslvlId,
			      		gsetId:gsetId,

			      	}
			      	).done(function(data){
				$('#studentSemesterGradeReportContainer').html(data);
				$('#printIcon').show();
			 });



		}
		$('#loadStudents').click(function(e){
			e.preventDefault();
			loadStudentsInGroup();
		});
		",
		$this::POS_READY);
?>
