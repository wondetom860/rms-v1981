<?php

use backend\models\Instructor;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\ExamSchedule\models\ExInvigilatorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Invigilators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-invigilators-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right hidden">
            <?= Html::a(Yii::t('rms', 'Add Invigilators'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Date Time Schedule',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Yii::t('rms', $model->examRoomSlotRel->dateTimeSlotRel->getDateScheduled()), '/ExamSchedule/ex-date-time-schedule/view?id=' . $model->examRoomSlotRel->date_time_slot_relId, []);
                }
            ],
            [
                'attribute' => 'exam_room_slot_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Yii::t('rms', $model->examRoomSlotRel->getHeaderText()), '/ExamSchedule/ex-date-time-schedule/view?id=' . $model->exam_room_slot_relId, []);
                },
                'filter' => ArrayHelper::map(frontend\modules\ExamSchedule\models\ExExamRoomSlot::find()
                        ->all(), 'id', function ($model) {
                    return $model->getHeaderText();
                }),
            ],
            // 'section_relId',
            // 'offering_detail_relId',
            [
                'attribute' => 'staff_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $inst = Instructor::find()->where(['inst_user_relId' => $model->staff_relId])->one();
                    return $inst->getFullName();
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return date('dMY', $model->created_at);
                }
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
