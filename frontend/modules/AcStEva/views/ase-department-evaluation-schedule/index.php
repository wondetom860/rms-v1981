<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcStEva\models\AseDepartmentEvaluationScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Department Evaluation Schedules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = $this->title;

// $model->startdate = date("d/m/Y",$model->start_date);
// $model->enddate = date("d/m/Y",$model->end_date);
?>
<div class="ase-department-evaluation-schedule-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'header_relId',
                'value' => function($model){
                    return $model->headerRel->evaluation_header;
                }
            ],
            [
                'attribute' => 'dept_relId',
                'value' => function($model){
                    return $model->deptRel ? $model->deptRel->dept_name : '-';
                }
            ],
            [
                'attribute' => 'criteria_bundle_relId',
                'format' => 'raw',
                'value' => function($model){
                    // $criteriaCount = count(json_decode($model->criteriaBundleRel->criteria_IDS));
                    // $count_link = "<a href='#' onclick='showEvaluationCriteriaDetail({$model->criteria_bundle_relId})'>{$criteriaCount}</a>";
                    return $model->criteriaBundleRel->code;
                }
            ],
            
            [
                'attribute' => 'start_date',
                'value' => function($model){
                    return date("d/m/Y",$model->start_date);
                }

            ], [
                'attribute' => 'end_date',
                'value' => function($model){
                    return date("d/m/Y",$model->end_date);
                }

            ],
            'finalized',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getStatus();
                }
            ],
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
