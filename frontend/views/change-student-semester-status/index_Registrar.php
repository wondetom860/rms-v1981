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
	        <div class="panel panel-primary">
	            <div class="panel-heading">
	                <h5>Student semester status change requests</h5>
	            </div>
	            <div class="panel-body">
	            	<div class="container-fluid" id="resultContainer">
		            	<div class="container-fluid table-responsive" id="studentSearchResultContainer">
		            		
		            	</div>
	            	</div>
	            </div>
	            <div class="panel-footer">
	            	<span class="bg-warning" style="display: none" id="updateNotification"></span>
	            </div>
	        </div>
	    </div>

	    <div class="col-md-12">
	        <div class="panel panel-warning">
	            <div class="panel-heading">
	                <h5>Students List with status change in this academic Year.</h5>
	            </div>
	            <div class="panel-body">
	            	<div class="container-fluid table-responsive" id="resultContainer_2" style="display: none;">
		            	
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<!-- studentSearchResultContainer -->
<!-- updateNotification -->
<!-- resultContainer_2 -->
<!-- requestsContainer -->

<?php

$this->registerJs(
	"

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
					if(data == 1){
						window.location.href = window.location;
					}else{
						alert(data);
					}
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
					searchForm($('#nameSearch').val());
				}else{
					$('#resultContainer_2').html(data).show();
				}
			});
		}
	}


	window.changeStatus = function(studentId,statusId){
		
		if(confirm('Are you sure to chenge the student status?')){
			$.post('/change-student-semester-status/change-student-semester-status',{studentId:studentId,statusId:statusId}).done(function(data){
				if(data == 1){
					loadStatusChangeStudents();
					searchForm($('#nameSearch').val());
				}else{
					$('#resultContainer_2').html(data).show();
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


	window.loadStatusChangerequests = function(){
		$('#studentSearchResultContainer').html('Getting your search...');

			$.post('/change-student-semester-status/get-status-change-requests',{}).done(function(data){
				$('#studentSearchResultContainer').html(data);
				$('#resultContainer').show();
			});
	}

	window.loadStatusChangeStudents = function(){
		$('#resultContainer_2').html('Loading Resulsts..');

		$.post('/change-student-semester-status/get-record',{}).done(function(data){
			$('#resultContainer_2').html(data).show();
		});
	}
	
	loadStatusChangerequests();
	loadStatusChangeStudents();
	",
	$this::POS_READY);
?>