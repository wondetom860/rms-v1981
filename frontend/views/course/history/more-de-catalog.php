
<div class="row">
	<div class="col-md-12" style="border: 0px solid black; font-size: 9pt;">
		<?php 
			$catalogEntries = $course->courseCatalogs;

			if ($catalogEntries && count($catalogEntries) > 0) {
				echo "<table class='table table-condensed table-striped table-bordered'>";
				//echo "<caption style='font-weight:bold; padding-left:10px; background-color: #6AD; color:black;'></caption>";
				echo "<thead style='background-color: #8D0;'>
						<th>#</th>
						<th>Department</th>
						<th>Program</th>
						<th>Class Level</th>
						<th>Semester</th>
						<th>Status</th>
						<th style='background-color: #8DD;'>Option</th>
					</thead>";
					$count = 0;
				foreach ($catalogEntries as $catalog){
					$clslvl = $catalog->cocatClslvlRel ? $catalog->cocatClslvlRel->getClslvlDisp() : '';
					$sem = $catalog->cocatSemRel ? $catalog->cocatSemRel->semester_name : '';
					$status = $catalog->cocat_status ? 'Active' : 'Not Active';
					$btn = '-';
					$spec = $catalog->cocatSpecRel ? "({$catalog->cocatSpecRel->spec_name})" : '';
					if (Yii::$app->user->can('Registrar')) {
						$btn = $catalog->cocat_status ? 
							"<button class='btn btn-danger btn-xs' title='Turn off offering? After changing status, course will not be listed for offering.' onclick='changeStatus({$catalog->id});return false;'>Hide for offering</button>"
							:
							"<button class='btn btn-warning btn-xs' title='Make active for offering? After change, course will be listed for offering.' onclick='changeStatus({$catalog->id});return false;'>Activate</button>";
					}
					echo "<tr>
							<td>".++$count."</td>
							<td>{$catalog->cocatDeptRel->dept_code}{$spec}</td>
							<td>{$catalog->cocatAdmissionRel->admission_name[0]}{$catalog->cocatProgramRel->program_name[0]}</td>
							<td>{$clslvl}</td>
							<td>{$sem}</td>
							<td>{$status}</td>
							<td>{$btn}</td>
						</tr>";
				}

				echo "</table>";
			}


		?>
	</div>
</div>
