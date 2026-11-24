<?php if (isset($srd2s) && count($srd2s) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<tbody>
		<?php $count = 0; foreach ($srd2s as $keySrd2): ?>
			<tr>
				<td><?= ++$count ?></td>
				<td><?= $keySrd2->srd2CourseRel->course_code ?></td>
				<td><?= $keySrd2->srd2CourseRel->course_title ?></td>
				<td><?= $keySrd2->srd2CourseRel->course_crdthr ?></td>
				<td><?= $keySrd2->srd2_grade_relId ? $keySrd2->srd2GradeRel->gs_ltr_grade:"-" ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>