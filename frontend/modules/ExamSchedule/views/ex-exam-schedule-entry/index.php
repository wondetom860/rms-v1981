<?php

use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View  $this*/
/* @var $searchModel frontend\modules\ExamSchedule\models\ExExamScheduleEntrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Exam Schedule Entries');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-exam-schedule-entry-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Exam Schedule Entry'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'acad_year_relId',
                'value' => function ($model) {
                    return $model->acadYearRel->getAcHeader();
                },
                'filter' => \yii\helpers\ArrayHelper::map(AcAcademicYear::find()
                    ->orderBy([
                        'school_year' => SORT_DESC,
                        'semester' => SORT_DESC,
                        ])
                    ->all(), 'id', function ($model) {
                    return $model->getAcHeader();
                })
            ],
            [
                'attribute' => 'Schedule date',
                'value' => function ($model) {
                    return date('dMY', $model->start_date) . ' to ' . date('dMY', $model->end_date);
                },
            ],
            // 'end_date',
            'header_text:ntext',
            // 'created_at',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->statusText;
                },
            ],
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>