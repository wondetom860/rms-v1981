<?php
$this->title = 'Approve group assignment.';
?>

<h4 style="text-align: center; text-decoration: underline;">Group Assignment Approval Page  - For <b>Registrar</b></h4>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Student Group Assignments
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="AssignmentRequestContainer">
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approved Group Assignments
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="AssignmentApproveReportContainer">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 
AssignmentRequestContainer
AssignmentApproveReportContainer 
-->

<?php

$this->registerJs(
	"


	window.loadunApprovedAssignments = function(){
		$('#AssignmentRequestContainer').html('Loading approved group assignment histroy.');
		// GetAssignments
		$.post('/approve-group-assignment/get-assignments',{}).done(function(data){
			$('#AssignmentRequestContainer').html(data);
		});
	}

	window.loadApprovedAssignments = function(){
		$('#AssignmentApproveReportContainer').html('Loading approved group assignment histroy.');
		$.post('/approve-group-assignment/get-approved-assignments',{}).done(function(data){
			$('#AssignmentApproveReportContainer').html(data);
		});
	}
	loadunApprovedAssignments();
	loadApprovedAssignments();
	",
	$this::POS_READY);

?>

