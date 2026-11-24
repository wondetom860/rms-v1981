<?php
?>
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="container-fluid">
				<table class="table table-bordered table-condensed table-striped" style="border-color: black; border-spacing: 0px; font-size: 9pt; width: 100%;">
					<caption style="font-weight: normal; color: black;">
						<center>
							<h3 style="text-decoration: underline">Admitted Students List for Academic Year of <b><?= $modelAcadYear->sch_year_desc ?></b></h3>
							Program : <b><?= $modelAdm->admission_name . ' ' . $modelProg->program_name ?></b>
						</center>
					</caption>
					<thead style="background-color: #8D0; border: 1px solid black;">
						<th>#</th>
						<th>Reg. ID</th>
						<th>Full Name</th>
						<th>Gender</th>
						<th>Dept.</th>
						<th>Spec.</th>
						<th>Nationality</th>
						<th>Type</th>
						<th>Sponsorship</th>
					</thead>
					<tbody>
						<?php
						$count = 0;
						foreach ($admittedStudents as $ASkey) {
							$nation = is_null($ASkey->stdUserRel->nationality) || $ASkey->stdUserRel->nationality == 0 ? 'Uknown' : \backend\models\Nationality::findOne($ASkey->stdUserRel->nationality)->name;
							$scholar = is_null($ASkey->std_schlrshpType_relId) ? 'Uknown' : \backend\models\ScholarshipType::findOne($ASkey->std_schlrshpType_relId)->schType_name;
							$spec = !is_null($ASkey->stdSpecRel) ? $ASkey->stdSpecRel->spec_name : '';
							$dd = !is_null($ASkey->stdDeptRel) ? $ASkey->stdDeptRel->dept_code : 'Not Set';
							echo "<tr>
									<td>" . ++$count . "</td>
									<td>{$ASkey->std_regId}</td>
									<td>{$ASkey->stdUserRel->first_name} {$ASkey->stdUserRel->middle_name} {$ASkey->stdUserRel->last_name}</td>
									<td>{$ASkey->stdUserRel->gender}</td>
									<td>{$dd}</td>
									<td>{$spec}</td>
									<td>{$nation}</td>
									<td>{$ASkey->std_type}</td>
									<td>{$scholar}</td>
								</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>