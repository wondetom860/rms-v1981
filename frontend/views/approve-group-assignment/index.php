<?php
$this->title = 'Approve group assignment.';
?>

<h2 style="text-align: center; text-decoration: underline;">Group Assignment Approval Page</h2>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
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

		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approved Re-assessment requests
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
	
	",
	$this::POS_READY);

?>

