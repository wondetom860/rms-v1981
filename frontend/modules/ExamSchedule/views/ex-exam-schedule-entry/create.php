<?php

use yii\helpers\Html;

/** @var yii\web\View  $this*/
/* @var $model frontend\modules\ExamSchedule\models\ExExamScheduleEntry */

$this->title = Yii::t('rms', 'Add New Exam Schedule Entry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exam Schedule Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-exam-schedule-entry-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
