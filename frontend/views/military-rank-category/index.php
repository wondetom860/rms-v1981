<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\MilitaryRankCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Military Rank Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="military-rank-category-index">

    <h3 style="border-bottom: 1px solid black"><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Military Rank Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'catagory_name',
            [
                'attribute' => 'created_by',
                'value' => 'createdBy.username',
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date_format(date_create($model->created_at),'d M,Y');
                }
            ],
            // 'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
