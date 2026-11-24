<?php

use yii\helpers\Html;

/** @var yii\web\View  $this*/
/* @var $model frontend\modules\ExamSchedule\models\ExDateTimeSchedule */
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->title = Yii::t('rms', 'Create Date Time Schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Date Time Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-date-time-schedule-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
