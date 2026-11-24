<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule */

$this->title = Yii::t('rms', 'Create Department Evaluation Schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Evaluation Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-department-evaluation-schedule-create">

    <h3><?= Html::encode($this->title) ?><span class="bg-warning pull-right" style="font-size: 9pt; font-style: italic;">Scheude date must be between <?= $model->headerRel->getScheduleInterval() ?></span></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
