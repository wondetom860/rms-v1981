<?php 
	// use yii\helpers\Html;
	$modleSemreg = $semregs;
 ?>

<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-info">Semester Grade Report</h5>
		<h4>Student Name: <?= $model->getFullName() ?></h4>
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive">
	<?php if ($modleSemreg && count($modleSemreg) > 0) {
		?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead style="background-color: #DFD;">
				<th>#</th>
				<th>Academic Year</th>
				<th>Semester</th>
				<th>Class Level</th>
				<th>SGPA</th>
				<th>CGPA</th>
				<th>Status</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				foreach ($modleSemreg as $semreg) {
					$sgpa = " - ";
					$cgpa = " - ";
					$status = '-';
					$btn = "<button class='btn btn-xs btn-success pull-right btn__semregs' id='btn__semreg{$semreg->id}' ttile='course registration detail of this ssemester' onclick='showMoreDetail({$semreg->id});return false'>More</button>";
					if ($semreg->semreg_gradeCompleted == 1) {
						$sgpa = sprintf("%0.2f",$semreg->semreg_SGPA);
						$cgpa = sprintf("%0.2f",$semreg->semreg_CGPA);
						$status = $semreg->semesterLastSsnRel ? $semreg->semesterLastSsnRel->sssn_name : '';
					}else{
						$sgpa = " - ";
						$cgpa = " - ";
					}
					?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $semreg->semregAcadYearRel->sch_year_desc ?></td>
						<td><?= $semreg->semregSemesterRel->semester_name ?></td>
						<td><?= $semreg->semregClslvlRel->getClslvlDisp() ?></td>
						<td><?= $sgpa ?></td>
						<td><?= $cgpa ?></td>
						<td><?= $status ?></td>
						<td><?= $btn ?></td>
					</tr>
					<?php
				}?>
			</tbody>
		</table>
		<hr>
		<div class="container-fluid" id="mode_detail_container"></div>
		<?php 
	}else{
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>

<script type="text/javascript">
	window.showMoreDetail = (semregId) => {
		$("#mode_detail_container").html('Loading more detail..');

		$('.btn__semregs').removeClass('btn-info');
		$('#btn__semreg'+semregId).addClass('btn-info');

		$("#mode_detail_container").focus();

		$.post('/semester-registration2/get-srd2-detail',{semregId:semregId}).done((resp) => { $("#mode_detail_container").html(resp); })
	}
</script>
