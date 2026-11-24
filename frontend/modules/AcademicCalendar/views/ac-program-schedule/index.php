<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcademicCalendar\models\AcProgramScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Program Schedules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-program-schedule-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="hidden">
        <?= Html::a(Yii::t('rms', 'Add New Program Schedule'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
                'style'=>'overflow: auto; word-wrap: break-word;',
            ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'academic_year',
                'value' => function($model){
                    return $model->getProgramHeader();
                },
                'format' => 'raw',
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcAcademicYear::find()->orderBy(['id' => SORT_DESC])->all(),'id',function($model){
                    return $model->getAcHeader();
                })
            ],[
                'attribute' => 'class_year',
                'value' => function($model){
                    return $model->getClaaYearHeader();
                },
                'format' => 'raw',
                'filter' => ArrayHelper::map(\backend\models\ClassLevel::find()->orderBy(['id' => SORT_ASC])->all(),'id',function($model){
                    return "Year-".$model->clslvl_num."({$model->clslvlProgramRel->program_name})";
                })
            ],
            [
                'attribute' => 'action_relId',
                'value' => function($model){
                    return $model->actionRel->aca_name;
                },
                'format' => 'raw',
                'filter' => ArrayHelper::map(\backend\models\AcademicCalanderAction::find()->orderBy(['aca_name' => SORT_ASC])->all(),'id',function($model){
                    return $model->aca_name;
                })
            ],
            [
                'attribute' => 'start_date',
                'value' => function($model){
                    return date("d/m/Y h:i A",$model->start_date);
                },
            ],[
                'attribute' => 'end_date',
                'value' => function($model){
                    return date("d/m/Y h:i A",$model->end_date);
                },
            ],[
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getStatus();
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcAcademicYear::find()->groupBy('status')->all(),'status',function($model){
                    return $model->statuses[$model->status];
                })
            ],
            
            //'last_extend',
            //'last_extend_status',
            // 'remark:ntext',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
