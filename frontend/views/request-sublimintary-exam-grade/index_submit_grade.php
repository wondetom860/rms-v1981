<?php

$this->title = "Submit Supplementary Exam Grade";
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-1">
			&nbsp;&nbsp;&nbsp;
		</div>
		<div class="col-md-10">
			<div class="panel panel-success">
				<div class="panel-heading">Approved Supllementary Exam Requests</div>
				<div class="panel-body">
					<div class="container-fluid">
						<?php 

							$gradedRequests = [];
							if (isset($approvedRequests) && count($approvedRequests) > 0) {
								?>
								<table class="table table-hover table-bordered table-condensed table-striped" style="font-size: 10pt;">
								<thead style="background-color: #8D0;">
									<th>#</th>
									<th>Reg. Id</th>
									<th>Full Name</th>
									<th>Course Code</th>
									<th>Course Title</th>
									<th>Registration Info.</th>
									<th>Before grade</th>
									<th>After grade</th>
									<th>Remark</th>
								</thead>
								<tbody>
								<?php
								$count = 0;
								$option  ="Grade NOT submited yet.";
								foreach ($approvedRequests as $ARkey) {
									if (!is_null($ARkey->se_graded)) {
										$gradedRequests[] = $ARkey;
										continue;
									}
									if (isset($gradeScales) && count($gradeScales) > 0) {
										$gss = "<select onchange = 'SubmitGrade({$ARkey->id},$(this).val())'>";
										foreach ($gradeScales as $Gkey) {
											$gss .= "<option value = '{$Gkey->id}'>{$Gkey->gs_ltr_grade}</option>";
										}
										$gss .= "</select>";
									}

									echo "<tr>
										<td>".++$count."</td>	
										<td>{$ARkey->seStdRel->std_regId}</td>				
										<td>{$ARkey->seStdRel->stdUserRel->first_name} {$ARkey->seStdRel->stdUserRel->middle_name}  {$ARkey->seStdRel->stdUserRel->last_name}</td>		
										<td>{$ARkey->seSrd2Rel->srd2CourseRel->course_code}</td>
										<td>{$ARkey->seSrd2Rel->srd2CourseRel->course_title}</td>
										<td>{$ARkey->seSrd2Rel->srd2SemregRel->semregAcadYearRel->sch_year_desc}-{$ARkey->seSrd2Rel->srd2SemregRel->semregSemesterRel->semester_name}</td>
										<td>{$ARkey->sePrevGradeRel->gs_ltr_grade}</td>
										<td>{$gss}</td>
										<td>{$option}</td>											
								</tr>";
								}
								?>
							</tbody>
						</table>
								<?php
							}else{
								echo "<p class='bg-danger'>No record</p>";
							}
						 ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-1">
			&nbsp;&nbsp;&nbsp;
		</div>
		<div class="col-md-10">
			<div class="panel panel-success">
				<div class="panel-heading">Grade Submitted Suplementary exam requests</div>
				<div class="panel-body">
					<?php
						if ($gradedRequests && count($gradedRequests) > 0) {
							?>
							<table class="table table-hover table-bordered table-condensed table-striped" style="font-size: 10pt;">
								<thead style="background-color: #8D0;">
									<th>#</th>
									<th>Reg. Id</th>
									<th>Full Name</th>
									<th>Course Code</th>
									<th>Course Title</th>
									<th>Registration Info.</th>
									<th>Before grade</th>
									<th>After grade</th>
									<th>Remark</th>
								</thead>
								<tbody>
									<?php
									$count = 0;
										foreach ($gradedRequests as $GGkey) {
											// echo "<pre>{$GGkey->seSrd2Rel->srd2CourseRel->course_title} => {$GGkey->seNewGradeRel->gs_ltr_grade}</pre>";
											$option = NULL;
											if ($GGkey->seSrd2Rel->srd2_gradeApproved != 1) {
												$option = "<button title='Cancel this grade' class='btn btn-warning btn-xs' onclick = 'cancelGrade({$GGkey->id})'>Cancel</button>";
											}else{
												$option = "<span class='bg-warning'>Grade already approved.</span>";
											}
											$ng = is_null($GGkey->seNewGradeRel)?'':$GGkey->seNewGradeRel->gs_ltr_grade;
											echo "<tr>
													<td>".++$count."</td>	
													<td>{$GGkey->seStdRel->std_regId}</td>				
													<td>{$GGkey->seStdRel->stdUserRel->first_name} {$GGkey->seStdRel->stdUserRel->middle_name}  {$GGkey->seStdRel->stdUserRel->last_name}</td>		
													<td>{$GGkey->seSrd2Rel->srd2CourseRel->course_code}</td>
													<td>{$GGkey->seSrd2Rel->srd2CourseRel->course_title}</td>
													<td>{$GGkey->seSrd2Rel->srd2SemregRel->semregAcadYearRel->sch_year_desc}-{$GGkey->seSrd2Rel->srd2SemregRel->semregSemesterRel->semester_name}</td>
													<td>{$GGkey->sePrevGradeRel->gs_ltr_grade}</td>
													<td>{$ng}</td>
													<td>{$option}</td>											
											</tr>";
										}
									?>
								</tbody>
							</table>
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	window.cancelGrade = function(reqId){
		if (confirm('You want to cancel this grade?')) {
			$.post('/request-sublimintary-exam-grade/cancel-grade-last',{reqId:reqId})
			.done(function(data){
				if (data == 1) {
					window.location.href = window.location;
				}else{
					alert(data);
				}
			});
		}
	}
	window.SubmitGrade = function(reqId,gsId){
		if (confirm('You want to assign this grade?')) {
			$.post('/request-sublimintary-exam-grade/submit-grade-last',{reqId:reqId,gsId:gsId})
			.done(function(data){
				if (data == 1) {
					window.location.href = window.location;
				}else{
					alert(data);
				}
			});
		}
	}
</script>