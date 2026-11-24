<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View  $this
 * @var $searchModel frontend\modules\ExamSchedule\models\ExDateTimeScheduleSearch 
 * @var $dataProvider yii\data\ActiveDataProvider 
 */
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->title = Yii::t('rms', 'Exam Date Time Schedules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-date-time-schedule-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Exam Date Time Schedule'), ['#'], ['class' => 'btn btn-success btn-xs', 'onclick' => "ShowCreateNewForm();return false;"]) ?>
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
                'attribute' => 'exam_schedule_entry_relId',
                'value' => function ($model) {
                    return $model->examScheduleEntryRel->getScehduleEntryHeader();
                },
                'filter' => \yii\helpers\ArrayHelper::map(\frontend\modules\ExamSchedule\models\ExExamScheduleEntry::find()->all(), 'id', function ($model) {
                    return $model->getScehduleEntryHeader();
                })
            ],
            'date:date',
            'shift',
            [
                'attribute' => 'start_time',
                'format' => ['datetime', 'php:H:i A']
            ],
            [
                'attribute' => 'end_time',
                'format' => ['datetime', 'php:H:i A']
            ],
            // 'standbys',
            //'supervisors',
            //'created_by',
            //'created_at',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<script>
    window.ShowCreateNewForm = () => {
        // $.ajax({
        //     url: '/ExamSchedule/ex-date-time-schedule/create',
        //     type: 'GET',
        //     success: function(data) {
        //         $('#modal').modal('show');
        //         $('#modal .body').html(data);
        //     }
        // });

        $('#modal').find('.modal-header').html('Add new Exam Date Time Schedule');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-date-time-schedule/create', {}).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>