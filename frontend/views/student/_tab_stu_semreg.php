<?php

use yii\helpers\Html;

$modleSemreg = $modelStudent
	->getSemesterRegistrations()
	->innerJoinWith('semregClslvlRel')
	->orderBy([
		'clslvl_num' => SORT_ASC,
		'semreg_acadYear_relId' => SORT_ASC,
		'semreg_semester_relId' => SORT_ASC,
	])
	->all();
?>

<div class="row">
	<div class="col-xs-12" style="font-size: 9pt;">
		<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">
			<i class="fa fa-grade"></i> <?= Html::encode(Yii::t('stu', 'Semester Reports')) ?>
		</h4>
	</div>
</div>

<div class="row hidden">
	<div class="col-xs-12">
		<h5 class="page-header bg-info"> Semester Reports</h5>
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive">
	<?= $modelStudent->entry_type != 1 ?
		"<button class='btn btn-xs btn-warning pull-right' onclick='createSemregEntry({$modelStudent->id});return false;' title='Add registration entry'>Add Reg. Entry</button>" : '' ?>
	<?php if ($modleSemreg && count($modleSemreg) > 0) {
	?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead style="background-color: #DFD;">
				<th>#</th>
				<th>Class Level</th>
				<th>Semester</th>
				<th>Academic Year</th>
				<th>SGPA</th>
				<th>CGPA</th>
				<th>Status</th>
				<th>Reports</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				$sgpa = " - ";
				$cgpa = " - ";
				$status = '-';
				$prntGrp = "";
				$btnRegrade = "";
				foreach ($modleSemreg as $semreg) {
					$btnRmvSmrg = '';
					$btnEnrol = '';
					$btnRegrade = "";
					if ($semreg->semreg_entry_type != 1) {
						if (count($semreg->semesterRegistration2Details) == 0) {
							$btnRmvSmrg = "<button class='btn btn-xs btn-warning pull-right' onclick='removeSemreg({$semreg->id});return false;' title='Remove this registration entry' style='margin: 0px 2px;'>X</button>";
						}
						$btnEnrol = "<button class='btn btn-xs btn-primary pull-right' onclick='enroll({$semreg->id});return false;' title='Enroll courses' style='margin: 0px 2px;'>+</button>";
					}
					$prntSlip = "<button class='btn btn-xs btn-info pull-right' onclick='slipReport({$semreg->id});return false;' style='margin: 0px 2px;'>Slip</button>";
					if ($semreg->semreg_gradeCompleted == 1) {
						$sgpa = sprintf("%0.2f", $semreg->semreg_SGPA);
						$cgpa = sprintf("%0.2f", $semreg->semreg_CGPA);
						$status = $semreg->semesterLastSsnRel ? $semreg->semesterLastSsnRel->sssn_name : '';
						$prntGrp = "<button class='btn btn-xs btn-secondary pull-right' onclick='gradeReport({$semreg->id});return false;'>Grade</button>";
						// check if any course graded gp<2 exists:
						// $btnRegrade = "";
						if(($semreg->semreg_gradeCompleted == 1) && $semreg->regradeRequestable())
							$btnRegrade = "<button class='btn btn-xs btn-warning pull-right' title='Request for regrading course' onclick='requestRegrade({$semreg->id});return false;'>ReGrade</button>";
					} else {
						$sgpa = " - ";
						$cgpa = " - ";
						$status = '-';
						$prntGrp = "";
					}
				?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $semreg->semregClslvlRel->getClslvlDisp() ?></td>
						<td><?= $semreg->semregSemesterRel->semester_name ?></td>
						<td><?= $semreg->semregAcadYearRel->sch_year_desc ?></td>
						<td><?= $sgpa ?></td>
						<td><?= $cgpa ?></td>
						<td><?= $status ?></td>
						<td><?= $prntGrp . $prntSlip . $btnRmvSmrg . $btnEnrol . $btnRegrade ?></td>
					</tr>
				<?php
				} ?>
			</tbody>
		</table>
	<?php
	} else {
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>
<script type="text/javascript">
	window.requestRegrade = (semregId) => {
		$('#modal').find('.modal-header').html('Request for Regrading');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$.ajax({
			url: '/semester-registration2/regrade-courses',
			type: 'POST',
			data: {
				semregId: semregId
			},
			success: function(data) {
				$('#modal').modal('show');
				$('#modalContent').html(data);
			}
		});
	}
	window.enroll = (semregId) => {
		$('#modal').find('.modal-header').html('Enroll courses for transfer student');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$.ajax({
			url: '/semester-registration2/enroll-courses',
			type: 'POST',
			data: {
				semregId: semregId
			},
			success: function(data) {
				$('#modal').modal('show');
				$('#modalContent').html(data);
			}
		});
	}
	window.removeSemreg = (semregId) => {
		if (confirm('Are you sure to remove this entry')) {
			$.post('/semester-registration2/delete?id=' + semregId);
		}
	}
	window.createSemregEntry = (stdId) => {
		$('#modal').find('.modal-header').html('Create semester registration entry for student');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$.ajax({
			url: '/semester-registration2/create-entry',
			type: 'POST',
			data: {
				stdId: stdId
			},
			success: function(data) {
				$('#modal').modal('show');
				$('#modalContent').html(data);
			}
		});
	}
	const slipReport = (semregId) => {
		__semregId = semregId;
		myWindow = window.open(
			'/enrolled-students-report/get-generated-report?semregId=' + semregId,
			'title=System Slip Report',
			'height=800,width=760');
	}
	const gradeReport = (semregId) => {
		__semregId = semregId;
		myWindow = window.open(
			'/semester-registration/get-semester-grade-report?semregId=' + semregId,
			'title=Student Semester Grade Report',
			'height=800,width=990');
	}
</script>