<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td title="<?= $keySrd2->srd2CourseRel->course_title ?>"><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td>
					<?php 
						$reass = $keySrd2->raSrd2Rel;
						$count1 = 0;
						if ($reass && count($reass) > 0) {
							echo "<table class='table table-condensed table-bordered table-sm' cellpadding='1' cellspacing='0'>";
							$bfg = $reass->ras_bef_grade_relId ? $reass->rasBefGradeRel->gs_ltr_grade:'-';
							$afg = $reass->ras_aft_grade_relId ? $reass->rasAftGradeRel->gs_ltr_grade:'-';
							echo "<tr>
									<td>".++$count1."</td>
									<td>{$reass->rasAcadYearRel->sch_year_desc}-{$reass->rasSemRel->semester_name}</td>
									<td>{$bfg}</td>
									<td>{$afg}</td>
								</tr>";
							echo "</table>";
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