
<?php
$this->title = 'Submit Re-assessment Grade';

?>

<h2>Submit re-assessment mark</h2>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
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

		<div class="col-md-12">
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
		$.post('/submit-reassessment-grade/getreassessrequests',{},function(data){
			$('#ReassessmentRequestContainer').html(data);
			});
	}

	window.saveGrade = function(reqId){

		value = Number($('#'+reqId+'_ti').val());
		max = Number($('#'+reqId+'_ti').attr('max'));
		// console.log(typeof value,max);
		if(value > max || value == ''){
			$('#'+reqId+'_ti').focus();
			return;
		}
		if(confirm('Are you sure to save this grade ?')){
			// actionId = actionId==0?2:actionId;
			//alert('Processing....');
			$.post(
				'/submit-reassessment-grade/changereassessmentstatus',
				{
					reqId:reqId,
					afterMark:value

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

	window.loadApprovedRequests = function(){
		$.post('/submit-reassessment-grade/getapprovedreassessrequests',{},function(data){
			$('#ReassessmentApproveReportContainer').html(data);
			});
	}

	// 

	loadRequests();
	loadApprovedRequests();
	",
	$this::POS_READY);

?>