<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WSeEvaluatorsListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Wse Evaluators Lists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wse-evaluators-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decris', 'Create Wse Evaluators List'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sel_se_relId',
            'sel_evaluator_relId',
            'se_date_started',
            'se_date_finished',
            // 'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
