<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
?>

<!-- <h2>course Add/index</h2> -->

<?php 
	
	if (Yii::$app->user->can('Student') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('DepartmentHead')) {

		?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							Makeup Exam Request
						</div>
						<div class="panel-body">
							<div class="container-fluid" id="MakeupexamrequestFromContainer">
								
							</div>
						</div>
					
						<div class="panel-footer">
							
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							Makeup Exam Request in this semester.
						</div>
						<div class="panel-body">
							<div class="container-fluid" id="MakeupexamrequestReportContainer">
								
							</div>
						</div>
						<div class="panel-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		
	}else{
		echo "<p class='bg-danger'>You have nothing to do here.</p>";die();
	}

?>
<!-- MakeupexamrequestFromContainer -->
<!-- MakeupexamrequestReportContainer -->

<?php
$this->registerJs(
	"
	$('#MakeupexamrequestFromContainer').html('Loading your enrolled courses...');

	window.changeStatus = function(merId,actionId){
		actionId = actionId==0?2:actionId;
		if(confirm('You want to Confirm this request?' )){
			$.post('/makeup-exam-request/changerequeststatus',
			{
				reqId:merId,
				actionId:actionId

			}).done(function(data){
			if(data == 1){
				loadEnrolledCoursess();
				loadRequestStatus();
				}else{
					$('#MakeupexamrequestReportContainer').html(data);
				}
			})
		}
	}

	window.req = function(srd2Id,courseId){
		reasonId = $('#makeupexamrequest-mer_reason_relid').val();
		if(reasonId == ''|| reasonId == 'undefined' || reasonId == ' '){
			alert('Reason Not selected.');
			$('#makeupexamrequest-mer_reason_relid').focus();
		}else{
			if(confirm('You want to request makeup exam fot this course?' )){
				$.post(
					'/makeup-exam-request/requestmakeupexam',
					{
						srd2Id:srd2Id,
						reasonId:reasonId,
						courseId:courseId,
					},
					function(data){
						if(data == 1){
							loadEnrolledCoursess();
							loadRequestStatus();
							}else{
								$('#MakeupexamrequestReportContainer').html(data);
							}
					}
					);
			}
		}
		
	}
	window.loadEnrolledCoursess = function(){
		$.post(
		'/makeup-exam-request/getenrolledcourses',
		{},
		function(data){
			$('#MakeupexamrequestFromContainer').html(data);
		}

	);
	}
	window.loadRequestStatus = function(){
		$.post(
		'/makeup-exam-request/requeststatus',
		{},
		function(data){
			$('#MakeupexamrequestReportContainer').html(data);
		}

	);
	}
	loadEnrolledCoursess();
	loadRequestStatus();
	",
	$this::POS_READY
);
?>