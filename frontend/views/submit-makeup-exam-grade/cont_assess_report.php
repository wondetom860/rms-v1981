
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
						<?= $contentss ?>
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