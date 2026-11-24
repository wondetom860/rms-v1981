<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExDateTimeSchedule */

$this->title = $model->getHeaderText();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Date Time Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ex-date-time-schedule-view">

    <h3><?= "Time Schedule for: <i>" . Html::encode($this->title) . '</i>' ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-xs btn btn-primary']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs ',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date:date',
            'start_time:time',
            'end_time:time',
            [
                'attribute' => 'Exam Room Slots',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('../ex-exam-room-slot/__report/exam-room-slots', ['model' => $model, 'addRoomSlotBtn' => true]);
                },
            ],
            [
                'attribute' => 'standbys',
                'label' => 'Standbys',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/_standby', ['model' => $model, 'addStandbyBtn' => true]);
                },
            ],
            [
                'attribute' => 'supervisors',
                'label' => 'Supervisors',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/_supervisor', ['model' => $model, 'addSupervisorBtn' => true]);
                },
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('dMY', $model->created_at);
                },
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy->username;
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return $model->updated_at ? date('dMY', $model->updated_at) : '';
                },
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updatedBy ? $model->updatedBy->username : '';
                },
            ],
        ],
    ]) ?>

</div>