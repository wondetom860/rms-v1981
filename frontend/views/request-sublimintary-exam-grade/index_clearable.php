<?php


$this->title = "Request Suplimentary Examiniation";
?>

<div class="container-fluid">
	<div class="row" style="font-size: 9pt;">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Suplimnetary Examination Request - for PG Students.</h4></div>
				<div class="panel-body">
					<?php
						if (isset($clearableCourses) && is_array($clearableCourses)) {
							if (count($clearableCourses) > 0) {
								echo "<table class='table table-bordered table-striped table-condensed table-hover'>
										<thead style = 'background-color:#8D0;'>
											<th>#</th>
											<th>Course Code</th>
											<th>Course Title</th>
											<th>Crdt Hr.</th>
											<th>Prev. Enrol History</th>
											<th>Grade Obtained</th>
											<th>Option</th>
											<th>Remark</th>
										</thead><tbody>";
									$count = 0;
								foreach ($clearableCourses as $EEkey) {
									$btn = "<button class='btn btn-xs btn-warning' onclick='requestSup({$EEkey->id})' title='Request Suplimentary Exam for this course'> Request </button>";
										echo "<tr>
												<td>".++$count."</td>
												<td>{$EEkey->srd2CourseRel->course_code}</td>
												<td>{$EEkey->srd2CourseRel->course_title}</td>
												<td>{$EEkey->srd2CourseRel->course_crdthr}</td>
												<td>{$EEkey->srd2SemregRel->semregAcadYearRel->sch_year_desc}-{$EEkey->srd2SemregRel->semregSemesterRel->semester_name}</td>
												<td>{$EEkey->srd2GradeRel->gs_ltr_grade}</td>
												<td>{$btn}</td>
												<td><span class='bg-error' id='error_message_{$EEkey->id}'></span></td>
											</tr>";
								}
								echo "</tbody>
								</table>";
							}else{
								var_dump($clearableCourses);
							}
						}else{
							echo "<p class='bg-danger'>{$clearableCourses}</p>";
						}

					?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h4>Suplimentary Exam requests</h4>
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="SuplimentaryExamReportContainer">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--  -->
<?php

$this->registerJs(
	"
	window.requestSup = function(id){
		if (confirm('You want to request Suplimentary exam for this course?')) {
			$.post('/request-sublimintary-exam-grade/request',{id:id}).done(function(data){ if (data == 1) { window.location.href = window.location; }else{ $('#error_message_'+id).html(data); } });
		}
	} 

	window.loadSuplimentaryExamRequests = function(){
		$.post('/request-sublimintary-exam-grade/get-request-report')
		.done(function(data){
			$('#SuplimentaryExamReportContainer').html(data); 
		});
	}
	loadSuplimentaryExamRequests();
	",
	$this::POS_READY);

?>