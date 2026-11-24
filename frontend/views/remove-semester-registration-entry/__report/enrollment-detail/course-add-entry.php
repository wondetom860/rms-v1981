<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td title="<?= $keySrd2->srd2CourseRel->course_title ?>"><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td>
					<?php 
						$addHistory = $keySrd2->srd2_coadd_flag ? $keySrd2->ahSrd2Rel : null;
						$count1 = 0;
						if ($addHistory && count($addHistory) > 0) {
							$gset = $addHistory->ahAddWithGroupRel;
							echo "<table class='table table-condensed table-bordered table-sm' cellpadding='1' cellspacing='0'>";
							echo "<tr>
									<td>Course added with {$gset->gsClslvlRel->getClslvlDisp()}, group {$gset->gsGroupRel->group_name} students.</td>
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