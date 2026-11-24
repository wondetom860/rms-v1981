<?php

use frontend\modules\AcademicCalendar\models\AcActivePrograms;

$activeProgramsModel = \Yii::$app->AcademicCalendar->getActiveProgramModels();
$modelActiveProgram = new \frontend\modules\AcademicCalendar\models\AcActivePrograms();
// checkProgramActive
$activePrograms = null;
$activeAcademicYear = null;
if ($activeProgramsModel && count($activeProgramsModel) > 0) {
	// foreach ($activeProgramsModel as $activeProgramModel) {}

	$activeAcademicYear = $activeProgramsModel[0];
	if (!\Yii::$app->user->isGuest) {
		if (\Yii::$app->user->can('Student')) {
			$student = \backend\models\Student::findOne([
				'std_user_relId' => \Yii::$app->user->id,
				'std_semester_status' => 'ACTIVE'
			]);
			if ($student && count([$student]) > 0) {
				$activePrograms = $modelActiveProgram->checkProgramActive($student->stdGroupReal, true);
			} else {
				echo "Active status not known";
			}
		} else if (\Yii::$app->user->can('Instructor') || \Yii::$app->user->can('DepartmentHead')) {
			$instructor = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);

			if ($instructor && count([$instructor])) {
				$activePrograms = AcActivePrograms::find()
					->where(['dept_relId' => $instructor->inst_dep_relId])
					->andWhere(['status' => 1])
					->orderBy([
						'academic_year_relId' => SORT_DESC,
						'dept_relId' => SORT_ASC,
						'program_relId' => SORT_DESC,
						'class_level_relId' => SORT_ASC,
					])
					->all();
			}
		} else {
			// admin, registrar, academic dean, 
			$activePrograms = AcActivePrograms::find()
				->where(['status' => 1])
				->orderBy([
					'academic_year_relId' => SORT_DESC,
					'dept_relId' => SORT_ASC,
					'program_relId' => SORT_DESC,
					'class_level_relId' => SORT_ASC,
				])
				->all();
		}
	} else {
		// guest page shows all programs
		$activePrograms = AcActivePrograms::find()
			->where(['status' => 1])
			->orderBy([
				'academic_year_relId' => SORT_DESC,
				'dept_relId' => SORT_ASC,
				'program_relId' => SORT_DESC,
				'class_level_relId' => SORT_ASC,
			])
			->all();
	}
}
$mngBtn = \Yii::$app->user->can('Admin') ? "<a class='btn btn-xs btn-link pull-right' href='/AcademicCalendar/ac-active-programs/'>Manage</a>" : '';
if ($activePrograms && count($activePrograms) > 0) {
	$count = 0;
?>

	<!-- of <?php //$activeAcademicYear->getAcHeader() 
			?> -->

	<div class="col-md-12">
		<table class="table table-condensed table-bordered table-hover" style="font-size:9pt;">
			<caption style="color:black; font-weight: normal; text-align: center;">
				<h4>Active Academic Programs <?= $mngBtn ?></h4>
			</caption>
			<thead style="background-color: #DEF;">
				<th>#</th>
				<th>ACY</th>
				<th>Dept.</th>
				<th>Program</th>
				<th>Class Level</th>
				<th>Specialization</th>
				<th>Tasks and Schedules</th>
			</thead>
			<tbody>
				<?php foreach ($activePrograms as $activeProgram):
					// $icon = $activeProgram->status == 1 ? '<i class="fa fa-lg fa-warning fa-exclamation-circle blink pull-right"></i>':'';
					$aScheduls = $activeProgram->getActiveSchedules();
					if ($aScheduls === null) {
						continue;
					}
				?>
					<tr>
						<td><?= ++$count ?></td>
						<td><?= $activeProgram->getAcadYearHeader() ?></td>
						<td><?= $activeProgram->deptRel ? $activeProgram->deptRel->dept_code : '-' ?></td>
						<td> <?= $activeProgram->getProgramHeader() ?> </td>
						<td> <?= $activeProgram->classLevelRel->getClslvlDisp() . " Sem-{$activeProgram->semesterRel->semester_name}" ?> </td>
						<td><?= $activeProgram->spec_relId ? $activeProgram->specRel->spec_name : '' ?></td>
						<td><?= $aScheduls ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>


<?php
} else {
	echo " <p class='bg-danger'>No Active program found.</p>";
}
?>