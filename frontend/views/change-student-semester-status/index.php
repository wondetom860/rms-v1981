<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Change Student Semester Status');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
	<div class="row">
	    <div class="col-md-12">
	        <div class="container-fluid">
		       <div class="row">
		           <div class="panel panel-primary">
		               <div class="panel-heading">
		                   <div class="row">
		                   	<?php $form = ActiveForm::begin(); ?>
		                     <div class="col-md-2">

		                      <?= $form->field($model, 'admission')->dropdownList(
		                          ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                          [
		                          'prompt'=>'Select admission.',
		                      ])->label('Admission') ?>
		                      
		                     </div>
		                       <div class="col-md-2">
		                        <?= $form->field($model, 'program')->dropdownList(
		                            ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
		                            [
		                            'prompt'=>'Select Program',
		                                'onChange'=>' 
		                                    $.post( "/program/clslvllists",
		                                        {
		                                            progId:$(this).val()
		                                        }, 
		                                      function( data ) {
		                                          //console.log("Returned: "+data);
		                                          $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data );
		                                          LoatStatus();       
		                                        }); ',


		                        ])->label('Program') ?>

		                       </div>
		              
		                      <div class="col-md-2">
		                         <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
		                          ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
		                                [
		                                     'prompt'=>'Select Class Level',

		                                      'onChange'=>'LoatStatus()',
		                            ])->label('ClassLevel') ?>
		                      </div>


		                      <div class="col-md-2">

		                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
		                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
		                            [
		                            'prompt'=>'Select academic year.',
		                            'onChange'=>'LoatStatus()',

		                            
		                        ])->label('Academic Year') ?>
		                       </div>

		                       <div class="col-md-2">
		                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
		                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
		                            [
		                            'prompt'=>'Select semester.',
		                            'onChange'=>'LoatStatus()',
		                        ])->label('Semester') ?>
		                       </div>
		                       <?php ActiveForm::end(); ?>
		                       <div class="col-md-2">  <br>                
		                        	<button class="btn btn-primary btn-sm pull-right" onclick="LoatStatus();return false;">Load Students</button>
		                       </div>
		                   </div>
		               </div>
		               <div class="panel-body">
		                   <div class="col-md-12 col-lg-12 colsm-12">
		                      <hr>
		                      <i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
		                       <div class="container-fluid" id="studentSemesterStatusContainer" style="border: 1px dashed black;">
		                           Waiting for your selection...
		                       </div>
		                   </div>
		               </div>
		           </div>
		       </div>
		   </div>
	    </div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
	            <div class="panel-heading">
	                <h5>Students List with status change in this academic Year.</h5>
	                <!-- <div class="container-fluid" class="form-group col-md-4">
	                	<input type="text" name="nameSearch" title="Search for Student" placeholder="Search Student ID / Name adn hit ENTER" id="nameSearch" class="form-control" />
	                </div> -->
	            </div>
	            <div class="panel-body">
	            	<div class="container-fluid table-responsive" id="resultContainer_2" style="display: none;">
		            	
	            	</div>
	            </div>
	            <div class="panel-footer">
	            	<!-- <span class="bg-warning" style="display: none" id="updateNotification"></span> -->
	            </div>
	        </div>
		</div>
	</div>
</div>
<!-- studentSearchResultContainer -->
<!-- updateNotification -->
<!-- resultContainer_2 -->

<?php

$this->registerJs(
	"

	window.LoatStatus = function(){

		$('#studentSemesterStatusContainer').html('Getting Students....');
		acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
	    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      	admId = $('#studentsemesterstatushistory-admission').val();
      	progId = $('#studentsemesterstatushistory-program').val();
      	clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      	deptId = $('#studentsemesterstatushistory-dept').val(); 
      	spec = $('#studentsemesterstatushistory-spec').val(); 

      	$.post('/change-student-semester-status/get-ad-student-list',
	    {
	        acadYearId:acadYearId,
	        semId:semId,
	        admId:admId,
	        progId:progId,
	        deptId:deptId,
	        clslvlId:clslvlId,
	        spec:spec,

	        }).done(function(data){
	          $('#studentSemesterStatusContainer').html(data);
	          loadStatusChangeStudents();
	          $('#printIcon').show();
	    });
    }
	window.approveStatus = function(recordId,actionId){

		if(actionId == '' || typeof actionId == 'undefined' || actionId == ' '){
			alert('Invalid Otion selected.');
		}else{
			if(confirm('Are You sure for your action?')){
				actionId = actionId == 0?2:actionId; //otherwise it will not detect the variable while passing it via post.
				// 
				$.post('/change-student-semester-status/approve-student-semester-status-change',
				{
					recordId:recordId,
					actionId:actionId

				}).done(function(data){
					$('#resultContainer_2').html(data);
					// approve-student-semester-status-change
			});
		}
		}
	}


	window.changeStatusBack = function(recordId){
		if(confirm('Are you sure to change the student status back?')){
			$.post('/change-student-semester-status/change-student-semester-status-back',
			{
				recordId:recordId
			}).done(function(data){
				if(data == 1){
					loadStatusChangeStudents();
					LoatStatus();
				}else{
					$('#resultContainer_2').html(data).show();
				}
			});
		}
	}

	window.adClas = function(id){
    	$('#row_'+id).toggleClass('bg-success');
    }
	window.changeStatus = function(studentId,statusId,defStatId){

		console.log(studentId,statusId,defStatId);
		acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
	    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
	    admId = $('#studentsemesterstatushistory-admission').val();
	    // studentsemesterstatushistory-admission
      	progId = $('#studentsemesterstatushistory-program').val();
      	clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      	deptId = $('#studentsemesterstatushistory-dept').val(); 
      	spec = $('#studentsemesterstatushistory-spec').val();

		
		if(confirm('Are you sure to change the student status?')){
			$.post('/change-student-semester-status/change-student-semester-status',
			{
				acadYearId:acadYearId,
				SemId:semId,
				studentId:studentId,
				statusId:statusId,
				defStatId:defStatId,
				admId:admId,
				progId:progId,
		        deptId:deptId,
		        clslvlId:clslvlId,
		        spec:spec,
			}).done(function(data){
				if(data == 1){
					loadStatusChangeStudents();
					LoatStatus();
				}else{
					$('#change_stat_'+studentId).html(data).show();
				}
			})
		}
	}

	$('#nameSearch').keypress(function(e){
		if(e.which == 13){
			e.preventDefault();
			text = $(this).val();
			searchForm(text);
		}
	});

	window.searchForm = function(key){
		$('#studentSearchResultContainer').html('Getting your search...');

			$.post('/change-student-semester-status/get-student',{searchKey:key}).done(function(data){
				$('#studentSearchResultContainer').html(data);
				$('#resultContainer').show();
			});
	}
	window.loadStatusChangeStudents = function(){
		$('#resultContainer_2').html('Loading Resulsts..');
		acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
	    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      	admId = $('#studentsemesterstatushistory-admission').val();
      	progId = $('#studentsemesterstatushistory-program').val();
      	clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      	deptId = $('#studentsemesterstatushistory-dept').val(); 
      	spec = $('#studentsemesterstatushistory-spec').val();

		$.post('/change-student-semester-status/get-record',
			{
				acadYearId:acadYearId,
		        semId:semId,
		        admId:admId,
		        progId:progId,
		        deptId:deptId,
		        clslvlId:clslvlId,
		        spec:spec,
			}
			).done(function(data){
			$('#resultContainer_2').html(data).show();
		});
	}

	loadStatusChangeStudents();
	",
	$this::POS_READY);
?>