<?php
$this->title = 'Approve Re-assessment request.';
?>

<h2>Reassessment approval page</h2>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Re-assessment requests
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="ReassessmentRequestContainer">
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approved Re-assessment requests
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="ReassessmentApproveReportContainer">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- ReassessmentRequestContainer -->
<!-- ReassessmentApproveReportContainer -->

<?php

$this->registerJs(
	"
	window.loadRequests = function(){
		$.post('/approve-reassessment-request/getreassessrequests',{},function(data){
			$('#ReassessmentRequestContainer').html(data);
			});
	}

	window.saveGrade = function(reqId,actionId){

		if(1!=1){
			
		}else{
			if(actionId == '' || actionId == ' ' || typeof actionId == 'undefined'){
				alert('Invalid Action selected');
			}else{
				if(confirm('Change request status')){
					actionId = actionId==0?2:actionId;
					//alert('Processing....');
					$.post(
						'/approve-reassessment-request/changereassessmentstatus',
						{
							reqId:reqId,
							actionId:actionId,

						},
						function(data){

							if(data == 1){
								loadRequests();
								loadApprovedRequests();
							}else{
								$('#ReassessmentApproveReportContainer').html(data);
							}

					})
				}
			}
		}
	}	

	window.loadApprovedRequests = function(){
		$.post('/approve-reassessment-request/getapprovedreassessrequests',{},function(data){
			$('#ReassessmentApproveReportContainer').html(data);
			});
	}

	// 

	loadRequests();
	loadApprovedRequests();
	",
	$this::POS_READY);

?>