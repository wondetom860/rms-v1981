<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcStEva\models\AseCriteriaBundleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Type-Criteria Bundles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-criteria-bundle-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'code',
            [
                'attribute' => 'eval_type_relId',
                'value' => "evalTypeRel.type_name",
            ],
            'evaluator_role',
            'candidate_staff_role',
            'average_point',
            'average_percentage',
            [
                'attribute' => 'evaluate_per_course',
                'value' => function($model){
                    return $model->evaluate_per_course ? "YES" : "NO";
                }
            ],
            [
                'attribute' => 'criteria_IDS',
                'format' => 'raw',
                'value' => function($model){
                    return count(json_decode($model->criteria_IDS))." entries";
                }
            ],
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
