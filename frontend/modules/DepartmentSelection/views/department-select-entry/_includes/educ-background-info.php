<?php 
	use yii\helpers\Html;
	$adminUser = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
	$modelEI = ($modelStudent->st_educ_info_relId)?$modelStudent->stdEducInfoRel:NULL;

	$modelSSA = ($modelEI)?\backend\models\SecondarySchoolAttended::find()->where(['ssa_ei_relId'=>$modelEI->id])->all():NULL;
	$modelEEM = ($modelEI)?\backend\models\EnteranceExamMark::find()->where(['eem_ei_relId'=>$modelEI->id])->all():NULL;
	$modelPSA = ($modelEI)?$modelEI->postSecondaryEducations:NULL;
 ?>
<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-success" style="padding: 4px;"><?= $modelStudent->getFullName() ?></h5>
	</div><!-- /.col -->
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
						<td><?=  $SSAkey->ssa_year ?></td>
						<td><?=  $SSAkey->ssa_gradeLevels ?></td>
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

<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-info">2. Entrance Examination Mark</h5>
	</div>
</div>

<div class="container-fluid table-responsive">
	<?php if ($modelEEM && count($modelEEM) > 0) {
		?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead>
				<th>#</th>
				<th>Subject</th>
				<th>Ref. #</th>
				<th>Year</th>
				<th>Mark</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				foreach ($modelEEM as $EEMkey) {
					?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $EEMkey->eem_subject ?></td>
						<td><?= $EEMkey->eem_reference_number ?></td>
						<td><?= $EEMkey->eem_year ?></td>
						<td><?= $EEMkey->eem_markObtained ?></td>
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

<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-info">3. Post Secondary Educations</h5>
	</div><!-- /.col -->
</div>
<div class="container-fluid table-responsive">
	<?php if ($modelPSA && count($modelPSA) > 0) {
		?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead>
				<th>#</th>
				<th>Institution Name</th>
				<th>Country</th>
				<th>From year - to</th>
				<th>Credit Hr.</th>
				<th>CGPA</th>
				<th>Certificate</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				foreach ($modelPSA as $PSEKey) {
					?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $PSEKey->pse_institute_name ?></td>
						<td><?= $PSEKey->pseNationalityRel->name ?></td>
						<td><?= $PSEKey->pse_from_year.' - '.$PSEKey->pse_to_year ?></td>
						<td><?= $PSEKey->pse_credit_earned ?></td>
						<td><?= $PSEKey->pse_CGPA_obtained ?></td>
						<td><?= $PSEKey->pse_certificate_earned ?></td>
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
