<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\Depal\models\DepalSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Setting Entries');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-setting-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Create New Setting'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'section_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->sectionRel->getClassLevelHeader();
                }
            ],
            'depts_count',
            [
                'attribute' => 'category_IDS',
                'format' => 'raw',
                'value' => function($model){ return $model->stringifyCategories(); }
            ],[
                'attribute' => 'elligible_depts',
                'format' => 'raw',
                'value' => function($model){ return $model->stringifyDepartments(); }
            ],
            [
                'attribute' => 'start_date',
                'value' => function($model){ return date("d/m/Y",$model->start_date); }
            ],[
                'attribute' => 'end_date',
                'value' => function($model){ return date("d/m/Y",$model->end_date); }
            ],
            [
                'attribute' => 'status',
                'value' => function($model){ return $model->getScheduleStatus(true); }
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
