<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExamRoomSlot */

$this->title = $model->getHeaderText();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exam Room Slots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ex-exam-room-slot-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
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

            [
                'attribute' => 'date_time_slot_relId',
                'value' => function ($model) {
                    return $model->getHeaderText();
                },
            ],
            [
                'attribute'=> 'Date',
                'value'=> function ($model) {
                    return $model->dateTimeSlotRel->getDateScheduled();
                }
            ],
            [
                'attribute' => 'class_room_relId',
                'value' => function ($model) {
                    return $model->classRoomRel->room;
                },
            ],
            [
                'attribute' => 'offering_detail_IDs',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/offering-detail', ['model' => $model,'addOfferingDetailBtn' => true]);
                },
            ],
            [
                'attribute'=> 'Invigilators',
                'format'=> 'raw',
                'value'=> function ($model) {
                    return $this->render('../ex-invigilators/__report/invigilators', ['model'=> $model,'addNew'=> true]);
                }
            ],
            // 'class_room_relId',
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