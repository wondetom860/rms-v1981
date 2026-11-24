<?php 
	// use yii\helpers\Html;
	$modleSrd2 = $srd2s;
	$model->calculateGPA();
 ?> 

<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-info"> Semester Registration Detail - <?= $model->semregStdRel->getFullName() ?>
			<small class="pull-right"><?= $model->semregAcadYearRel->sch_year_desc."-".$model->semregSemesterRel->semester_name ?></small>
		</h5>
		
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive">
	<?php if ($modleSrd2 && count($modleSrd2) > 0) {
		?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead style="background-color: #DFD;">
				<th>#</th>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Repeat status</th>
				<th>Total Mark</th>
				<th>Grade</th>
				<th>Approval</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				foreach ($modleSrd2 as $srd2) {
					$ltGrade = $srd2->srd2_grade_relId ? $srd2->srd2GradeRel->gs_ltr_grade : '-';
					$course = $srd2->srd2CourseRel;
					$crf = $srd2->srd2_corptd_flag ? $srd2->srd2_corptd_flag : '';
					$totalMark = $srd2->getTotalMark();
					?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $course->course_code ?></td>
						<td><?= $course->course_title ?></td>
						<td><?= $crf ?></td>
						<td><?= $totalMark ?></td>
						<td style="text-align: center;"><?= $ltGrade ?></td>
						<td style="text-align: center;"><?= $srd2->srd2_gradeApproved ?></td>
					</tr>
					<?php
				}?>
			</tbody>
		</table>
		<?php 
	}else{
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>
