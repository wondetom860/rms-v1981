<?php


if (isset($modelRequest)) {
	if (is_array($modelRequest) && count($modelRequest) > 0) {
		// $modelSupExam = new \backend\models\SupplementaryExam();
		?>
		<table class="table table-hover table-condensed table-bordered table-striped">
			<thead style="background-color: #8D0;">
				<th>#</th>
				<th>Reg. Id</th>
				<th>Full Name</th>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Prev. Grade</th>
				<th>New Grade</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($modelRequest as $Rkey) {
					$course = $Rkey->seSrd2Rel->srd2CourseRel;
					$newGrade = is_null($Rkey->se_newGrade_relId)?' - ':$Rkey->seNewGradeRel->gs_ltr_grade;

					$dd = date_format(date_create($Rkey->se_created_at),"d M, Y");
					$btn = "";
					if (Yii::$app->user->can('Student')) {
						$btn = "<button class='btn btn-warning btn-xs' onclick='deleterequest({$Rkey->id})' title = 'Requested On {$dd}'>Cancel</button>";
						$btn = ($Rkey->isRequested($Rkey->seSrd2Rel) && is_null($Rkey->se_registrarApproved) && is_null($Rkey->se_registrarApproved))?$btn:'<span class="bg-info">On_Process</span>';
					}else if (Yii::$app->user->can('Registrar')) {
						$btn = is_null($Rkey->se_registrarApproved)?
							"<button class='btn btn-primary btn-xs' onclick='approveRequest({$Rkey->id})' title = 'Requested On {$dd}'>Approve</button>":
							"<button class='btn btn-warning btn-xs' onclick='depproveRequest({$Rkey->id})' title = 'Requested On {$dd}'>Cancel</button>";
							;
						if (!is_null($Rkey->se_hodApproved)) {
							$btn = "<span title = 'Requested On {$dd}'>approved by Hod</span>";
						}
					}else if(Yii::$app->user->can('DepartmentHead')){
						if (!is_null($Rkey->se_registrarApproved) && is_null($Rkey->se_graded)) {
							$btn = is_null($Rkey->se_hodApproved)?
								"<button class='btn btn-primary btn-xs' onclick='approveRequest({$Rkey->id})' title = 'Requested On {$dd}'>Approve</button>":
								"<button class='btn btn-warning btn-xs' onclick='depproveRequest({$Rkey->id})' title = 'Requested On {$dd}'>Cancel</button>"
								;
						}
					}else{
						$btn = "Requetsed on {$dd}";
					}
					echo "<tr>
							<td>".++$count."</td>
							<td>{$Rkey->seStdRel->std_regId}</td>
							<td>{$Rkey->seStdRel->stdUserRel->first_name} {$Rkey->seStdRel->stdUserRel->middle_name}  {$Rkey->seStdRel->stdUserRel->last_name} </td>
							<td>{$course->course_code}</td>
							<td>{$course->course_title}</td>
							<td>{$Rkey->sePrevGradeRel->gs_ltr_grade}</td>
							<td>{$newGrade}</td>
							<td>{$btn}</td>
					</tr>";
				}
				?>
			</tbody>
		</table>
		<?php
		
	}else{
		echo is_array($modelRequest)?"[]":"<p class = 'bg-warning'>Invalid record! [{$modelRequest}].</p>";
	}
}else{
	echo "<p class = 'bg-warning'>No record found!.</p>";
}
?>

<script type="text/javascript">

	window.depproveRequest = function(id){
		if (confirm("You want to continue")) {
			$.post('/request-sublimintary-exam-grade/de-approve-request?id='+id)
			.done(function(data){
				if (data == 1) {
					window.location.href = window.location;
				}else{
					alert(data);
				}
			});
		}
	}
	window.approveRequest = function(id){
		if (confirm("You want to continue")) {
			$.post('/request-sublimintary-exam-grade/approve-request?id='+id)
			.done(function(data){
				if (data == 1) {
					window.location.href = window.location;
				}else{
					alert(data);
				}
			});
		}
	}

	window.deleterequest = function(id){
		if (confirm("You want to delete request?")) {
			$.post('/request-sublimintary-exam-grade/delete-request?id='+id)
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