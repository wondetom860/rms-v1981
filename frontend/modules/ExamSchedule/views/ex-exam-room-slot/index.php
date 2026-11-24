<?php

use frontend\modules\ClassSchedule\models\CsAvailableClassRoom;
use frontend\modules\ExamSchedule\models\ExDateTimeSchedule;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var yii\web\View  $this
 * @var $searchModel frontend\modules\ExamSchedule\models\ExExamRoomSlotSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->title = Yii::t('rms', 'Exam Room Slots');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-exam-room-slot-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right hidden">
            <?= Html::a(Yii::t('rms', 'Create Exam Room Slot'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>

    </h3>

    <?php Pjax::begin(); ?>
    <?php  // echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'Date Time Schedule',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Yii::t('rms', $model->dateTimeSlotRel->getDateScheduled()), '/ExamSchedule/ex-date-time-schedule/view?id=' . $model->date_time_slot_relId, []);
                }
            ],
            [
                'attribute' => 'date_time_slot_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->getHeaderText(), ['ex-date-time-schedule/view', 'id' => $model->date_time_slot_relId]);
                    // return $model->getHeaderText();
                },
                'filter' => \yii\helpers\ArrayHelper::map(ExDateTimeSchedule::find()->all(), 'id', function ($model) {
                    return $model->getHeaderText();
                })
            ],
            [
                'attribute' => 'class_room_relId',
                'value' => function ($model) {
                    return $model->classRoomRel->room;
                },
                'filter' => \yii\helpers\ArrayHelper::map(CsAvailableClassRoom::find()->all(), 'id', 'room')
            ],
            [
                'attribute' => 'offering_detail_IDs',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getCourseCode();
                }
            ],
            // 'class_room_relId',
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