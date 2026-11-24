<?php 

if (isset($model) && count($model) > 0) {
	$count = 0;
?>
<div class="continer-fluid table-responsive">
	<table class="table table-condensed table-striped table-hover table-xs table-bordered">
		<caption style="font-weight:normal; color: #000;text-align: center;">
			<h4>Changed Course Grades List Report</h4>
		</caption>
		<thead>
			<th>#</th>
			<th>Reg. ID</th>
			<th>Student Name</th>
			<th>Academic Year</th>
			<th>Course Title</th>
			<th>Credit Hrs.</th>
			<th>Prev. grade</th>
			<th>Changed To</th>
			<th>Reason of Change</th>
			<th>Status</th>
			<th>Remark</th>
		</thead>
		<tbody>
			<?php foreach ($model as $keytg): ?>
				<tr>
					<td><?php echo ++$count ?></td>
					<td><?php echo $keytg->tgSemregRel->semregStdRel->std_regId ?></td>
					<td><?php echo $keytg->tgSemregRel->semregStdRel->getFullName() ?></td>
					<td><?php echo $keytg->tgAcadYearRel->sch_year_desc."-".$keytg->tgSemRel->semester_name ?></td>
					<td><?php echo $keytg->tgSrd2Rel->srd2CourseRel->course_title ?></td>
					<td><?php echo $keytg->tgSrd2Rel->srd2CourseRel->getCourseCredits() ?></td>
					<td style="text-align: center; font-weight: bold;">
						<?php echo $keytg->tg_beforeGrade_relId ? $keytg->tgBeforeGradeRel->gs_ltr_grade : '-' ?>
					</td>
					<td style="text-align: center; font-weight: bold;">
						<?php echo $keytg->tg_afterGrade_relId ? $keytg->tgAfterGradeRel->gs_ltr_grade : '-' ?>
					</td>
					<td>
						<?php echo $keytg->tg_reason ?>
					</td>
					<td>
						<?php echo $keytg->tg_approved_by ? "Approved" : 'Pending approval' ?>
					</td>
					<td></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php }else{
	echo "<p class='bg-warning'>No record.</p>";
	return;
}
?>


