<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td title="<?= $keySrd2->srd2CourseRel->course_title ?>"><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td>
					<?php 
						$supExam = $keySrd2->seSrd2Rel;
						$count1 = 0;
						if ($supExam && count($supExam) > 0) {
							echo "<table class='table table-condensed table-bordered table-sm' cellpadding='1' cellspacing='0'>";
							$bfg = $supExam->se_prevGrade_relId ? $supExam->sePrevGradeRel->gs_ltr_grade:'-';
							$afg = $supExam->se_newGrade_relId ? $supExam->seNewGradeRel->gs_ltr_grade:'-';

							$semreg = $keySrd2->srd2SemregRel;
							$acadYear = $semreg->semregAcadYearRel->sch_year_desc."-".$semreg->semregSemesterRel->semester_name;

							echo "<tr>
									<td>".++$count1."</td>
									<td>{$acadYear}</td>
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