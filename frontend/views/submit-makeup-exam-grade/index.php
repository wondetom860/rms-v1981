
<?php
// use yii;
use yii\bootstrap\Modal;
$this->title = 'Submit Make-up exam mark';

?>

<h2><?= $this->title; ?></h2>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Make-up exam requests
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="ReassessmentRequestContainer">
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approved Make-up exam requests
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="ReassessmentApproveReportContainer">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

 Modal::begin([
    'header'=>'<h4 id="modalHeder">Students\' mark Progress report.</h4>',
    'id'=>'modal_stdMark',
    'size'=>'modal-md',
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading student mark...</div>";
 echo "<div id='modalFooter' class='container-fluid' style='font-size:9pt'></div>"; // alert-info
 Modal::end();

 ?>

<!-- ReassessmentRequestContainer -->
<!-- ReassessmentApproveReportContainer -->

<?php

$this->registerJs(
	"
	window.loadRequests = function(){
		$.post('/submit-makeup-exam-grade/get-makeup-exam-requests',{},function(data){
			$('#ReassessmentRequestContainer').html(data);
			});
	}

	window.saveMark = function(courseId,stdId,catagoryId,imaId){
		maxPoint = $('#markInput').attr('max');
		minPoint = $('#markInput').attr('min');
		markEntered = $('#markInput').val();

		$('#modalFooter').html('...');

		if(markEntered < minPoint || markEntered > maxPoint || markEntered == '' || markEntered == 'undefined' || markEntered == ' '){
			$('#modalFooter').html('<p class=\"bg-danger\">Invalid Input for mark!, Please correct</p>');
			// alert('Invalid Input!, Please correct.');
			$('#markInput').focus();
		}else{
			if(confirm('Are you sure to save this Mark ?')){
				$.post('/submit-makeup-exam-grade/save-mark',
				{
					courseId:courseId,
					stdId:stdId,
					catagoryId:catagoryId,
					imaId:imaId,
					markEntered:markEntered

					}).done(function(data){
					if(data == 1){
						window.location.href = window.location;
					}else{
						$('#modalFooter').html(data);
					}
				});
			}
		}
	}

	window.showAssessmentModal  = function(stdId,courseId,srd2Id){
		$('#modal_stdMark').modal('show');
        $('#modalHeder').parent().css('background-color','#8D5');
        // $('#modalFooter').html('Loading Inforamtion. Scaling Up of student missing mark is another option for continous assessment mark submission completion. Have  a nice time...');

        $('#modalContent').load('/submit-makeup-exam-grade/load-cont-assess-report',{srdId:srd2Id});
		// console.log(stdId,courseId,srd2Id);
	}
	// window.saveGrade = function(reqId){

	// 	value = $('#'+reqId+'_ti').val();
	// 	max = $('#'+reqId+'_ti').attr('max');
	// 	console.log(value,max);
	// 	if(value > max || value == ''){
	// 		$('#'+reqId+'_ti').focus();
	// 		return;
	// 	}
	// 	if(confirm('Are you sure to save this grade ?')){
	// 		// actionId = actionId==0?2:actionId;
	// 		//alert('Processing....');
	// 		$.post(
	// 			'/submit-reassessment-grade/changereassessmentstatus',
	// 			{
	// 				reqId:reqId,
	// 				afterMark:value

	// 			},
	// 			function(data){

	// 				if(data == 1){
	// 					loadRequests();
	// 					loadApprovedRequests();
	// 				}else{
	// 					$('#ReassessmentApproveReportContainer').html(data);
	// 				}

	// 			})
	// 	}
		
	// }	

	window.loadApprovedRequests = function(){
		$.post('/submit-makeup-exam-grade/get-approved-makeup-exam-requests',{},function(data){
			$('#ReassessmentApproveReportContainer').html(data);
			});
	}

	// 

	loadRequests();
	loadApprovedRequests();
	",
	$this::POS_READY);

?>