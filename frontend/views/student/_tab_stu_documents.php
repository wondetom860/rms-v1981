<?php

use yii\helpers\Html;

$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
$modelEI = ($modelStudent->st_educ_info_relId) ? $modelStudent->stdEducInfoRel : NULL;
$total = $modelEI ? $modelEI->ei_enteranceMarkPoint : '-';

$modelSSA = ($modelEI) ? \backend\models\SecondarySchoolAttended::find()->where(['ssa_ei_relId' => $modelEI->id])->all() : NULL;
$modelEEM = ($modelEI) ? \backend\models\EnteranceExamMark::find()->where(['eem_ei_relId' => $modelEI->id])->all() : NULL;
?>

<div class="row">
	<div class="col-xs-12">
		<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">
			<i class="fa fa-files-o"></i> <?= Html::encode(Yii::t('stu', 'Education Documents')) ?>
		</h4>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<h5 class="page-header bg-info">1. Secondary Schools Attended</h5>
	</div><!-- /.col -->
</div>
<div class="container-fluid table-responsive">
	<?php if ($modelSSA && count($modelSSA) > 0) {
	?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead>
				<th>#</th>
				<th>School Name</th>
				<th>Town</th>
				<th>Year</th>
				<th>Grade</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				foreach ($modelSSA as $SSAkey) {
				?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $SSAkey->ssa_school_name ?></td>
						<td><?= $SSAkey->ssa_town ?></td>
						<td><?= $SSAkey->ssa_year ?></td>
						<td><?= $SSAkey->ssa_gradeLevels ?></td>
						<td>
							<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right' ?>">
								<?= Html::a('<i class="fa fa-pencil-square-o"></i> ' . Yii::t('rms', 'Edit'), ['#'], ['onclick' => "updateSecondarySchool({$SSAkey->id});return false;"]) ?>

								<?= Html::a('<i class="fa fa-trash-o"></i> ' . Yii::t('rms', 'Delete'), ['/secondary-school-attended/delete', 'id' => $SSAkey['id']], [
									'class' => 'btn btn-danger btn-xs',
									'data' => [
										'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]) ?>


							</div>
						</td>
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

<div class="row">
	<div class="col-xs-12">
		<h5 class="page-header bg-info">2. Entrance Examination Mark
			<button class="btn btn-xs btn-primary pull-right" onclick="addNewEEM(<?= $modelStudent->id ?>)">Add New</button>
		</h5>
	</div>
</div>

<div class="container-fluid table-responsive">
	<?php if ($modelEEM && count($modelEEM) > 0) {
	?>
		<table border="1" class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead style="background-color:#8D0;">
				<th>#</th>
				<th>Subject</th>
				<th>Ref. #</th>
				<th>Year</th>
				<th>Mark</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				// $total = 0;
				foreach ($modelEEM as $EEMkey) {
					// $total += $EEMkey->eem_markObtained;
				?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $EEMkey->eem_subject ?></td>
						<td><?= $EEMkey->eem_reference_number ?></td>
						<td><?= $EEMkey->eem_year ?></td>
						<td style="text-align:center;"><?= $EEMkey->eem_markObtained ?></td>
						<td>
							<div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right' ?>">
								<?= Html::a('<i class="fa fa-pencil-square-o"></i> ' . Yii::t('rms', 'Edit'), ['#'], ['onclick' => "updateEnteranceMark({$EEMkey->id});return false;"]) ?>

								<?= Html::a('<i class="fa fa-trash-o"></i> ' . Yii::t('rms', 'Delete'), ['/enterance-exam-mark/delete', 'id' => $EEMkey['id']], [
									'class' => 'btn btn-danger btn-xs',
									'data' => [
										'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]) ?>


							</div>
						</td>
					</tr>
				<?php
				} ?>
				<tr style="background-color: #ACE; text-align: center; font-weight: bold; text-decoration:underline;">
					<td colspan="4">Total</td>
					<td><?= $total ?></td>
					<td>-</td>
				</tr>
			</tbody>
		</table>
	<?php
	} else {
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>



<script>
	const addNewEEM = (student_id) => {
		$('#modal').find('.modal-header').html('Enter New University Enterance Exam Result');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/enterance-exam-mark/create2', {
			std_id: student_id,
			ar_request: 1,
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
	const updateEnteranceMark = (id) => {
		$('#modal').find('.modal-header').html('Update University Enterance Exam Result');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/enterance-exam-mark/update', {
			id: id,
			ar_request: 1,
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
	const updateSecondarySchool = (id) => {
		$('#modal').find('.modal-header').html('Secondary School Attended:');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/secondary-school-attended/update', {
			id: id,
			ar_request: 1,
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
</script>