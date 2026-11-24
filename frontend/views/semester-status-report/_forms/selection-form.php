<?php 


?>

<div class="bg-dark">
	<div class="row">
		<div class="col-md-3">
			<label>Academic Year</label>
			<select class="form-control" id="filter_acad_year">
				
			</select>
		</div>
		<div class="col-md-3">
			<label>Semester</label>
			<select class="form-control" id="fileter_semester">
				
			</select>
		</div>
		<div class="col-md-2">
			<h4>Program</h4>
			<?php


			$status = \backend\models\Program::find()->all();

			if ($status && count($status) > 0) {
				echo "<div class='container-fluid' style= 'height:80px; overflow:auto; background-color:#da9;' >";
				foreach ($status as $keyProgram) {
					echo "{$keyProgram->program_name}<input class='pull-right' type='checkbox' name='{$keyProgram->program_name}' id='status_{$keyProgram->id}' onchange='addProgramToList({$keyProgram->id})'><br>";
				}
				echo "</div>";
			}

			?>
		</div>
		<div class="col-md-3">
			<h4>Status</h4>
			<?php


			$status = \backend\models\StudentSemesterStatusName::find()->all();

			if ($status && count($status) > 0) {
				echo "<div class='container-fluid' style= 'height:80px; overflow:auto; background-color:#dd9;' >";
				foreach ($status as $keyStatus) {
					echo "{$keyStatus->sssn_name}<input class='pull-right' type='checkbox' name='{$keyStatus->sssn_name}' id='status_{$keyStatus->id}' onchange='addStatusToList({$keyStatus->id})'><br>";
				}

				echo "</div>";
			}

			?>
		</div>
		
	</div>
</div>

<script type="text/javascript">
	window.addStatusToList = (statusId)=>{
		console.log(statusId);
	}

	window.addProgramToList = (programId)=>{
		console.log(programId);
	}
</script>