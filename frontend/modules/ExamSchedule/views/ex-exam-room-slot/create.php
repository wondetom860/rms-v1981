<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExamRoomSlot */

$this->title = Yii::t('rms', 'Create Ex Exam Room Slot');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ex Exam Room Slots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-exam-room-slot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
