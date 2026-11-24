<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule */

$this->title = Yii::t('rms', 'Update Department Evaluation Schedule: {name}', [
    'name' => $model->headerRel->evaluation_header." [{$model->criteriaBundleRel->code}]",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Evaluation Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->headerRel->evaluation_header, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
$model->startdate = date("d-m-Y",$model->start_date);
$model->enddate = date("d-m-Y",$model->end_date);
?>
<div class="ase-department-evaluation-schedule-update">

    <h3><?= Html::encode($this->title) ?><span class="bg-warning pull-right" style="font-size: 9pt; font-style: italic;">Scheude date must be between <?= $model->headerRel->getScheduleInterval() ?></span></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
