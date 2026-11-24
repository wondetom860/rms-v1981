<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Student Semester Grade Report');
$this->params['breadcrumbs'][] = 'Grade Report';
?><br>
<div class="student-semester-semester-grade-index">
   <div class="container-fluid">
       <div class="row">
           <div class="panel panel-info">
               <div class="panel-heading">
               	<?php $form = ActiveForm::begin(); ?>
                   <div class="row">
		                <div class="col-md-2">

		                  <?= $form->field($model, 'admission')->dropdownList(
		                      ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                      [
		                      'prompt'=>'Select admission.',
		                      'required'=>TRUE,
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
			                                      // loadGroup();       
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
                                      $("#studentsemesterstatushistory-sssh_acadyear_relid").trigger("change");
                                      ',
                            ])->label('ClassLevel') ?>
                      	</div>


                      	<div class="col-md-2">

	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
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
	                                    //console.log("Returned: "+data);
	                                    $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
	                                }); 
	                            // $("select#studentsemesterstatushistory-group" ).trigger("change");
	                            ',

	                            
	                        ])->label('Academic Year') ?>
                       	</div>

                       	<div class="col-md-2">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
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
	                        ])->label('Semester') ?>
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
	                        <button class="btn btn-primary btn-sm" id="loadStudents">Load Students</button>
	                       </div>
		                </div>
	                   	<?php ActiveForm::end(); ?>
                   </div>
	               <div class="panel-body">
		               	<div class="col-md-4 col-lg-4 col-sm-4">
		               		<div class="container-fluid" id="StudentListContainer" style="background-color: #dd9">
		               			Waiting...
		               		</div>
		               	</div>
	                    <div class="col-md-8 col-lg-8 colsm-8">
	                   		<i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('studentSemesterGradeReportContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
		                       	<div class="container-fluid table-responsive" id="studentSemesterGradeReportContainer" style="border: 1px dashed #333;">
		                           Waiting for your selection...
		                    	</div>
	                    </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <!-- StudentListContainer -->
   <!-- studentSemesterGradeReportContainer -->

<?php
	$this->registerJs(
		"
		gsetId1 = '';
		studentId1 = '';
		window.showGrade = function(gsetId,studentId){

			gsetId1 = gsetId;
			studentId1 = studentId;
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

			params = {
				'gsetId':gsetId1,
				'studentId':studentId1
			};
			 myWindow = window.open(
	          '/student-semester-grade-report/print-grade?params='+JSON.stringify(params), 
	          'Print Students Grade Report', 
	          'height=600,width=700');

	        myWindow.print();
	        // myWindow.close();
	        return false;
	    }


		window.loadStudentsInGroup = function(){
			$('#printIcon').hide();
			$('#studentSemesterGradeReportContainer').html('Waiting for student selection...');
			$('#StudentListContainer').html('Loading Students for your selection.');

			// Load students assigned to the group in the selected academic year adn semester.

				  acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
			      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
			      admId = $('#studentsemesterstatushistory-admission').val();
			      progId = $('#studentsemesterstatushistory-program').val();
			      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
			      gsetId = $('#studentsemesterstatushistory-group').val(); 
			      //requiredStatus = $('#studentsemesterstatushistory-status').val();

			 $.post('/student-semester-grade-report/report',
			      	{
			      		acadYearId:acadYearId,
			      		semId:semId,
			      		admId:admId,
			      		progId:progId,
			      		clslvlId:clslvlId,
			      		gsetId:gsetId,

			      	}
			      	).done(function(data){
				$('#StudentListContainer').html(data);
			 });



		}
		$('#loadStudents').click(function(e){
			e.preventDefault();
			loadStudentsInGroup();
		});
		",
		$this::POS_READY);
?>