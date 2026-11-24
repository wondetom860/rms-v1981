<?php

use yii\helpers\Html;

/** @var yii\web\View  $this*/
/* @var $model frontend\modules\ExamSchedule\models\ExDateTimeSchedule */

$this->title = Yii::t('rms', 'Update Date Time Schedule: {name}', [
    'name' => $model->getHeaderText(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Date Time Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getHeaderText(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
$model->start_time_str = date('H:i A', $model->start_time);
$model->end_time_str = date('H:i A', $model->end_time);
$model->date_str = date('d-m-Y', $model->date);
?>
<div class="ex-date-time-schedule-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
