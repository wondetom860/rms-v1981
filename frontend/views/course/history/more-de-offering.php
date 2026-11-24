<div class="row">
	<div class="col-md-12" style="border: 0px solid black; max-height: 300px; overflow: auto; font-size: 9pt;">
		<?php
		$offeringEntries = $course->getCourseOfferings()
			->joinWith('coff2detCoff2Rel')
			->orderBy([
				'coff2_acadYear_relId' => SORT_DESC,
				'coff2_sem_relId' => SORT_DESC,
			])
			->all();

		if ($offeringEntries && count($offeringEntries) > 0) {
			echo "<table class='table table-condensed table-striped table-bordered'>";
			//echo "<caption style='font-weight:bold; padding-left:10px; background-color: #6AD; color:black;'></caption>";
			echo "<thead style='background-color: #8D0;'>
						<th>#</th>
						<th>Department</th>
						<th>Program</th>
						<th>Class Level</th>
						<th>Ac. Yr.</th>
						<th>Entrolments</th>
						<th>Option</th>
					</thead>";
			$count = 0;
			foreach ($offeringEntries as $offering) {
				// gsClslvlRel->getClslvlDisp()
				$clslvl = $offering->coff2detCoff2Rel->coff2GroupRel->getGsDisp();
				$sem = $offering->coff2detCoff2Rel->getOfferingAcadeYearInfo();
				$srd2s = count($offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->all());
				$dltBtn = ($srd2s == 0 && Yii::$app->user->can('Registrar')) ? "<button class='btn btn-xs btn-warning' onclick='dltOffering({$offering->id}); return false;' title='Delete this offering'>Delete</button>" : '';
				echo "<tr>
							<td>" . ++$count . "</td>
							<td>{$offering->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->dept_name}</td>
							<td>{$offering->coff2detCoff2Rel->coff2GroupRel->gsAdmRel->admission_name[0]}{$offering->coff2detCoff2Rel->coff2GroupRel->gsProgRel->program_name[0]}</td>
							<td>{$clslvl}</td>
							<td>{$sem}</td>
							<td style='text-align:center;'>{$srd2s}</td>
							<td>{$dltBtn}</td>
						</tr>";
			}

			echo "</table>";
		}
		?>
	</div>
</div>

<script>
	const dltOffering = (offeringId) => {
		if (confirm('Are you sure to delete this offering?')) {
			$.post('/course-offering2-detail/delete?id=' + offeringId).done((resp) => {
				if (resp == 1) {
					window.location.href = window.location;
				} else {
					alert(resp);
				}
			});
		}
	}
</script>