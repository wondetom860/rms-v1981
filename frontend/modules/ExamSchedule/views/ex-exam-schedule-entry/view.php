<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View  $this
 * 
 *
 * @var $model frontend\modules\ExamSchedule\models\ExExamScheduleEntry 
 */

$this->title = $model->acadYearRel->getAcHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exam Schedule Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ex-exam-schedule-entry-view">

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
                'attribute' => 'acad_year_relId',
                'value' => function ($model) {
                    return $model->acadYearRel->getAcHeader();
                },
            ],
            'header_text:ntext',
            [
                'attribute' => 'Schedule date',
                'value' => function ($model) {
                    return date('dMY', $model->start_date) . ' to ' . date('dMY', $model->end_date);
                },
            ],
            // 'start_date',
            // 'end_date',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->statusText;
                },
            ],
            [
                'attribute' => 'Date Time Schedule',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('../ex-date-time-schedule/__report/date-time-schedules', [
                        'model' => $model,
                    ]);
                    // return $this->render('/ex-date-time-schedule/index', [
                    //     'searchModel' => new \frontend\modules\ExamSchedule\models\ExDateTimeScheduleSearch(['exam_schedule_entry_relId' => $model->id]),
                    //     'dataProvider' => new \yii\data\ActiveDataProvider([
                    //         'query' => $model->getExDateTimeSchedules(),
                    //     ]),
                    // ]);
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy->username;
                },
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updatedBy ? $model->updatedBy->username : '';
                },
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('dMY', $model->created_at);
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return $model->updated_at ? date('dMY', $model->updated_at) : '';
                },
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script>
    
</script>