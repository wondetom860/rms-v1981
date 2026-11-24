<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td title="<?= $keySrd2->srd2CourseRel->course_title ?>"><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td>
					<?php 
						$contAssess = $keySrd2->continuousAssessmentEntries;

						if ($contAssess && count($contAssess) > 0) {
							$totalObtained = 0;
							$totalAssessed = 0;
							foreach ($contAssess as $keyCA) {
								$totalObtained += $keyCA->ca_markObtained;
								$totalAssessed += $keyCA->caMarkAssignmentRel->ima_possiblePoint;
								echo "<p style='padding:0px;'>{$keyCA->caMarkAssignmentRel->ima_instanceName}: <span class='bg-info pull-right'>{$keyCA->ca_markObtained}/{$keyCA->caMarkAssignmentRel->ima_possiblePoint}</span></p>";
							}
							echo "<p class='bg-success'>Total: <span class='bg-info pull-right'>{$totalObtained}/{$totalAssessed}</span></p>";
						}else{
							echo "<p style='padding:0px;'>None</p>";
						}
					?>
				</td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>