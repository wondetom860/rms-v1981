<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
?>

<!-- <h2>course Add/index</h2> -->

<?php 
	
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Course Reassessment requsts in this semester
					</div>
					<div class="panel-body">
						<div class="container-fluid" id="ReassessmentFromContainer">
							
						</div>
					</div>
				
					<div class="panel-footer">
						<div class="container-fluid" id="ErrorContainer">
							
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Approved Course Reassessment requests.
					</div>
					<div class="panel-body">
						<div class="container-fluid" id="ReassessmentReportContainer">
							
						</div>
					</div>
					<div class="panel-footer">
						
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php

	 Modal::begin([
	    'header'=>'<h4>Course re-assessment payment form</h4>',
	    'id'=>'modal_payment',
	    'size'=>'modal-lg',
	 ]);
	 echo "<div id='modalContent' class='jumbotron'>Loading payment form...</div>";
	 Modal::end();

	 ?>
	<?php

?>
<!-- ReassessmentFromContainer -->
<!-- ErrorContainer -->
<!-- ReassessmentReportContainer -->

<?php
$this->registerJs(
	"
	$('#ReassessmentFromContainer').html('Loading course reassessment requests i  this semester...');

	window.loadPaymentForm = function(courseId){
        //console.log(data);
        $('#modal_payment').modal('show')
            .find('#modalContent').css('textAlign','left');
            getFile(courseId);
    }
    window.getFile = function(courseId){
        //data = '';
        $.post('/payment-request/indexx',{courseId:courseId}).done(function(data){
        $('#modalContent').html(data);
        });
        //return data;
    }

	// window.loadPaymentForm = function(){
	// 	$('#modal_payment').modal('show');

	// 		// Get if any payment request is made.
	// 		$.post('/reassessment/check-payment',{courseId:courseId}).done(function(data){
	// 			$('#modalContent').html(data);
	// 		});
	// }
	window.req = function(courseId,bgrdId, srd2Id){
		if(confirm('You want to re-assess this course?' )){
			$.post(
				'/reassessment/requestreassessment',
				{
					courseId:courseId,
					bgrdId:bgrdId,
					srd2Id:srd2Id
				},
				function(data){
					if(data == 1){
						loadEnrolledCoursess();
						loadRequestStatus();
					}else{
						$('#ErrorContainer').html(data);
					}
				}
				);
		}
		
	}
	window.loadEnrolledCoursess = function(){
		$.post(
		'/reassessment/getenrolledcourses',
		{},
		function(data){
			$('#ReassessmentFromContainer').html(data);
		}

	);
	}
	window.loadRequestStatus = function(){
		$.post(
		'/reassessment/requeststatus',
		{},
		function(data){
			$('#ReassessmentReportContainer').html(data);
		}

	);
	}
	// loadEnrolledCoursess();
	loadRequestStatus();
	",
	$this::POS_READY
);
?>