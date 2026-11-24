<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Course Completion Check');
$this->params['breadcrumbs'][] = 'Course Completion Check';
?><br>
<div class="course-completion-index">
   <div class="container-fluid">
       <div class="row">
           <div class="panel panel-info">
               <div class="panel-heading">
               
               </div>
               <div class="panel-body">
               	<div class="col-md-2">
               		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               	</div>
                    <div class="col-md-8 col-lg-8 colsm-8">
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

   <!-- StudentListContainer -->
   <!-- studentSemesterGradeReportContainer -->

<?php
	$this->registerJs(
		"
		window.showGrade = function(gsetId,studentId){

			$('#studentSemesterGradeReportContainer').html('Loading.....');
			$.post('/course-completion/report-table',
				{
					gsetId:gsetId, 
					studentId:studentId

					}).done(function(data){
						$('#studentSemesterGradeReportContainer').html(data);
						$('#printIcon').show();

				});
		}

		window.printCont = function(el){
	        // console.log(el);
	        $('.non-print').hide();
	        var restorePage = $('body').html();
	        var printContent = $('#studentSemesterGradeReportContainer').html();
	        $('body').html(printContent);
	        window.print();
	        $('body').html(restorePage);
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

			 $.post('/course-completion/report',
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
		loadStudentsInGroup();
		$('#loadStudents').click(function(e){
			e.preventDefault();
			loadStudentsInGroup();
		});
		",
		$this::POS_READY);
?>