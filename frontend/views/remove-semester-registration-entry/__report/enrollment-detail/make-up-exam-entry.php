<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td title="<?= $keySrd2->srd2CourseRel->course_title ?>"><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td>
					<?php 
						$dropEntry = $keySrd2->cdhSrd2Rel;
						$count1 = 0;
						if ($dropEntry && count($dropEntry) > 0) {
							$gset = $dropEntry->cdhGroupRel;
							echo "<table class='table table-condensed table-bordered table-sm' cellpadding='1' cellspacing='0'>";
							echo "<tr>
									<td>Dropped the course enrolled in {$gset->gsClslvlRel->getClslvlDisp()}, with group {$gset->gsGroupRel->group_name} students.</td>
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