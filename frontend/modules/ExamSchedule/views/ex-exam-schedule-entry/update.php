<?php

use yii\helpers\Html;

/** @var yii\web\View  $this*/
/** @var frontend\modules\ExamSchedule\models\ExExamScheduleEntry  $model*/

$model->start_date_str = date('d-m-Y', $model->start_date);
$model->end_date_str = date('d-m-Y', $model->end_date);

$this->title = Yii::t('rms', 'Update Exam Schedule Entry: {name}', [
    'name' => $model->acadYearRel->getAcHeader(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exam Schedule Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ex-exam-schedule-entry-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>