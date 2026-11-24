<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\Depal\models\DepalCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Criteria Categories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-category-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Register new criteria'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'category_name',
            'comparison_type',
            'attribute',
            [
                'attribute' => 'lower_bound',
                'value' => function($model){ return is_null($model->upper_bound) ? '-':sprintf("%0.3f",$model->lower_bound); }
            ],[
                'attribute' => 'upper_bound',
                'value' => function($model){ return is_null($model->upper_bound) ? '-':sprintf("%0.3f",$model->upper_bound); }
            ],
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
