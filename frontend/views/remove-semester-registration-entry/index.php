<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */

$this->title = "Remove student semester registration entry";
?>


<div class="remove-semester-registration-entry-index table-repsonsive">
	<h3><?= $this->title ?></h3>
	<div class="row">
		<div class="col-md-4" style="min-height: 300px; height: 300px; background-color: #AAE;">
			<div class="container-fluid" style="background-color: #99F; padding: 10px; margin-top: 20px;">
				<h4>Get Student</h4>
				<div class="row">
					<div class="col-md-9">
						<input type="text" class="form-control" name="" id="search_key_entry" placeholder="Search by student's first name, middle name or reg Id No.">
					</div>
					<div class="col-md-3">
						<button class="btn btn-primary btn-xs pull-left" onclick="getStudents();return false;">Get Student</button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-8" id="search_result_container" style="background-color: #AAE; padding: 10px; max-height: 300px; overflow-y: auto; min-height: 300px; height: 300px; font-size: 9pt;">
			waiting for your selection
		</div>
	</div>
	<hr>
	<div class="row" style="font-size: 9pt;">
		<div class="col-md-6" id="enrollments_container">
			
		</div>
		<div class="col-md-6" style="background-color: #FAA;" id="more_detail_container">
			
		</div>
		
	</div>
</div>

<script type="text/javascript">
	window.getStudents = () => {
		search_key_value = $("#search_key_entry").val();
		$("#search_result_container").html('Loading result...');
		if (search_key_value != '' && search_key_value != ' ') {
			$.post('/remove-semester-registration-entry/get-students',{search_key_value:search_key_value}).done((resp) => { $("#search_result_container").html(resp)});
		}
	}
</script>

<!-- ReassessmentFromContainer -->
<!-- ReassessmentReportContainer -->
