<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\MilitaryRankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Military Ranks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="military-rank-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Military Rank'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'mr_category_relId',
                'value' => 'mrCategoryRel.catagory_name',
                'filter' => ArrayHelper::map(\backend\models\MilitaryRank::find()->groupBy('mr_category_relId')->all(),'mr_category_relId',function($model){
                    return $model->mrCategoryRel->catagory_name;
                })
            ],
            'mr_accronym',
            'mr_rank_name',
            'mr_order_count',
            'mr_duration_month',
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
