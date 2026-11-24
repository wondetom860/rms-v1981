<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcProgramSchedule */
$model->start_date = date("d-m-Y",$model->start_date);
$model->end_date = date("d-m-Y",$model->end_date);
$this->title = Yii::t('rms', 'Update Program Schedule: {name}', [
    'name' => $model->actionRel->aca_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Program Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->actionRel->aca_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ac-program-schedule-update">

    <h3><?= Html::encode($this->title)." <i>of [".$model->activeProgramRel->academicYearRel->getAcHeader()."] for <u>{$model->activeProgramRel->getClassLevelHeader()}</u></i>" ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
