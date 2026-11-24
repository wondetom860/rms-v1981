<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcStEva\models\AseSemesterEvaluationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluations');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-semester-evaluation-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Yii::$app->user->can('AcademicDean') || Yii::$app->user->can('Registrar') ? 
        Html::a(Yii::t('rms', 'Create Semester Evaluation Entry'), ['create'], ['class' => 'btn btn-success btn-xs']) : '' ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'academic_year_relId',
                'value' => function($model){
                    return $model->academicYearRel->getAcHeader();
                }
            ],
            'evaluation_header',
            [
                'attribute' => 'start_date',
                'value' => function($model){ return date('d/m/Y',$model->start_date); }
            ],[
                'attribute' => 'end_date',
                'value' => function($model){ return date('d/m/Y',$model->end_date); }
            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getStatus();
                }
            ],
            [
                'attribute' => 'criteria_bundle_IDS',
                'value' => function($model){
                    return $model->criteria_bundle_IDS ? count(json_decode($model->criteria_bundle_IDS)) : '-';
                }
            ],
            // 'status',
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date('d/m/Y',$model->created_at);
                }
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
