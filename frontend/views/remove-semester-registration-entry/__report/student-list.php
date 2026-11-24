<?php 


if ($model && count($model) > 0) {
?>

<table class="table table-condensed table-sm table-bordered" cellpadding="2" cellspacing="0">
	<thead>
		<th>#</th>
		<th>Reg. Id</th>
		<th>Student Name</th>
		<th>Admission Year</th>
		<th>Department</th>
		<th>Spec.</th>
		<th>Show</th>
	</thead>
	<tbody>
	<?php $count = 0; foreach ($model as $keyStudent): ?>
		<tr>
			<td><?= ++$count ?></td>
			<td><?= $keyStudent->std_regId ?></td>
			<td><?= $keyStudent->getFullName() ?></td>
			<td><?= $keyStudent->stdAcadYearRel->sch_year_desc."-".$keyStudent->stdSemRel->semester_name ?></td>
			<td><?= $keyStudent->stdDeptRel->dept_name ?></td>
			<td><?= $keyStudent->std_spec_relId ? $keyStudent->stdSpecRel->spec_name:"-" ?></td>
			<td><button class="btn btn-primary btn-xs" onclick="showEnrollments(<?= $keyStudent->id ?>);return false;" title="Show Enrollments">Enrollments</button></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
<script type="text/javascript">
	window.showEnrollments = (stdId) => {
		$("#enrollments_container").html('Loading enrollment histories...');

		$.post('/remove-semester-registration-entry/get-enrollments',{stdId:stdId}).done((resp) => { $("#enrollments_container").html(resp); });
	}
</script>
<?php }else{
	echo "<p class='bg-warning'>No result.</p>";
}

?>