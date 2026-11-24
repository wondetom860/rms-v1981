<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
?>

<!-- <h2>course Add/index</h2> -->

<?php

if (Yii::$app->user->can('Student')) {

	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Course Reassessment
					</div>
					<div class="panel-body">
						<div class="container-fluid" id="ReassessmentFromContainer">

						</div>
					</div>

					<div class="panel-footer">

					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Course Reassessment requests in this semester.
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
		'header' => '<h4>Course re-assessment payment form</h4>',
		'id' => 'modal_payment',
		'size' => 'modal-lg',
	]);
	echo "<div id='modalContent_2' class=''>Loading payment form...</div>";
	Modal::end();

?>
<?php

}

?>
<!-- ReassessmentFromContainer -->
<!-- ReassessmentReportContainer -->

<?php
$this->registerJs(
	"
	$('#ReassessmentFromContainer').html('Loading your enrolled courses...');

	window.loadPaymentForm = function(courseId){
        //console.log(data);
        $('#modal_payment').modal('show')
            .find('#modalContent_2').css('textAlign','left');
            getFile(courseId);
    }
    window.getFile = function(courseId){
        //data = '';
		$('#modalContent_2').html('Loading payment form...');
        $.post('/payment-request/indexx',{courseId:courseId}).done(function(data){
			console.log(data);
        	$('#modalContent_2').html(data);
        });
        //return data;
    }

	// window.loadPaymentForm = function(){
	// 	$('#modal_payment').modal('show');

	// 		// Get if any payment request is made.
	// 		$.post('/reassessment/check-payment',{courseId:courseId}).done(function(data){
	// 			$('#modalContent_2').html(data);
	// 		});
	// }
	window.req = function(courseId,bgrdId, srd2Id,finalMark,finalMarkMax,catagoryId){
		// console.log(courseId,bgrdId, srd2Id,finalMark,finalMarkMax);return;
		if(confirm('You want to re-assess this course?' )){
			$.post(
				'/reassessment/requestreassessment2',
				{
					courseId:courseId,
					bgrdId:bgrdId,
					srd2Id:srd2Id,
					finalMark:finalMark,
					finalMarkMax:finalMarkMax,
					catagoryId:catagoryId,
				},
				function(data){
					if(data == 1){
						loadEnrolledCoursess();
						loadRequestStatus();
					}else{
						$('#ReassessmentReportContainer').html(data);
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
	loadEnrolledCoursess();
	loadRequestStatus();
	",
		$this::POS_READY
);
?>